<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM Lead Attribution Center - Main Bean Class
 * 
 * This class defines the DM_LeadAttribution SugarBean entity for managing lead 
 * attribution tracking, UTM parameter capture, and ROI analysis within marketing
 * and sales operations.
 * 
 * Features:
 * - UTM parameter capture and storage
 * - Google Analytics 4 API integration
 * - Facebook Lead Ads webhook processing
 * - Multi-touch attribution modeling
 * - ROI calculation and cost analysis
 * - Lead journey tracking and reporting
 * - Integration with Leads, Accounts, and Opportunities
 */

require_once("include/SugarObjects/templates/basic/Basic.php");

/**
 * DM_LeadAttribution Bean Class
 * 
 * Manages lead attribution data with UTM tracking, API integration, and 
 * ROI analysis for comprehensive marketing attribution reporting
 */
#[\AllowDynamicProperties]
class DM_LeadAttribution extends Basic
{
    public $table_name = "dm_leadattribution";
    public $object_name = "DM_LeadAttribution";
    public $module_dir = "DM_LeadAttribution";
    public $module_name = "DM_LeadAttribution";
    
    // Disable row-level security for now
    public $disable_row_level_security = true;
    
    // Enable auditing for attribution tracking (compliance requirement)
    public $audited = true;
    
    // Core attribution identification fields
    public $id;
    public $name;                          // Display name (Lead Name - Campaign)
    
    // Relationship links
    public $lead_id;                       // Link to lead record
    public $customer_id;                   // Link to customer account
    public $opportunity_id;                // Link to sales opportunity
    
    // First Touch Attribution
    public $first_touch_source;           // Initial source (Google, Facebook, Direct, etc.)
    public $first_touch_medium;           // Initial medium (cpc, organic, social, etc.)
    public $first_touch_campaign;         // Initial campaign name
    public $first_touch_date;             // First interaction timestamp
    
    // Last Touch Attribution
    public $last_touch_source;            // Last source before conversion
    public $last_touch_medium;            // Last medium before conversion
    public $last_touch_campaign;          // Last campaign before conversion
    public $last_touch_date;              // Last interaction timestamp
    
    // UTM Parameter Tracking
    public $utm_source;                   // UTM source parameter
    public $utm_medium;                   // UTM medium parameter
    public $utm_campaign;                 // UTM campaign name
    public $utm_content;                  // UTM content variant
    public $utm_term;                     // UTM keyword term
    
    // Referrer and Landing Page
    public $referrer_url;                 // Referring website URL
    public $landing_page;                 // First page visited
    
    // ROI and Cost Analysis
    public $conversion_value;             // Revenue attributed to this lead
    public $total_cost;                   // Cost to acquire this lead
    public $roi_percentage;               // Return on investment percentage
    
    // Attribution Analysis
    public $touch_count;                  // Number of touchpoints
    public $attribution_model;            // Attribution model used
    public $channel_journey;              // JSON of full customer journey
    
    // External Platform Integration
    public $external_ids;                 // JSON of external platform IDs
    public $ga_client_id;                 // Google Analytics Client ID
    public $ga_session_id;                // Google Analytics Session ID
    public $fb_lead_id;                   // Facebook Lead Ads Lead ID
    public $fb_form_id;                   // Facebook Lead Form ID
    public $fb_ad_id;                     // Facebook Ad ID
    
    // Additional tracking
    public $notes;                        // Additional attribution notes
    
    // Standard SugarBean fields
    public $assigned_user_id;             // Assigned attribution analyst
    public $date_entered;
    public $date_modified;
    public $created_by;
    public $modified_user_id;
    public $deleted;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Initialize logging for attribution operations
        $GLOBALS['log']->info("DM_LeadAttribution: Initializing Lead Attribution Bean");
    }

    /**
     * Generate display name for attribution record
     * Format: "Lead Name - First Touch Campaign"
     */
    public function save($check_notify = false)
    {
        // Log the save operation
        $GLOBALS['log']->info("DM_LeadAttribution: Saving attribution record ID: " . $this->id);
        
        // Auto-generate name if not provided
        if (empty($this->name)) {
            $this->name = $this->generateDisplayName();
            $GLOBALS['log']->info("DM_LeadAttribution: Auto-generated name: " . $this->name);
        }
        
        // Calculate ROI if both values are available
        if (!empty($this->conversion_value) && !empty($this->total_cost)) {
            $this->roi_percentage = $this->calculateROI();
            $GLOBALS['log']->info("DM_LeadAttribution: Calculated ROI: " . $this->roi_percentage . "%");
        }
        
        // Update touch dates
        $this->updateTouchDates();
        
        // Update channel journey
        $this->updateChannelJourney();
        
        return parent::save($check_notify);
    }

    /**
     * Generate display name for the attribution record
     * 
     * @return string Generated display name
     */
    private function generateDisplayName()
    {
        $name = '';
        
        // Get lead name if available
        if (!empty($this->lead_id)) {
            $lead = BeanFactory::getBean('Leads', $this->lead_id);
            if ($lead && !empty($lead->name)) {
                $name = $lead->name;
            }
        }
        
        // Add first touch campaign if available
        if (!empty($this->first_touch_campaign)) {
            $name .= (!empty($name) ? ' - ' : '') . $this->first_touch_campaign;
        } elseif (!empty($this->utm_campaign)) {
            $name .= (!empty($name) ? ' - ' : '') . $this->utm_campaign;
        }
        
        // Fallback to attribution model and source
        if (empty($name)) {
            $name = $this->attribution_model . ' Attribution';
            if (!empty($this->first_touch_source)) {
                $name .= ' - ' . $this->first_touch_source;
            }
        }
        
        return $name;
    }

    /**
     * Calculate ROI percentage
     * 
     * @return float ROI percentage
     */
    public function calculateROI()
    {
        if (empty($this->total_cost) || $this->total_cost == 0) {
            return 0;
        }
        
        $roi = (($this->conversion_value - $this->total_cost) / $this->total_cost) * 100;
        return round($roi, 2);
    }

    /**
     * Update touch dates based on attribution model
     */
    private function updateTouchDates()
    {
        $currentDate = date('Y-m-d H:i:s');
        
        // Set first touch date if not set
        if (empty($this->first_touch_date)) {
            $this->first_touch_date = $currentDate;
        }
        
        // Always update last touch date
        $this->last_touch_date = $currentDate;
        
        // Increment touch count
        if (empty($this->touch_count)) {
            $this->touch_count = 1;
        } else {
            $this->touch_count++;
        }
    }

    /**
     * Update channel journey with new touchpoint
     */
    private function updateChannelJourney()
    {
        $journey = array();
        
        // Parse existing journey
        if (!empty($this->channel_journey)) {
            $journey = json_decode($this->channel_journey, true);
            if (!is_array($journey)) {
                $journey = array();
            }
        }
        
        // Add new touchpoint
        $touchpoint = array(
            'timestamp' => date('Y-m-d H:i:s'),
            'source' => $this->last_touch_source,
            'medium' => $this->last_touch_medium,
            'campaign' => $this->last_touch_campaign,
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_content' => $this->utm_content,
            'utm_term' => $this->utm_term,
            'referrer_url' => $this->referrer_url,
            'landing_page' => $this->landing_page
        );
        
        $journey[] = $touchpoint;
        
        // Store updated journey
        $this->channel_journey = json_encode($journey);
    }

    /**
     * Capture UTM parameters from URL or POST data
     * 
     * @param array $params UTM parameters array
     * @return bool Success status
     */
    public function captureUTMParameters($params = null)
    {
        if ($params === null) {
            $params = $_GET; // Default to GET parameters
        }
        
        $captured = false;
        
        // Capture UTM parameters
        $utmFields = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term');
        
        foreach ($utmFields as $field) {
            if (isset($params[$field]) && !empty($params[$field])) {
                $this->$field = $params[$field];
                $captured = true;
                $GLOBALS['log']->info("DM_LeadAttribution: Captured $field: " . $params[$field]);
            }
        }
        
        // Capture referrer if available
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            $this->referrer_url = $_SERVER['HTTP_REFERER'];
            $captured = true;
        }
        
        // Capture landing page
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->landing_page = $_SERVER['REQUEST_URI'];
            $captured = true;
        }
        
        // Set first touch attribution from UTM parameters
        if ($captured && empty($this->first_touch_source)) {
            $this->first_touch_source = $this->utm_source;
            $this->first_touch_medium = $this->utm_medium;
            $this->first_touch_campaign = $this->utm_campaign;
        }
        
        // Set last touch attribution
        $this->last_touch_source = $this->utm_source;
        $this->last_touch_medium = $this->utm_medium;
        $this->last_touch_campaign = $this->utm_campaign;
        
        return $captured;
    }

    /**
     * Process Google Analytics 4 data
     * 
     * @param array $gaData GA4 data array
     * @return bool Success status
     */
    public function processGoogleAnalyticsData($gaData)
    {
        if (empty($gaData)) {
            return false;
        }
        
        // Store GA4 identifiers
        if (isset($gaData['client_id'])) {
            $this->ga_client_id = $gaData['client_id'];
        }
        
        if (isset($gaData['session_id'])) {
            $this->ga_session_id = $gaData['session_id'];
        }
        
        // Extract attribution data from GA4
        if (isset($gaData['source'])) {
            $this->first_touch_source = $gaData['source'];
            $this->last_touch_source = $gaData['source'];
        }
        
        if (isset($gaData['medium'])) {
            $this->first_touch_medium = $gaData['medium'];
            $this->last_touch_medium = $gaData['medium'];
        }
        
        if (isset($gaData['campaign'])) {
            $this->first_touch_campaign = $gaData['campaign'];
            $this->last_touch_campaign = $gaData['campaign'];
        }
        
        // Store full GA data in external_ids
        $externalIds = array();
        if (!empty($this->external_ids)) {
            $externalIds = json_decode($this->external_ids, true);
        }
        $externalIds['google_analytics'] = $gaData;
        $this->external_ids = json_encode($externalIds);
        
        $GLOBALS['log']->info("DM_LeadAttribution: Processed Google Analytics data for client: " . $this->ga_client_id);
        
        return true;
    }

    /**
     * Process Facebook Lead Ads webhook data
     * 
     * @param array $fbData Facebook Lead Ads data
     * @return bool Success status
     */
    public function processFacebookLeadAdsData($fbData)
    {
        if (empty($fbData)) {
            return false;
        }
        
        // Store Facebook identifiers
        if (isset($fbData['lead_id'])) {
            $this->fb_lead_id = $fbData['lead_id'];
        }
        
        if (isset($fbData['form_id'])) {
            $this->fb_form_id = $fbData['form_id'];
        }
        
        if (isset($fbData['ad_id'])) {
            $this->fb_ad_id = $fbData['ad_id'];
        }
        
        // Set attribution data for Facebook
        $this->first_touch_source = 'facebook';
        $this->first_touch_medium = 'social';
        $this->last_touch_source = 'facebook';
        $this->last_touch_medium = 'social';
        
        if (isset($fbData['campaign_name'])) {
            $this->first_touch_campaign = $fbData['campaign_name'];
            $this->last_touch_campaign = $fbData['campaign_name'];
        }
        
        // Store full Facebook data in external_ids
        $externalIds = array();
        if (!empty($this->external_ids)) {
            $externalIds = json_decode($this->external_ids, true);
        }
        $externalIds['facebook'] = $fbData;
        $this->external_ids = json_encode($externalIds);
        
        $GLOBALS['log']->info("DM_LeadAttribution: Processed Facebook Lead Ads data for lead: " . $this->fb_lead_id);
        
        return true;
    }

    /**
     * Get attribution summary for reporting
     * 
     * @return array Attribution summary data
     */
    public function getAttributionSummary()
    {
        return array(
            'lead_id' => $this->lead_id,
            'first_touch' => array(
                'source' => $this->first_touch_source,
                'medium' => $this->first_touch_medium,
                'campaign' => $this->first_touch_campaign,
                'date' => $this->first_touch_date
            ),
            'last_touch' => array(
                'source' => $this->last_touch_source,
                'medium' => $this->last_touch_medium,
                'campaign' => $this->last_touch_campaign,
                'date' => $this->last_touch_date
            ),
            'utm_data' => array(
                'source' => $this->utm_source,
                'medium' => $this->utm_medium,
                'campaign' => $this->utm_campaign,
                'content' => $this->utm_content,
                'term' => $this->utm_term
            ),
            'roi' => array(
                'conversion_value' => $this->conversion_value,
                'total_cost' => $this->total_cost,
                'roi_percentage' => $this->roi_percentage
            ),
            'touch_count' => $this->touch_count,
            'attribution_model' => $this->attribution_model
        );
    }

    /**
     * Get channel journey as array
     * 
     * @return array Channel journey touchpoints
     */
    public function getChannelJourney()
    {
        if (empty($this->channel_journey)) {
            return array();
        }
        
        $journey = json_decode($this->channel_journey, true);
        return is_array($journey) ? $journey : array();
    }

    /**
     * Static method to capture attribution for a lead
     * 
     * @param string $leadId Lead ID
     * @param array $attributionData Attribution data
     * @return DM_LeadAttribution|false Attribution record or false on failure
     */
    public static function captureLeadAttribution($leadId, $attributionData = array())
    {
        if (empty($leadId)) {
            return false;
        }
        
        // Check if attribution record already exists for this lead
        $attribution = BeanFactory::newBean('DM_LeadAttribution');
        $attribution->retrieve_by_string_fields(array('lead_id' => $leadId));
        
        // Create new record if none exists
        if (empty($attribution->id)) {
            $attribution = BeanFactory::newBean('DM_LeadAttribution');
            $attribution->lead_id = $leadId;
            $attribution->attribution_model = 'Last-touch'; // Default model
        }
        
        // Populate attribution data
        foreach ($attributionData as $field => $value) {
            if (property_exists($attribution, $field)) {
                $attribution->$field = $value;
            }
        }
        
        // Capture UTM parameters from current request
        $attribution->captureUTMParameters();
        
        // Save the attribution record
        $attribution->save();
        
        $GLOBALS['log']->info("DM_LeadAttribution: Captured attribution for lead: $leadId");
        
        return $attribution;
    }

    /**
     * Bean relationship setup
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}