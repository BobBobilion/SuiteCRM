<?php
/**
 * SuiteCRM Trade-In Manager - Controller
 * 
 * This controller handles custom actions for the Trade-In Manager module,
 * including API integrations for VIN decoding and market valuations.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/Controller/SugarController.php');

/**
 * DM_TradeIns Controller Class
 * 
 * Handles custom actions for trade-in management including:
 * - VIN decoding via NHTSA API
 * - Market valuation via Vehicle Databases API
 * - Bulk operations on trade-ins
 * - Status workflow management
 */
class DM_TradeInsController extends SugarController
{
    /**
     * Pre-action processing
     */
    public function preProcess()
    {
        parent::preProcess();
        
        // Log controller action
        $GLOBALS['log']->info("DM_TradeInsController: Processing action - " . $this->action);
    }

    /**
     * Get market values for a vehicle using Vehicle Databases API
     */
    public function action_get_market_values()
    {
        $GLOBALS['log']->info("DM_TradeInsController: action_get_market_values() called");
        
        $response = array(
            'success' => false,
            'message' => '',
            'data' => array()
        );
        
        try {
            // Get parameters
            $vin = $_REQUEST['vin'] ?? '';
            $year = $_REQUEST['year'] ?? '';
            $make = $_REQUEST['make'] ?? '';
            $model = $_REQUEST['model'] ?? '';
            $mileage = $_REQUEST['mileage'] ?? '';
            $condition = $_REQUEST['condition'] ?? 'Good';
            
            if (empty($year) || empty($make) || empty($model)) {
                throw new Exception('Year, Make, and Model are required for market valuation');
            }
            
            // Get market values from API
            $valuationData = $this->getMarketValuesFromAPI($year, $make, $model, $mileage, $condition);
            
            if ($valuationData) {
                $response['success'] = true;
                $response['message'] = 'Market values retrieved successfully';
                $response['data'] = $valuationData;
                
                $GLOBALS['log']->info("DM_TradeInsController: Market values retrieved successfully");
            } else {
                throw new Exception('Failed to retrieve market values from API');
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_TradeInsController: Error getting market values - " . $e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * Decode VIN using NHTSA API
     */
    public function action_decode_vin()
    {
        $GLOBALS['log']->info("DM_TradeInsController: action_decode_vin() called");
        
        $response = array(
            'success' => false,
            'message' => '',
            'data' => array()
        );
        
        try {
            $vin = $_REQUEST['vin'] ?? '';
            
            if (empty($vin) || strlen($vin) !== 17) {
                throw new Exception('Valid 17-character VIN is required');
            }
            
            // Decode VIN using NHTSA API
            $vehicleData = $this->decodeVINFromAPI($vin);
            
            if ($vehicleData) {
                $response['success'] = true;
                $response['message'] = 'VIN decoded successfully';
                $response['data'] = $vehicleData;
                
                $GLOBALS['log']->info("DM_TradeInsController: VIN decoded successfully for: " . $vin);
            } else {
                throw new Exception('Failed to decode VIN');
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_TradeInsController: Error decoding VIN - " . $e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * Bulk update trade-in statuses
     */
    public function action_bulk_update_status()
    {
        $GLOBALS['log']->info("DM_TradeInsController: action_bulk_update_status() called");
        
        $response = array(
            'success' => false,
            'message' => '',
            'updated_count' => 0
        );
        
        try {
            $trade_in_ids = $_REQUEST['trade_in_ids'] ?? array();
            $new_status = $_REQUEST['new_status'] ?? '';
            
            if (empty($trade_in_ids) || empty($new_status)) {
                throw new Exception('Trade-in IDs and new status are required');
            }
            
            $updated_count = 0;
            
            foreach ($trade_in_ids as $trade_in_id) {
                $trade_in = BeanFactory::getBean('DM_TradeIns', $trade_in_id);
                if ($trade_in && !empty($trade_in->id)) {
                    $trade_in->status = $new_status;
                    
                    // Set workflow dates based on status
                    if ($new_status === 'Appraised' && empty($trade_in->appraisal_date)) {
                        $trade_in->appraisal_date = date('Y-m-d H:i:s');
                    }
                    
                    $trade_in->save();
                    $updated_count++;
                    
                    $GLOBALS['log']->info("DM_TradeInsController: Updated trade-in {$trade_in_id} status to {$new_status}");
                }
            }
            
            $response['success'] = true;
            $response['message'] = "Updated {$updated_count} trade-in(s) successfully";
            $response['updated_count'] = $updated_count;
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_TradeInsController: Error in bulk update - " . $e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * Schedule appraisal action
     */
    public function action_schedule_appraisal()
    {
        $GLOBALS['log']->info("DM_TradeInsController: action_schedule_appraisal() called");
        
        try {
            $trade_in_id = $_REQUEST['record'] ?? '';
            $appraisal_date = $_REQUEST['appraisal_date'] ?? '';
            
            if (empty($trade_in_id)) {
                throw new Exception('Trade-in ID is required');
            }
            
            $trade_in = BeanFactory::getBean('DM_TradeIns', $trade_in_id);
            if (!$trade_in || empty($trade_in->id)) {
                throw new Exception('Trade-in record not found');
            }
            
            // Set appraisal scheduled date
            if (!empty($appraisal_date)) {
                $trade_in->appraisal_scheduled_date = $appraisal_date;
            } else {
                // Default to tomorrow at 10 AM
                $trade_in->appraisal_scheduled_date = date('Y-m-d 10:00:00', strtotime('+1 day'));
            }
            
            $trade_in->save();
            
            $GLOBALS['log']->info("DM_TradeInsController: Appraisal scheduled for trade-in: " . $trade_in_id);
            
            // Redirect back to detail view
            SugarApplication::redirect("index.php?module=DM_TradeIns&action=DetailView&record={$trade_in_id}");
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_TradeInsController: Error scheduling appraisal - " . $e->getMessage());
            
            // Redirect with error message
            $_SESSION['tradein_error'] = $e->getMessage();
            SugarApplication::redirect("index.php?module=DM_TradeIns&action=DetailView&record={$trade_in_id}");
        }
    }

    /**
     * Get market values from Vehicle Databases API
     * 
     * @param string $year Vehicle year
     * @param string $make Vehicle make
     * @param string $model Vehicle model
     * @param string $mileage Vehicle mileage
     * @param string $condition Vehicle condition
     * @return array|false Market valuation data or false on failure
     */
    private function getMarketValuesFromAPI($year, $make, $model, $mileage = '', $condition = 'Good')
    {
        $GLOBALS['log']->info("DM_TradeInsController: getMarketValuesFromAPI() called for {$year} {$make} {$model}");
        
        // For MVP, we'll use demo calculations
        // In production, this would call the Vehicle Databases API
        
        $baseValue = $this->calculateDemoBaseValue($make);
        $ageAdjustment = $this->calculateAgeAdjustment($year);
        $mileageAdjustment = $this->calculateMileageAdjustment($mileage, $year);
        $conditionAdjustment = $this->calculateConditionAdjustment($condition);
        
        $adjustedValue = $baseValue - $ageAdjustment - $mileageAdjustment + $conditionAdjustment;
        
        // Calculate different value types
        $retail_value = round($adjustedValue * 1.15);
        $trade_value = round($adjustedValue * 0.85);
        $private_value = round($adjustedValue);
        
        $valuationData = array(
            'retail_value' => $retail_value,
            'trade_value' => $trade_value,
            'private_value' => $private_value,
            'valuation_date' => date('Y-m-d H:i:s'),
            'valuation_source' => 'Demo Calculation',
            'api_response' => array(
                'success' => true,
                'source' => 'Internal calculation for MVP demo'
            )
        );
        
        $GLOBALS['log']->info("DM_TradeInsController: Demo values calculated - Retail: {$retail_value}, Trade: {$trade_value}, Private: {$private_value}");
        
        return $valuationData;
    }

    /**
     * Decode VIN using NHTSA API
     * 
     * @param string $vin Vehicle VIN
     * @return array|false Vehicle data or false on failure
     */
    private function decodeVINFromAPI($vin)
    {
        $GLOBALS['log']->info("DM_TradeInsController: decodeVINFromAPI() called for VIN: " . $vin);
        
        try {
            $apiUrl = "https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/{$vin}?format=json";
            
            // Use curl to make API request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200 || !$response) {
                throw new Exception("NHTSA API request failed with HTTP code: " . $httpCode);
            }
            
            $data = json_decode($response, true);
            
            if (!$data || !isset($data['Results'])) {
                throw new Exception("Invalid response from NHTSA API");
            }
            
            // Extract vehicle information
            $vehicleData = array();
            
            foreach ($data['Results'] as $result) {
                switch ($result['Variable']) {
                    case 'Model Year':
                        if ($result['Value'] && $result['Value'] !== 'Not Applicable') {
                            $vehicleData['year'] = $result['Value'];
                        }
                        break;
                    case 'Make':
                        if ($result['Value'] && $result['Value'] !== 'Not Applicable') {
                            $vehicleData['make'] = $result['Value'];
                        }
                        break;
                    case 'Model':
                        if ($result['Value'] && $result['Value'] !== 'Not Applicable') {
                            $vehicleData['model'] = $result['Value'];
                        }
                        break;
                    case 'Trim':
                        if ($result['Value'] && $result['Value'] !== 'Not Applicable') {
                            $vehicleData['trim'] = $result['Value'];
                        }
                        break;
                    case 'Vehicle Type':
                        if ($result['Value'] && $result['Value'] !== 'Not Applicable') {
                            $vehicleData['vehicle_type'] = $result['Value'];
                        }
                        break;
                }
            }
            
            $GLOBALS['log']->info("DM_TradeInsController: VIN decoded successfully - " . json_encode($vehicleData));
            
            return $vehicleData;
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_TradeInsController: VIN decode error - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate demo base value for different makes
     */
    private function calculateDemoBaseValue($make)
    {
        $baseValues = array(
            'TOYOTA' => 25000,
            'HONDA' => 23000,
            'FORD' => 20000,
            'CHEVROLET' => 19000,
            'NISSAN' => 18000,
            'BMW' => 35000,
            'MERCEDES-BENZ' => 40000,
            'AUDI' => 32000,
            'VOLKSWAGEN' => 22000,
            'MAZDA' => 20000,
            'SUBARU' => 24000,
            'HYUNDAI' => 18000,
            'KIA' => 17000,
            'JEEP' => 25000,
            'DODGE' => 22000,
            'CHRYSLER' => 21000,
            'CADILLAC' => 35000,
            'BUICK' => 28000,
            'GMC' => 26000,
            'LINCOLN' => 38000
        );
        
        return $baseValues[strtoupper($make)] ?? 20000;
    }

    /**
     * Calculate age-based depreciation adjustment
     */
    private function calculateAgeAdjustment($year)
    {
        $currentYear = date('Y');
        $age = $currentYear - intval($year);
        
        // $2,000 depreciation per year on average
        return $age * 2000;
    }

    /**
     * Calculate mileage adjustment
     */
    private function calculateMileageAdjustment($mileage, $year)
    {
        if (empty($mileage) || empty($year)) {
            return 0;
        }
        
        $currentYear = date('Y');
        $age = $currentYear - intval($year);
        $expectedMileage = $age * 12000; // 12,000 miles per year average
        $mileageDifference = intval($mileage) - $expectedMileage;
        
        // $0.10 per mile adjustment
        return $mileageDifference * 0.10;
    }

    /**
     * Calculate condition-based adjustment
     */
    private function calculateConditionAdjustment($condition)
    {
        $adjustments = array(
            'Excellent' => 2000,
            'Good' => 0,
            'Fair' => -3000,
            'Poor' => -6000
        );
        
        return $adjustments[$condition] ?? 0;
    }
} 