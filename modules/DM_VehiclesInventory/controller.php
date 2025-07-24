<?php
/**
 * SuiteCRM Vehicle Inventory System - Custom Controller
 *
 * This controller handles custom actions for the vehicle inventory module
 * including VIN decoding, photo uploads, bulk operations, and print functionality.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/Controller/SugarController.php');

/**
 * DM_VehiclesInventory Custom Controller
 *
 * Handles specialized vehicle inventory actions and API integrations
 */
class DM_VehiclesInventoryController extends SugarController
{
    /**
     * List of valid actions this controller can handle
     * Note: 'index' => 'listview' is already handled by parent SugarController
     */
    protected $action_remap = array(
        'index' => 'listview',  // Add this mapping to fix the "no action" error
        'decodevin' => 'decodeVIN',
        'uploadphoto' => 'uploadPhoto',
        'deletephoto' => 'deletePhoto',
        'bulkstatus' => 'bulkStatusUpdate',
        'printwindowsticker' => 'printWindowSticker',
        'marketvaluation' => 'getMarketValuation',
        'featuresave' => 'saveFeatures',
        'duplicatecheck' => 'checkDuplicateVIN',
    );

    /**
     * Map actions to views - fixes "no action by that name" errors
     */
    protected $action_view_map = array(
        'editview' => 'edit',
        'detailview' => 'detail',
        'listview' => 'list'
    );

    /**
     * Override setup to ensure proper initialization
     */
    public function setup($module = '')
    {
        // Call parent setup first
        parent::setup($module);
        
        // Log setup information for debugging
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Setup called - Module: {$this->module}, Action: {$this->action}, Do_Action: {$this->do_action}");
        
        // Ensure we have proper access
        if (!ACLController::checkAccess($this->module, 'list', true)) {
            $this->hasAccess = false;
            $GLOBALS['log']->debug("DM_VehiclesInventoryController: Access denied for module {$this->module}");
        }
    }

    /**
     * Pre-action setup and validation
     *
     * @return boolean True to continue with action, false to abort
     */
    public function pre_action()
    {
        parent::pre_action();

        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Pre-action for action: " . $this->action);

        // Log the incoming request for debugging
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Request method: " . $_SERVER['REQUEST_METHOD']);
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: POST data: " . print_r($_POST, true));
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: GET data: " . print_r($_GET, true));

        return true;
    }

    /**
     * Override listview action to ensure proper module loading
     * This handles the remapped 'index' action
     */
    public function action_listview()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Executing listview action");
        
        // Ensure the module is properly initialized
        if (!$this->bean) {
            $this->loadBean();
        }
        
        // Call parent listview action
        parent::action_listview();
    }

    /**
     * Decode VIN and return vehicle information
     * Uses external VIN decoder API to populate vehicle details
     */
    public function action_decodeVIN()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Starting VIN decode action");

        try {
            // Get VIN from request
            $vin = !empty($_REQUEST['vin']) ? trim($_REQUEST['vin']) : '';
            
            if (empty($vin)) {
                throw new Exception('VIN is required for decoding');
            }

            // Validate VIN format
            if (!$this->validateVINFormat($vin)) {
                throw new Exception('Invalid VIN format. Please provide a valid 17-character VIN.');
            }

            $GLOBALS['log']->debug("DM_VehiclesInventoryController: Decoding VIN: " . $vin);

            // Call VIN decoder service
            $decodedData = $this->callVINDecoderAPI($vin);

            // Return JSON response
            $this->sendJSONResponse(array(
                'success' => true,
                'data' => $decodedData,
                'message' => 'VIN decoded successfully'
            ));

        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_VehiclesInventoryController: VIN decode error: " . $e->getMessage());
            
            $this->sendJSONResponse(array(
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ), 400);
        }
    }

    /**
     * Handle photo upload for vehicles
     * Supports multiple file uploads with validation and resizing
     */
    public function action_uploadPhoto()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Starting photo upload action");

        try {
            // Get vehicle ID
            $vehicleId = !empty($_REQUEST['vehicle_id']) ? $_REQUEST['vehicle_id'] : '';
            
            if (empty($vehicleId)) {
                throw new Exception('Vehicle ID is required for photo upload');
            }

            // Validate vehicle exists and user has permission
            $vehicle = BeanFactory::getBean('DM_VehiclesInventory', $vehicleId);
            if (!$vehicle || empty($vehicle->id)) {
                throw new Exception('Vehicle not found');
            }

            // Check file upload
            if (empty($_FILES['photos'])) {
                throw new Exception('No photos uploaded');
            }

            $GLOBALS['log']->debug("DM_VehiclesInventoryController: Processing photo uploads for vehicle: " . $vehicleId);

            $uploadedPhotos = $this->processPhotoUploads($_FILES['photos'], $vehicleId);

            // Update vehicle record with new photos
            $existingPhotos = json_decode($vehicle->photos ?: '[]', true);
            $allPhotos = array_merge($existingPhotos, $uploadedPhotos);
            
            $vehicle->photos = json_encode($allPhotos);
            $vehicle->save();

            $GLOBALS['log']->debug("DM_VehiclesInventoryController: Successfully uploaded " . count($uploadedPhotos) . " photos");

            $this->sendJSONResponse(array(
                'success' => true,
                'data' => array(
                    'uploaded_count' => count($uploadedPhotos),
                    'total_photos' => count($allPhotos),
                    'photos' => $uploadedPhotos
                ),
                'message' => count($uploadedPhotos) . ' photo(s) uploaded successfully'
            ));

        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_VehiclesInventoryController: Photo upload error: " . $e->getMessage());
            
            $this->sendJSONResponse(array(
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ), 400);
        }
    }

    /**
     * Delete a specific photo from vehicle
     */
    public function action_deletePhoto()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Starting photo delete action");

        try {
            $vehicleId = !empty($_REQUEST['vehicle_id']) ? $_REQUEST['vehicle_id'] : '';
            $photoIndex = isset($_REQUEST['photo_index']) ? (int)$_REQUEST['photo_index'] : -1;

            if (empty($vehicleId)) {
                throw new Exception('Vehicle ID is required');
            }

            if ($photoIndex < 0) {
                throw new Exception('Valid photo index is required');
            }

            // Load vehicle
            $vehicle = BeanFactory::getBean('DM_VehiclesInventory', $vehicleId);
            if (!$vehicle || empty($vehicle->id)) {
                throw new Exception('Vehicle not found');
            }

            // Get current photos
            $photos = json_decode($vehicle->photos ?: '[]', true);
            
            if (!isset($photos[$photoIndex])) {
                throw new Exception('Photo not found at specified index');
            }

            $deletedPhoto = $photos[$photoIndex];

            // Remove photo from array
            array_splice($photos, $photoIndex, 1);

            // Update vehicle record
            $vehicle->photos = json_encode($photos);
            $vehicle->save();

            // TODO: Delete physical file from server/CDN
            // $this->deletePhotoFile($deletedPhoto['path']);

            $GLOBALS['log']->debug("DM_VehiclesInventoryController: Successfully deleted photo at index: " . $photoIndex);

            $this->sendJSONResponse(array(
                'success' => true,
                'message' => 'Photo deleted successfully',
                'data' => array(
                    'remaining_photos' => count($photos)
                )
            ));

        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_VehiclesInventoryController: Photo delete error: " . $e->getMessage());
            
            $this->sendJSONResponse(array(
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ), 400);
        }
    }

    /**
     * Bulk status update for multiple vehicles
     */
    public function action_bulkStatusUpdate()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Starting bulk status update action");

        try {
            $vehicleIds = !empty($_REQUEST['vehicle_ids']) ? $_REQUEST['vehicle_ids'] : array();
            $newStatus = !empty($_REQUEST['status']) ? $_REQUEST['status'] : '';

            if (empty($vehicleIds) || !is_array($vehicleIds)) {
                throw new Exception('Vehicle IDs array is required');
            }

            if (empty($newStatus)) {
                throw new Exception('New status is required');
            }

            $GLOBALS['log']->debug("DM_VehiclesInventoryController: Updating " . count($vehicleIds) . " vehicles to status: " . $newStatus);

            $successCount = 0;
            $errors = array();

            foreach ($vehicleIds as $vehicleId) {
                try {
                    $vehicle = BeanFactory::getBean('DM_VehiclesInventory', $vehicleId);
                    if ($vehicle && !empty($vehicle->id)) {
                        $vehicle->status = $newStatus;
                        $vehicle->save();
                        $successCount++;
                        
                        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Updated vehicle " . $vehicleId . " status to " . $newStatus);
                    } else {
                        $errors[] = "Vehicle ID {$vehicleId} not found";
                    }
                } catch (Exception $e) {
                    $errors[] = "Failed to update vehicle {$vehicleId}: " . $e->getMessage();
                    $GLOBALS['log']->error("DM_VehiclesInventoryController: Bulk update error for vehicle {$vehicleId}: " . $e->getMessage());
                }
            }

            $this->sendJSONResponse(array(
                'success' => true,
                'data' => array(
                    'updated_count' => $successCount,
                    'total_requested' => count($vehicleIds),
                    'errors' => $errors
                ),
                'message' => "Successfully updated {$successCount} vehicle(s)"
            ));

        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_VehiclesInventoryController: Bulk status update error: " . $e->getMessage());
            
            $this->sendJSONResponse(array(
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ), 400);
        }
    }

    /**
     * Generate and print window sticker for vehicle
     */
    public function action_printWindowSticker()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Starting print window sticker action");

        try {
            $vehicleId = !empty($_REQUEST['vehicle_id']) ? $_REQUEST['vehicle_id'] : '';
            
            if (empty($vehicleId)) {
                throw new Exception('Vehicle ID is required');
            }

            // Load vehicle
            $vehicle = BeanFactory::getBean('DM_VehiclesInventory', $vehicleId);
            if (!$vehicle || empty($vehicle->id)) {
                throw new Exception('Vehicle not found');
            }

            $GLOBALS['log']->debug("DM_VehiclesInventoryController: Generating window sticker for vehicle: " . $vehicleId);

            // Generate PDF window sticker
            $pdfContent = $this->generateWindowStickerPDF($vehicle);

            // Set headers for PDF download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="window_sticker_' . $vehicle->stock_number . '.pdf"');
            header('Content-Length: ' . strlen($pdfContent));

            echo $pdfContent;
            exit;

        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_VehiclesInventoryController: Window sticker error: " . $e->getMessage());
            
            // Redirect back with error message
            SugarApplication::redirect("index.php?module=DM_VehiclesInventory&action=DetailView&record={$vehicleId}&error=" . urlencode($e->getMessage()));
        }
    }

    /**
     * Save vehicle features from feature editor
     */
    public function action_saveFeatures()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Starting save features action");

        try {
            $vehicleId = !empty($_REQUEST['vehicle_id']) ? $_REQUEST['vehicle_id'] : '';
            $features = !empty($_REQUEST['features']) ? $_REQUEST['features'] : '';

            if (empty($vehicleId)) {
                throw new Exception('Vehicle ID is required');
            }

            // Load vehicle
            $vehicle = BeanFactory::getBean('DM_VehiclesInventory', $vehicleId);
            if (!$vehicle || empty($vehicle->id)) {
                throw new Exception('Vehicle not found');
            }

            // Validate and save features JSON
            if (!empty($features)) {
                $featuresArray = json_decode($features, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Invalid features JSON format');
                }
                $vehicle->features = $features;
            } else {
                $vehicle->features = '[]';
            }

            $vehicle->save();

            $GLOBALS['log']->debug("DM_VehiclesInventoryController: Successfully saved features for vehicle: " . $vehicleId);

            $this->sendJSONResponse(array(
                'success' => true,
                'message' => 'Features saved successfully',
                'data' => array(
                    'features_count' => count(json_decode($vehicle->features, true))
                )
            ));

        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_VehiclesInventoryController: Save features error: " . $e->getMessage());
            
            $this->sendJSONResponse(array(
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ), 400);
        }
    }

    /**
     * Check for duplicate VIN in the system
     */
    public function action_checkDuplicateVIN()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Starting duplicate VIN check");

        try {
            $vin = !empty($_REQUEST['vin']) ? trim($_REQUEST['vin']) : '';
            $excludeId = !empty($_REQUEST['exclude_id']) ? $_REQUEST['exclude_id'] : '';

            if (empty($vin)) {
                throw new Exception('VIN is required');
            }

            // Search for existing VIN
            $existingVehicle = BeanFactory::getBean('DM_VehiclesInventory');
            $vehicles = $existingVehicle->get_full_list('', "dm_vehiclesinventory.vin = '" . $existingVehicle->db->quote($vin) . "'" . 
                (!empty($excludeId) ? " AND dm_vehiclesinventory.id != '" . $existingVehicle->db->quote($excludeId) . "'" : ""));

            $isDuplicate = !empty($vehicles);

            $GLOBALS['log']->debug("DM_VehiclesInventoryController: VIN duplicate check for {$vin}: " . ($isDuplicate ? 'DUPLICATE' : 'UNIQUE'));

            $this->sendJSONResponse(array(
                'success' => true,
                'data' => array(
                    'is_duplicate' => $isDuplicate,
                    'vin' => $vin,
                    'existing_vehicle' => $isDuplicate ? array(
                        'id' => $vehicles[0]->id,
                        'name' => $vehicles[0]->name,
                        'stock_number' => $vehicles[0]->stock_number
                    ) : null
                ),
                'message' => $isDuplicate ? 'VIN already exists in system' : 'VIN is unique'
            ));

        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_VehiclesInventoryController: Duplicate VIN check error: " . $e->getMessage());
            
            $this->sendJSONResponse(array(
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ), 400);
        }
    }

    // Helper Methods

    /**
     * Validate VIN format (17 characters, no I, O, Q)
     */
    private function validateVINFormat($vin)
    {
        if (strlen($vin) !== 17) {
            return false;
        }

        // VIN cannot contain I, O, or Q
        if (preg_match('/[IOQ]/', strtoupper($vin))) {
            return false;
        }

        return true;
    }

    /**
     * Call external VIN decoder API
     * TODO: Implement actual API integration (NHTSA, DataOne, etc.)
     */
    private function callVINDecoderAPI($vin)
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Calling VIN decoder API for: " . $vin);

        // TODO: Replace with actual API call
        // For now, return mock data based on VIN
        return array(
            'year' => '20' . substr($vin, 9, 2),
            'make' => 'Toyota', // TODO: Decode from VIN
            'model' => 'Camry', // TODO: Decode from VIN
            'trim' => 'LE',
            'body_style' => 'Sedan',
            'engine_type' => '2.5L 4-Cylinder',
            'transmission' => 'Automatic',
            'drivetrain' => 'FWD',
            'fuel_type' => 'Gasoline',
            'country_origin' => 'United States',
            'manufacturer' => 'Toyota Motor Manufacturing',
            'plant_code' => substr($vin, 10, 1),
            'api_source' => 'Mock Data - TODO: Implement real API'
        );
    }

    /**
     * Process multiple photo uploads
     */
    private function processPhotoUploads($files, $vehicleId)
    {
        $uploadedPhotos = array();
        $uploadDir = 'upload/vehicles/' . $vehicleId . '/';

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Handle both single and multiple file uploads
        $fileCount = is_array($files['name']) ? count($files['name']) : 1;

        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $fileTmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];
            $fileError = is_array($files['error']) ? $files['error'][$i] : $files['error'];

            if ($fileError !== UPLOAD_ERR_OK) {
                $GLOBALS['log']->error("DM_VehiclesInventoryController: File upload error for {$fileName}: " . $fileError);
                continue;
            }

            // Validate file type
            $allowedTypes = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
            $fileType = mime_content_type($fileTmpName);
            
            if (!in_array($fileType, $allowedTypes)) {
                $GLOBALS['log']->error("DM_VehiclesInventoryController: Invalid file type for {$fileName}: " . $fileType);
                continue;
            }

            // Validate file size (max 5MB)
            if ($fileSize > 5 * 1024 * 1024) {
                $GLOBALS['log']->error("DM_VehiclesInventoryController: File too large: {$fileName} (" . $fileSize . " bytes)");
                continue;
            }

            // Generate unique filename
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = $vehicleId . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = $uploadDir . $uniqueFileName;

            // Move uploaded file
            if (move_uploaded_file($fileTmpName, $filePath)) {
                $uploadedPhotos[] = array(
                    'url' => $filePath,
                    'filename' => $uniqueFileName,
                    'original_name' => $fileName,
                    'size' => $fileSize,
                    'type' => $fileType,
                    'uploaded_at' => date('Y-m-d H:i:s'),
                    'description' => ''
                );

                $GLOBALS['log']->debug("DM_VehiclesInventoryController: Successfully uploaded photo: " . $filePath);
            } else {
                $GLOBALS['log']->error("DM_VehiclesInventoryController: Failed to move uploaded file: " . $fileName);
            }
        }

        return $uploadedPhotos;
    }

    /**
     * Generate PDF window sticker
     * TODO: Implement PDF generation library (TCPDF/FPDF)
     */
    private function generateWindowStickerPDF($vehicle)
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryController: Generating PDF for vehicle: " . $vehicle->id);

        // TODO: Implement actual PDF generation
        // For now, return a simple PDF header
        $pdfContent = "%PDF-1.4\n";
        $pdfContent .= "1 0 obj\n";
        $pdfContent .= "<<\n";
        $pdfContent .= "/Type /Catalog\n";
        $pdfContent .= "/Pages 2 0 R\n";
        $pdfContent .= ">>\n";
        $pdfContent .= "endobj\n";
        $pdfContent .= "Window Sticker for " . $vehicle->name . " - TODO: Implement full PDF generation";

        return $pdfContent;
    }

    /**
     * Send JSON response with proper headers
     */
    private function sendJSONResponse($data, $httpCode = 200)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
} 