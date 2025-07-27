<?php
/**
 * SuiteCRM Lead Attribution Center - Logic Hook for Lead Integration
 * 
 * This logic hook automatically captures attribution data when leads are created or modified
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('modules/DM_LeadAttribution/UTMCapture.php');
require_once('modules/DM_LeadAttribution/GoogleAnalyticsAPI.php');

class LeadAttributionLogicHook
{
    /**
     * After save hook for Leads - capture attribution data
     * 
     * @param SugarBean $bean Lead bean
     * @param string $event Event type
     * @param array $arguments Event arguments
     */
    public function afterSaveLead($bean, $event, $arguments)
    {
        $GLOBALS['log']->info("LeadAttributionLogicHook: Processing lead save for ID: " . $bean->id);
        
        try {
            // Only process on new leads or when specific fields change
            if (!$this->shouldProcessLead($bean, $arguments)) {
                return;
            }
            
            // Initialize UTM capture
            $utmCapture = new UTMCapture();
            
            // Try to get UTM data from various sources
            $utmData = $this->gatherAttributionData($bean, $utmCapture);
            
            if (!empty($utmData)) {
                // Create or update attribution record
                $this->createAttributionRecord($bean->id, $utmData);
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("LeadAttributionLogicHook: Error processing lead attribution - " . $e->getMessage());
        }
    }
    
    /**
     * Determine if we should process this lead for attribution
     */
    private function shouldProcessLead($bean, $arguments)
    {
        // Always process new leads
        if (!empty($arguments['isUpdate']) && $arguments['isUpdate'] === false) {
            return true;
        }
        
        // For existing leads, only process if lead source changed or attribution fields were added
        if (!empty($arguments['isUpdate']) && $arguments['isUpdate'] === true) {
            $dataChanges = $arguments['dataChanges'] ?? array();
            
            // Check if lead source or related fields changed
            $attributionFields = array('lead_source', 'refered_by', 'lead_source_description');
            foreach ($attributionFields as $field) {
                if (isset($dataChanges[$field])) {
                    return true;
                }
            }
            
            // Check if UTM parameters were added to the request
            $utmFields = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term');
            foreach ($utmFields as $field) {
                if (isset($_REQUEST[$field]) && !empty($_REQUEST[$field])) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Gather attribution data from various sources
     */
    private function gatherAttributionData($bean, $utmCapture)
    {
        $attributionData = array();
        
        // 1. Try to get UTM data from session
        $sessionUTM = $utmCapture->getFromSession();
        if (!empty($sessionUTM)) {
            $attributionData = array_merge($attributionData, $sessionUTM);
            $GLOBALS['log']->info("LeadAttributionLogicHook: Found UTM data in session");
        }
        
        // 2. Try to get UTM data from current request
        $requestUTM = $utmCapture->captureFromRequest($_REQUEST);
        if (!empty($requestUTM)) {
            $attributionData = array_merge($attributionData, $requestUTM);
            $GLOBALS['log']->info("LeadAttributionLogicHook: Captured UTM data from request");
        }
        
        // 3. Try to get data from lead source mapping
        $leadSourceData = $this->mapLeadSourceToAttribution($bean);
        if (!empty($leadSourceData)) {
            $attributionData = array_merge($attributionData, $leadSourceData);
            $GLOBALS['log']->info("LeadAttributionLogicHook: Mapped lead source to attribution data");
        }
        
        // 4. Try to get Google Analytics data if client ID is available
        $gaData = $this->getGoogleAnalyticsData($bean);
        if (!empty($gaData)) {
            $attributionData = array_merge($attributionData, $gaData);
            $GLOBALS['log']->info("LeadAttributionLogicHook: Retrieved Google Analytics data");
        }
        
        // 5. Add lead-specific information
        $attributionData['lead_id'] = $bean->id;
        $attributionData['lead_email'] = $bean->email1 ?? '';
        $attributionData['lead_name'] = $bean->first_name . ' ' . $bean->last_name;
        $attributionData['lead_source_original'] = $bean->lead_source ?? '';
        
        return $attributionData;
    }
    
    /**
     * Map SuiteCRM lead source to attribution data
     */
    private function mapLeadSourceToAttribution($bean)
    {
        $leadSource = strtolower($bean->lead_source ?? '');
        $attributionData = array();
        
        // Define mapping from lead source to UTM-style data
        $sourceMapping = array(
            'web site' => array(
                'utm_source' => 'website',
                'utm_medium' => 'organic',
                'first_touch_source' => 'website',
                'first_touch_medium' => 'organic'
            ),
            'google' => array(
                'utm_source' => 'google',
                'utm_medium' => 'organic',
                'first_touch_source' => 'google',
                'first_touch_medium' => 'organic'
            ),
            'email' => array(
                'utm_source' => 'email',
                'utm_medium' => 'email',
                'first_touch_source' => 'email',
                'first_touch_medium' => 'email'
            ),
            'social media' => array(
                'utm_source' => 'social',
                'utm_medium' => 'social',
                'first_touch_source' => 'social',
                'first_touch_medium' => 'social'
            ),
            'facebook' => array(
                'utm_source' => 'facebook',
                'utm_medium' => 'social',
                'first_touch_source' => 'facebook',
                'first_touch_medium' => 'social'
            ),
            'referral' => array(
                'utm_source' => 'referral',
                'utm_medium' => 'referral',
                'first_touch_source' => 'referral',
                'first_touch_medium' => 'referral'
            ),
            'advertisement' => array(
                'utm_source' => 'ads',
                'utm_medium' => 'display',
                'first_touch_source' => 'ads',
                'first_touch_medium' => 'display'
            )
        );
        
        if (isset($sourceMapping[$leadSource])) {
            $attributionData = $sourceMapping[$leadSource];
            
            // Add campaign information if available
            if (!empty($bean->lead_source_description)) {
                $attributionData['utm_campaign'] = $bean->lead_source_description;
                $attributionData['first_touch_campaign'] = $bean->lead_source_description;
            }
        }
        
        return $attributionData;
    }
    
    /**
     * Get Google Analytics data for the lead
     */
    private function getGoogleAnalyticsData($bean)
    {
        try {
            // Check if Google Analytics is configured
            $gaPropertyId = $GLOBALS['sugar_config']['ga4_property_id'] ?? '';
            if (empty($gaPropertyId)) {
                return array();
            }
            
            // Try to get GA client ID from various sources
            $clientId = $this->getGAClientId($bean);
            if (empty($clientId)) {
                return array();
            }
            
            // Initialize Google Analytics API
            $gaAPI = new GoogleAnalyticsAPI($gaPropertyId);
            
            // Get attribution data from GA4
            $gaData = $gaAPI->getAttributionData($clientId, 30);
            
            if (!empty($gaData)) {
                // Convert GA4 data to our attribution format
                return $this->convertGADataToAttribution($gaData);
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("LeadAttributionLogicHook: Error getting GA data - " . $e->getMessage());
        }
        
        return array();
    }
    
    /**
     * Get Google Analytics client ID for the lead
     */
    private function getGAClientId($bean)
    {
        // Try various sources for GA client ID
        
        // 1. From request parameters
        if (!empty($_REQUEST['ga_client_id'])) {
            return $_REQUEST['ga_client_id'];
        }
        
        // 2. From cookies (if available)
        if (!empty($_COOKIE['_ga'])) {
            // Parse GA cookie format: GA1.2.clientId
            $gaCookie = $_COOKIE['_ga'];
            $parts = explode('.', $gaCookie);
            if (count($parts) >= 4) {
                return $parts[2] . '.' . $parts[3];
            }
        }
        
        // 3. From session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!empty($_SESSION['ga_client_id'])) {
            return $_SESSION['ga_client_id'];
        }
        
        // 4. From existing attribution records for this lead
        $attribution = BeanFactory::newBean('DM_LeadAttribution');
        $attribution->retrieve_by_string_fields(array('lead_id' => $bean->id));
        if (!empty($attribution->ga_client_id)) {
            return $attribution->ga_client_id;
        }
        
        return '';
    }
    
    /**
     * Convert Google Analytics data to attribution format
     */
    private function convertGADataToAttribution($gaData)
    {
        if (empty($gaData)) {
            return array();
        }
        
        // Get first and last touchpoints
        $firstTouch = reset($gaData);
        $lastTouch = end($gaData);
        
        $attributionData = array(
            'first_touch_source' => $firstTouch['first_source'] ?? '',
            'first_touch_medium' => $firstTouch['first_medium'] ?? '',
            'first_touch_campaign' => $firstTouch['first_campaign'] ?? '',
            'first_touch_date' => $firstTouch['date'] ?? '',
            'last_touch_source' => $lastTouch['session_source'] ?? '',
            'last_touch_medium' => $lastTouch['session_medium'] ?? '',
            'last_touch_campaign' => $lastTouch['session_campaign'] ?? '',
            'touch_count' => count($gaData),
            'external_ids' => json_encode(array('google_analytics' => $gaData))
        );
        
        return $attributionData;
    }
    
    /**
     * Create or update attribution record
     */
    private function createAttributionRecord($leadId, $attributionData)
    {
        try {
            // Check if attribution record already exists
            $attribution = BeanFactory::newBean('DM_LeadAttribution');
            $attribution->retrieve_by_string_fields(array('lead_id' => $leadId));
            
            $isNew = empty($attribution->id);
            
            if ($isNew) {
                $attribution = BeanFactory::newBean('DM_LeadAttribution');
                $attribution->lead_id = $leadId;
                $attribution->attribution_model = 'Last-touch'; // Default model
            }
            
            // Update attribution data
            foreach ($attributionData as $field => $value) {
                if (property_exists($attribution, $field) && !empty($value)) {
                    $attribution->$field = $value;
                }
            }
            
            // Set timestamps if new record
            if ($isNew) {
                $now = date('Y-m-d H:i:s');
                $attribution->first_touch_date = $attribution->first_touch_date ?: $now;
                $attribution->last_touch_date = $now;
            }
            
            // Save attribution record
            $attribution->save();
            
            $action = $isNew ? 'Created' : 'Updated';
            $GLOBALS['log']->info("LeadAttributionLogicHook: $action attribution record for lead: $leadId");
            
            return $attribution;
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("LeadAttributionLogicHook: Error saving attribution record - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Before delete hook for Leads - handle attribution cleanup
     */
    public function beforeDeleteLead($bean, $event, $arguments)
    {
        try {
            // Find and mark attribution records as deleted
            $attribution = BeanFactory::newBean('DM_LeadAttribution');
            $attributionList = $attribution->get_full_list('', "lead_id = '{$bean->id}'");
            
            if (!empty($attributionList)) {
                foreach ($attributionList as $attributionRecord) {
                    $attributionRecord->mark_deleted($attributionRecord->id);
                }
                $GLOBALS['log']->info("LeadAttributionLogicHook: Cleaned up attribution records for deleted lead: " . $bean->id);
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("LeadAttributionLogicHook: Error cleaning up attribution records - " . $e->getMessage());
        }
    }
    
    /**
     * Hook for lead conversion - update attribution with opportunity info
     */
    public function leadConversionHook($bean, $event, $arguments)
    {
        try {
            if (!empty($arguments['opportunity_id'])) {
                // Update attribution record with opportunity information
                $attribution = BeanFactory::newBean('DM_LeadAttribution');
                $attribution->retrieve_by_string_fields(array('lead_id' => $bean->id));
                
                if (!empty($attribution->id)) {
                    $attribution->opportunity_id = $arguments['opportunity_id'];
                    $attribution->customer_id = $arguments['account_id'] ?? '';
                    $attribution->save();
                    
                    $GLOBALS['log']->info("LeadAttributionLogicHook: Updated attribution for converted lead: " . $bean->id);
                }
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("LeadAttributionLogicHook: Error updating attribution for converted lead - " . $e->getMessage());
        }
    }
}