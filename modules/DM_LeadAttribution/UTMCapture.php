<?php
/**
 * SuiteCRM Lead Attribution Center - UTM Parameter Capture Utility
 * 
 * This class handles UTM parameter capture and processing for lead attribution
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class UTMCapture
{
    private $sessionKey = 'suitecrm_utm_data';
    
    /**
     * Capture UTM parameters from request
     * 
     * @param array $requestData Request data (GET/POST)
     * @return array Captured UTM data
     */
    public function captureFromRequest($requestData = null)
    {
        if ($requestData === null) {
            $requestData = array_merge($_GET, $_POST);
        }
        
        $utmData = array();
        $utmFields = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term');
        
        // Capture UTM parameters
        foreach ($utmFields as $field) {
            if (isset($requestData[$field]) && !empty($requestData[$field])) {
                $utmData[$field] = $this->sanitizeUTMValue($requestData[$field]);
            }
        }
        
        // Capture additional tracking data
        $utmData['referrer_url'] = $_SERVER['HTTP_REFERER'] ?? '';
        $utmData['landing_page'] = $_SERVER['REQUEST_URI'] ?? '';
        $utmData['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $utmData['ip_address'] = $this->getClientIP();
        $utmData['timestamp'] = date('Y-m-d H:i:s');
        
        // Store in session for persistence
        $this->storeInSession($utmData);
        
        $GLOBALS['log']->info("UTMCapture: Captured UTM data - " . json_encode($utmData));
        
        return $utmData;
    }
    
    /**
     * Sanitize UTM parameter value
     */
    private function sanitizeUTMValue($value)
    {
        // Remove dangerous characters and limit length
        $value = strip_tags($value);
        $value = preg_replace('/[^\w\-\.\_\s]/', '', $value);
        $value = substr($value, 0, 255);
        
        return trim($value);
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP()
    {
        $ipKeys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
                       'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Store UTM data in session
     */
    private function storeInSession($utmData)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Keep history of UTM data for journey tracking
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = array();
        }
        
        $_SESSION[$this->sessionKey][] = $utmData;
        
        // Limit session history to prevent memory issues
        if (count($_SESSION[$this->sessionKey]) > 10) {
            $_SESSION[$this->sessionKey] = array_slice($_SESSION[$this->sessionKey], -10);
        }
    }
    
    /**
     * Get UTM data from session
     * 
     * @param bool $getAll Get all history or just latest
     * @return array UTM data
     */
    public function getFromSession($getAll = false)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionData = $_SESSION[$this->sessionKey] ?? array();
        
        if ($getAll) {
            return $sessionData;
        }
        
        return end($sessionData) ?: array();
    }
    
    /**
     * Clear UTM data from session
     */
    public function clearSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION[$this->sessionKey]);
    }
    
    /**
     * Parse UTM parameters from URL
     * 
     * @param string $url URL to parse
     * @return array UTM parameters
     */
    public function parseFromURL($url)
    {
        $utmData = array();
        $parsedUrl = parse_url($url);
        
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $params);
            
            $utmFields = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term');
            foreach ($utmFields as $field) {
                if (isset($params[$field])) {
                    $utmData[$field] = $this->sanitizeUTMValue($params[$field]);
                }
            }
        }
        
        return $utmData;
    }
    
    /**
     * Generate UTM URL
     * 
     * @param string $baseUrl Base URL
     * @param array $utmParams UTM parameters
     * @return string URL with UTM parameters
     */
    public function generateUTMURL($baseUrl, $utmParams)
    {
        $url = $baseUrl;
        $queryParams = array();
        
        $utmFields = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term');
        foreach ($utmFields as $field) {
            if (!empty($utmParams[$field])) {
                $queryParams[$field] = urlencode($utmParams[$field]);
            }
        }
        
        if (!empty($queryParams)) {
            $separator = strpos($url, '?') !== false ? '&' : '?';
            $url .= $separator . http_build_query($queryParams);
        }
        
        return $url;
    }
    
    /**
     * Track form submission with UTM data
     * 
     * @param string $formName Form identifier
     * @param array $formData Form submission data
     * @return bool Success status
     */
    public function trackFormSubmission($formName, $formData = array())
    {
        $utmData = $this->getFromSession();
        
        if (empty($utmData)) {
            // No UTM data available
            return false;
        }
        
        // Combine UTM data with form data
        $trackingData = array_merge($utmData, array(
            'form_name' => $formName,
            'form_data' => $formData,
            'submission_time' => date('Y-m-d H:i:s')
        ));
        
        // Log the form submission
        $GLOBALS['log']->info("UTMCapture: Form submission tracked - $formName");
        
        // Store in database for reporting
        $this->storeFormSubmission($trackingData);
        
        return true;
    }
    
    /**
     * Store form submission data
     */
    private function storeFormSubmission($trackingData)
    {
        global $db;
        
        // Create a simple tracking table entry
        $sql = "INSERT INTO utm_form_submissions 
                (form_name, utm_source, utm_medium, utm_campaign, utm_content, utm_term, 
                 referrer_url, landing_page, ip_address, submission_time, tracking_data)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = array(
            $trackingData['form_name'] ?? '',
            $trackingData['utm_source'] ?? '',
            $trackingData['utm_medium'] ?? '',
            $trackingData['utm_campaign'] ?? '',
            $trackingData['utm_content'] ?? '',
            $trackingData['utm_term'] ?? '',
            $trackingData['referrer_url'] ?? '',
            $trackingData['landing_page'] ?? '',
            $trackingData['ip_address'] ?? '',
            $trackingData['submission_time'] ?? date('Y-m-d H:i:s'),
            json_encode($trackingData)
        );
        
        try {
            $stmt = $db->getConnection()->prepare($sql);
            $stmt->execute($params);
        } catch (Exception $e) {
            $GLOBALS['log']->error("UTMCapture: Failed to store form submission - " . $e->getMessage());
        }
    }
    
    /**
     * Get attribution model data
     * 
     * @param string $model Attribution model (first-touch, last-touch, linear)
     * @return array Attribution data
     */
    public function getAttributionModel($model = 'last-touch')
    {
        $sessionHistory = $this->getFromSession(true);
        
        if (empty($sessionHistory)) {
            return array();
        }
        
        switch ($model) {
            case 'first-touch':
                return $this->getFirstTouchAttribution($sessionHistory);
                
            case 'last-touch':
                return $this->getLastTouchAttribution($sessionHistory);
                
            case 'linear':
                return $this->getLinearAttribution($sessionHistory);
                
            default:
                return $this->getLastTouchAttribution($sessionHistory);
        }
    }
    
    /**
     * Get first-touch attribution
     */
    private function getFirstTouchAttribution($history)
    {
        $firstTouch = reset($history);
        
        return array(
            'model' => 'first-touch',
            'source' => $firstTouch['utm_source'] ?? '',
            'medium' => $firstTouch['utm_medium'] ?? '',
            'campaign' => $firstTouch['utm_campaign'] ?? '',
            'content' => $firstTouch['utm_content'] ?? '',
            'term' => $firstTouch['utm_term'] ?? '',
            'timestamp' => $firstTouch['timestamp'] ?? '',
            'credit' => 1.0
        );
    }
    
    /**
     * Get last-touch attribution
     */
    private function getLastTouchAttribution($history)
    {
        $lastTouch = end($history);
        
        return array(
            'model' => 'last-touch',
            'source' => $lastTouch['utm_source'] ?? '',
            'medium' => $lastTouch['utm_medium'] ?? '',
            'campaign' => $lastTouch['utm_campaign'] ?? '',
            'content' => $lastTouch['utm_content'] ?? '',
            'term' => $lastTouch['utm_term'] ?? '',
            'timestamp' => $lastTouch['timestamp'] ?? '',
            'credit' => 1.0
        );
    }
    
    /**
     * Get linear attribution (equal credit to all touchpoints)
     */
    private function getLinearAttribution($history)
    {
        $touchpointCount = count($history);
        $creditPerTouch = 1.0 / $touchpointCount;
        
        $attribution = array();
        foreach ($history as $touchpoint) {
            $attribution[] = array(
                'model' => 'linear',
                'source' => $touchpoint['utm_source'] ?? '',
                'medium' => $touchpoint['utm_medium'] ?? '',
                'campaign' => $touchpoint['utm_campaign'] ?? '',
                'content' => $touchpoint['utm_content'] ?? '',
                'term' => $touchpoint['utm_term'] ?? '',
                'timestamp' => $touchpoint['timestamp'] ?? '',
                'credit' => $creditPerTouch
            );
        }
        
        return $attribution;
    }
    
    /**
     * Validate UTM parameters
     * 
     * @param array $utmData UTM data to validate
     * @return array Validation errors
     */
    public function validateUTMData($utmData)
    {
        $errors = array();
        
        // Check required parameters
        if (empty($utmData['utm_source'])) {
            $errors[] = 'UTM source is required';
        }
        
        if (empty($utmData['utm_medium'])) {
            $errors[] = 'UTM medium is required';
        }
        
        // Validate parameter values
        $validMediums = array('cpc', 'organic', 'social', 'email', 'referral', 'direct', 'display', 'affiliate');
        if (!empty($utmData['utm_medium']) && !in_array(strtolower($utmData['utm_medium']), $validMediums)) {
            $errors[] = 'Invalid UTM medium value';
        }
        
        // Check parameter lengths
        $maxLengths = array(
            'utm_source' => 100,
            'utm_medium' => 100,
            'utm_campaign' => 255,
            'utm_content' => 255,
            'utm_term' => 255
        );
        
        foreach ($maxLengths as $field => $maxLength) {
            if (!empty($utmData[$field]) && strlen($utmData[$field]) > $maxLength) {
                $errors[] = "UTM $field exceeds maximum length of $maxLength characters";
            }
        }
        
        return $errors;
    }
    
    /**
     * Create attribution record from UTM data
     * 
     * @param string $leadId Lead ID
     * @param array $utmData UTM data
     * @return DM_LeadAttribution|false Attribution record or false on failure
     */
    public function createAttributionRecord($leadId, $utmData = null)
    {
        if (empty($leadId)) {
            return false;
        }
        
        if ($utmData === null) {
            $utmData = $this->getFromSession();
        }
        
        if (empty($utmData)) {
            return false;
        }
        
        // Use the static method from the bean class
        return DM_LeadAttribution::captureLeadAttribution($leadId, $utmData);
    }
}