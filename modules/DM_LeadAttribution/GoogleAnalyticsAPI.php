<?php
/**
 * SuiteCRM Lead Attribution Center - Google Analytics 4 API Integration
 * 
 * This class handles Google Analytics 4 API integration for lead attribution tracking
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class GoogleAnalyticsAPI
{
    private $propertyId;
    private $credentials;
    private $accessToken;
    
    /**
     * Constructor
     * 
     * @param string $propertyId GA4 Property ID
     * @param array $credentials Service account credentials
     */
    public function __construct($propertyId = null, $credentials = null)
    {
        $this->propertyId = $propertyId ?: $GLOBALS['sugar_config']['ga4_property_id'] ?? '';
        $this->credentials = $credentials ?: $GLOBALS['sugar_config']['ga4_credentials'] ?? array();
        
        $GLOBALS['log']->info("GoogleAnalyticsAPI: Initializing with property ID: " . $this->propertyId);
    }
    
    /**
     * Get access token for GA4 API
     */
    private function getAccessToken()
    {
        if ($this->accessToken && $this->isTokenValid()) {
            return $this->accessToken;
        }
        
        // Use service account credentials to get access token
        $jwt = $this->createJWT();
        
        $postData = array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $tokenData = json_decode($response, true);
            $this->accessToken = $tokenData['access_token'] ?? '';
            
            $GLOBALS['log']->info("GoogleAnalyticsAPI: Access token obtained successfully");
            return $this->accessToken;
        } else {
            $GLOBALS['log']->error("GoogleAnalyticsAPI: Failed to get access token. HTTP Code: $httpCode, Response: $response");
            return false;
        }
    }
    
    /**
     * Create JWT for service account authentication
     */
    private function createJWT()
    {
        $header = json_encode(array(
            'alg' => 'RS256',
            'typ' => 'JWT'
        ));
        
        $now = time();
        $payload = json_encode(array(
            'iss' => $this->credentials['client_email'] ?? '',
            'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ));
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = '';
        if (isset($this->credentials['private_key'])) {
            $privateKey = $this->credentials['private_key'];
            openssl_sign($base64Header . '.' . $base64Payload, $signature, $privateKey, 'SHA256');
            $signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        }
        
        return $base64Header . '.' . $base64Payload . '.' . $signature;
    }
    
    /**
     * Check if current access token is valid
     */
    private function isTokenValid()
    {
        // Simple validation - in production, you'd want to store token expiry
        return !empty($this->accessToken);
    }
    
    /**
     * Get attribution data for a specific client ID
     * 
     * @param string $clientId GA4 Client ID
     * @param int $days Number of days to look back
     * @return array Attribution data
     */
    public function getAttributionData($clientId, $days = 30)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return array();
        }
        
        $startDate = date('Y-m-d', strtotime("-$days days"));
        $endDate = date('Y-m-d');
        
        $requestBody = array(
            'requests' => array(
                array(
                    'entity' => array(
                        'propertyId' => $this->propertyId
                    ),
                    'dateRanges' => array(
                        array(
                            'startDate' => $startDate,
                            'endDate' => $endDate
                        )
                    ),
                    'dimensions' => array(
                        array('name' => 'firstUserSource'),
                        array('name' => 'firstUserMedium'),
                        array('name' => 'firstUserCampaignName'),
                        array('name' => 'sessionSource'),
                        array('name' => 'sessionMedium'),
                        array('name' => 'sessionCampaignName'),
                        array('name' => 'date')
                    ),
                    'metrics' => array(
                        array('name' => 'sessions'),
                        array('name' => 'totalUsers'),
                        array('name' => 'conversions')
                    ),
                    'dimensionFilter' => array(
                        'filter' => array(
                            'fieldName' => 'clientId',
                            'stringFilter' => array(
                                'value' => $clientId,
                                'matchType' => 'EXACT'
                            )
                        )
                    )
                )
            )
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://analyticsdata.googleapis.com/v1beta/properties/' . $this->propertyId . ':batchRunReports');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return $this->parseAttributionData($data);
        } else {
            $GLOBALS['log']->error("GoogleAnalyticsAPI: Failed to get attribution data. HTTP Code: $httpCode, Response: $response");
            return array();
        }
    }
    
    /**
     * Parse GA4 API response into attribution data
     */
    private function parseAttributionData($apiResponse)
    {
        $attributionData = array();
        
        if (isset($apiResponse['reports'][0]['rows'])) {
            foreach ($apiResponse['reports'][0]['rows'] as $row) {
                $dimensions = $row['dimensionValues'] ?? array();
                $metrics = $row['metricValues'] ?? array();
                
                $touchpoint = array(
                    'first_source' => $dimensions[0]['value'] ?? '',
                    'first_medium' => $dimensions[1]['value'] ?? '',
                    'first_campaign' => $dimensions[2]['value'] ?? '',
                    'session_source' => $dimensions[3]['value'] ?? '',
                    'session_medium' => $dimensions[4]['value'] ?? '',
                    'session_campaign' => $dimensions[5]['value'] ?? '',
                    'date' => $dimensions[6]['value'] ?? '',
                    'sessions' => $metrics[0]['value'] ?? 0,
                    'users' => $metrics[1]['value'] ?? 0,
                    'conversions' => $metrics[2]['value'] ?? 0
                );
                
                $attributionData[] = $touchpoint;
            }
        }
        
        $GLOBALS['log']->info("GoogleAnalyticsAPI: Parsed " . count($attributionData) . " touchpoints");
        
        return $attributionData;
    }
    
    /**
     * Get real-time attribution data
     * 
     * @param string $clientId GA4 Client ID
     * @return array Real-time attribution data
     */
    public function getRealTimeAttribution($clientId)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return array();
        }
        
        $requestBody = array(
            'dimensions' => array(
                array('name' => 'source'),
                array('name' => 'medium'),
                array('name' => 'campaignName')
            ),
            'metrics' => array(
                array('name' => 'activeUsers')
            ),
            'dimensionFilter' => array(
                'filter' => array(
                    'fieldName' => 'clientId',
                    'stringFilter' => array(
                        'value' => $clientId,
                        'matchType' => 'EXACT'
                    )
                )
            )
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://analyticsdata.googleapis.com/v1beta/properties/' . $this->propertyId . ':runRealtimeReport');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return $this->parseRealTimeData($data);
        } else {
            $GLOBALS['log']->error("GoogleAnalyticsAPI: Failed to get real-time data. HTTP Code: $httpCode");
            return array();
        }
    }
    
    /**
     * Parse real-time API response
     */
    private function parseRealTimeData($apiResponse)
    {
        $realTimeData = array();
        
        if (isset($apiResponse['rows'][0])) {
            $row = $apiResponse['rows'][0];
            $dimensions = $row['dimensionValues'] ?? array();
            
            $realTimeData = array(
                'source' => $dimensions[0]['value'] ?? '',
                'medium' => $dimensions[1]['value'] ?? '',
                'campaign' => $dimensions[2]['value'] ?? '',
                'timestamp' => date('Y-m-d H:i:s'),
                'active' => true
            );
        }
        
        return $realTimeData;
    }
    
    /**
     * Track custom event in GA4
     * 
     * @param string $eventName Event name
     * @param array $parameters Event parameters
     * @param string $clientId GA4 Client ID
     */
    public function trackEvent($eventName, $parameters = array(), $clientId = '')
    {
        // This would typically use the Measurement Protocol for GA4
        // For now, we'll log the event for tracking purposes
        $GLOBALS['log']->info("GoogleAnalyticsAPI: Tracking event '$eventName' for client '$clientId'");
        
        // In a real implementation, you would send data to GA4 Measurement Protocol
        // https://developers.google.com/analytics/devguides/collection/protocol/ga4
        
        return true;
    }
    
    /**
     * Validate GA4 configuration
     */
    public function validateConfiguration()
    {
        $errors = array();
        
        if (empty($this->propertyId)) {
            $errors[] = 'GA4 Property ID is not configured';
        }
        
        if (empty($this->credentials['client_email'])) {
            $errors[] = 'GA4 service account email is not configured';
        }
        
        if (empty($this->credentials['private_key'])) {
            $errors[] = 'GA4 service account private key is not configured';
        }
        
        // Test API connection
        if (empty($errors)) {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                $errors[] = 'Unable to obtain GA4 API access token';
            }
        }
        
        return $errors;
    }
    
    /**
     * Get channel grouping for attribution
     */
    public function getChannelGrouping($source, $medium)
    {
        $source = strtolower($source);
        $medium = strtolower($medium);
        
        // Define channel grouping rules (simplified version of GA4 default channel grouping)
        if ($medium === 'organic') {
            return 'Organic Search';
        }
        
        if ($medium === 'cpc' || $medium === 'ppc') {
            return 'Paid Search';
        }
        
        if (in_array($source, array('facebook', 'instagram', 'twitter', 'linkedin', 'youtube'))) {
            if ($medium === 'cpc' || $medium === 'ppc') {
                return 'Paid Social';
            } else {
                return 'Organic Social';
            }
        }
        
        if ($medium === 'email') {
            return 'Email';
        }
        
        if ($medium === 'referral') {
            return 'Referral';
        }
        
        if ($medium === 'direct' || $source === 'direct') {
            return 'Direct';
        }
        
        if ($medium === 'display') {
            return 'Display';
        }
        
        return 'Other';
    }
}