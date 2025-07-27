<?php
/**
 * SuiteCRM Lead Attribution Center - Controller
 * 
 * This controller handles special actions for the Lead Attribution module including:
 * - UTM parameter capture from web forms
 * - Google Analytics 4 API integration
 * - Facebook Lead Ads webhook processing
 * - ROI dashboard and reporting views
 * - Attribution analysis and calculations
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/Controller/SugarController.php');

class DM_LeadAttributionController extends SugarController
{
    /**
     * Handle UTM parameter capture from lead forms
     */
    public function action_captureUTM()
    {
        $GLOBALS['log']->info("DM_LeadAttribution: Processing UTM capture request");
        
        try {
            $leadId = $_REQUEST['lead_id'] ?? '';
            $utmData = array();
            
            // Extract UTM parameters
            $utmFields = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term');
            foreach ($utmFields as $field) {
                if (isset($_REQUEST[$field])) {
                    $utmData[$field] = $_REQUEST[$field];
                }
            }
            
            // Capture referrer and landing page
            $utmData['referrer_url'] = $_SERVER['HTTP_REFERER'] ?? '';
            $utmData['landing_page'] = $_SERVER['REQUEST_URI'] ?? '';
            
            // Create or update attribution record
            $attribution = DM_LeadAttribution::captureLeadAttribution($leadId, $utmData);
            
            if ($attribution) {
                $response = array(
                    'success' => true,
                    'attribution_id' => $attribution->id,
                    'message' => 'UTM parameters captured successfully'
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to capture UTM parameters'
                );
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_LeadAttribution: UTM capture error: " . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Error capturing UTM parameters: ' . $e->getMessage()
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    /**
     * Handle Google Analytics 4 webhook/integration
     */
    public function action_googleAnalyticsWebhook()
    {
        $GLOBALS['log']->info("DM_LeadAttribution: Processing Google Analytics webhook");
        
        try {
            // Get JSON payload
            $input = file_get_contents('php://input');
            $gaData = json_decode($input, true);
            
            if (empty($gaData)) {
                throw new Exception('No Google Analytics data received');
            }
            
            // Extract lead identifier (could be email, client_id, etc.)
            $leadId = $gaData['lead_id'] ?? '';
            $clientId = $gaData['client_id'] ?? '';
            
            if (empty($leadId) && !empty($clientId)) {
                // Try to find lead by GA client ID
                $lead = $this->findLeadByGAClientId($clientId);
                $leadId = $lead ? $lead->id : '';
            }
            
            if (!empty($leadId)) {
                // Create or update attribution record
                $attribution = BeanFactory::newBean('DM_LeadAttribution');
                $attribution->retrieve_by_string_fields(array('lead_id' => $leadId));
                
                if (empty($attribution->id)) {
                    $attribution = BeanFactory::newBean('DM_LeadAttribution');
                    $attribution->lead_id = $leadId;
                }
                
                // Process GA4 data
                $attribution->processGoogleAnalyticsData($gaData);
                $attribution->save();
                
                $response = array(
                    'success' => true,
                    'attribution_id' => $attribution->id,
                    'message' => 'Google Analytics data processed successfully'
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'No lead found for Google Analytics data'
                );
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_LeadAttribution: GA webhook error: " . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Error processing Google Analytics data: ' . $e->getMessage()
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    /**
     * Handle Facebook Lead Ads webhook
     */
    public function action_facebookWebhook()
    {
        $GLOBALS['log']->info("DM_LeadAttribution: Processing Facebook webhook");
        
        try {
            // Verify webhook (Facebook requirement)
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $this->verifyFacebookWebhook();
                return;
            }
            
            // Get JSON payload
            $input = file_get_contents('php://input');
            $fbData = json_decode($input, true);
            
            if (empty($fbData)) {
                throw new Exception('No Facebook data received');
            }
            
            // Process Facebook Lead Ads data
            if (isset($fbData['entry'])) {
                foreach ($fbData['entry'] as $entry) {
                    if (isset($entry['changes'])) {
                        foreach ($entry['changes'] as $change) {
                            if ($change['field'] === 'leadgen') {
                                $this->processFacebookLead($change['value']);
                            }
                        }
                    }
                }
            }
            
            $response = array('success' => true);
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_LeadAttribution: Facebook webhook error: " . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Error processing Facebook webhook: ' . $e->getMessage()
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    /**
     * ROI Dashboard view
     */
    public function action_ROIDashboard()
    {
        $GLOBALS['log']->info("DM_LeadAttribution: Loading ROI Dashboard");
        
        // Get attribution data for dashboard
        $attributionBean = BeanFactory::newBean('DM_LeadAttribution');
        
        // Calculate ROI metrics
        $roiMetrics = $this->calculateROIMetrics();
        $channelPerformance = $this->getChannelPerformance();
        $campaignROI = $this->getCampaignROI();
        
        // Assign data to view
        $this->view_object_map['roiMetrics'] = $roiMetrics;
        $this->view_object_map['channelPerformance'] = $channelPerformance;
        $this->view_object_map['campaignROI'] = $campaignROI;
        
        // Set view template
        $this->view = 'roi_dashboard';
    }
    
    /**
     * Attribution Report view
     */
    public function action_AttributionReport()
    {
        $GLOBALS['log']->info("DM_LeadAttribution: Loading Attribution Report");
        
        // Get filter parameters
        $dateFrom = $_REQUEST['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
        $dateTo = $_REQUEST['date_to'] ?? date('Y-m-d');
        $source = $_REQUEST['source'] ?? '';
        $campaign = $_REQUEST['campaign'] ?? '';
        
        // Generate attribution report data
        $reportData = $this->generateAttributionReport($dateFrom, $dateTo, $source, $campaign);
        
        // Assign data to view
        $this->view_object_map['reportData'] = $reportData;
        $this->view_object_map['filters'] = array(
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'source' => $source,
            'campaign' => $campaign
        );
        
        // Set view template
        $this->view = 'attribution_report';
    }
    
    /**
     * Find lead by Google Analytics client ID
     */
    private function findLeadByGAClientId($clientId)
    {
        $attribution = BeanFactory::newBean('DM_LeadAttribution');
        $attribution->retrieve_by_string_fields(array('ga_client_id' => $clientId));
        
        if (!empty($attribution->lead_id)) {
            return BeanFactory::getBean('Leads', $attribution->lead_id);
        }
        
        return null;
    }
    
    /**
     * Verify Facebook webhook challenge
     */
    private function verifyFacebookWebhook()
    {
        $verifyToken = $GLOBALS['sugar_config']['facebook_verify_token'] ?? 'your_verify_token';
        $hubVerifyToken = $_GET['hub_verify_token'] ?? '';
        $hubChallenge = $_GET['hub_challenge'] ?? '';
        
        if ($hubVerifyToken === $verifyToken) {
            echo $hubChallenge;
        } else {
            echo 'Invalid verify token';
        }
        exit;
    }
    
    /**
     * Process Facebook Lead Ads lead data
     */
    private function processFacebookLead($leadData)
    {
        $leadId = $leadData['leadgen_id'] ?? '';
        $formId = $leadData['form_id'] ?? '';
        $adId = $leadData['ad_id'] ?? '';
        
        // Create attribution record
        $attribution = BeanFactory::newBean('DM_LeadAttribution');
        $attribution->fb_lead_id = $leadId;
        $attribution->fb_form_id = $formId;
        $attribution->fb_ad_id = $adId;
        
        // Process Facebook data
        $attribution->processFacebookLeadAdsData($leadData);
        $attribution->save();
        
        $GLOBALS['log']->info("DM_LeadAttribution: Processed Facebook lead: $leadId");
    }
    
    /**
     * Calculate ROI metrics for dashboard
     */
    private function calculateROIMetrics()
    {
        global $db;
        
        $sql = "SELECT 
                    COUNT(*) as total_leads,
                    SUM(conversion_value) as total_revenue,
                    SUM(total_cost) as total_cost,
                    AVG(roi_percentage) as avg_roi,
                    COUNT(CASE WHEN conversion_value > 0 THEN 1 END) as converted_leads
                FROM dm_leadattribution 
                WHERE deleted = 0";
        
        $result = $db->query($sql);
        $row = $db->fetchByAssoc($result);
        
        return array(
            'total_leads' => $row['total_leads'] ?? 0,
            'total_revenue' => $row['total_revenue'] ?? 0,
            'total_cost' => $row['total_cost'] ?? 0,
            'avg_roi' => $row['avg_roi'] ?? 0,
            'converted_leads' => $row['converted_leads'] ?? 0,
            'conversion_rate' => $row['total_leads'] > 0 ? ($row['converted_leads'] / $row['total_leads']) * 100 : 0
        );
    }
    
    /**
     * Get channel performance data
     */
    private function getChannelPerformance()
    {
        global $db;
        
        $sql = "SELECT 
                    first_touch_source as channel,
                    COUNT(*) as leads,
                    SUM(conversion_value) as revenue,
                    SUM(total_cost) as cost,
                    AVG(roi_percentage) as avg_roi
                FROM dm_leadattribution 
                WHERE deleted = 0 AND first_touch_source IS NOT NULL
                GROUP BY first_touch_source
                ORDER BY revenue DESC";
        
        $result = $db->query($sql);
        $channels = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $channels[] = $row;
        }
        
        return $channels;
    }
    
    /**
     * Get campaign ROI data
     */
    private function getCampaignROI()
    {
        global $db;
        
        $sql = "SELECT 
                    first_touch_campaign as campaign,
                    COUNT(*) as leads,
                    SUM(conversion_value) as revenue,
                    SUM(total_cost) as cost,
                    AVG(roi_percentage) as avg_roi
                FROM dm_leadattribution 
                WHERE deleted = 0 AND first_touch_campaign IS NOT NULL
                GROUP BY first_touch_campaign
                ORDER BY avg_roi DESC
                LIMIT 10";
        
        $result = $db->query($sql);
        $campaigns = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $campaigns[] = $row;
        }
        
        return $campaigns;
    }
    
    /**
     * Generate attribution report data
     */
    private function generateAttributionReport($dateFrom, $dateTo, $source = '', $campaign = '')
    {
        global $db;
        
        $whereClause = "WHERE deleted = 0 AND first_touch_date >= '$dateFrom' AND first_touch_date <= '$dateTo'";
        
        if (!empty($source)) {
            $whereClause .= " AND first_touch_source = '" . $db->quote($source) . "'";
        }
        
        if (!empty($campaign)) {
            $whereClause .= " AND first_touch_campaign = '" . $db->quote($campaign) . "'";
        }
        
        $sql = "SELECT 
                    id, name, lead_id, first_touch_source, first_touch_medium, 
                    first_touch_campaign, first_touch_date, conversion_value, 
                    total_cost, roi_percentage, touch_count
                FROM dm_leadattribution 
                $whereClause
                ORDER BY first_touch_date DESC";
        
        $result = $db->query($sql);
        $reportData = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $reportData[] = $row;
        }
        
        return $reportData;
    }
}