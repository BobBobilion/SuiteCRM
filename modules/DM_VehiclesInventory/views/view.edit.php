<?php
/**
 * SuiteCRM Vehicle Inventory System - Edit View
 *
 * This view handles the creation and editing of vehicle inventory records
 * with enhanced functionality for VIN decoding and photo management.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/views/view.edit.php');

/**
 * Vehicle Inventory Edit View Class
 *
 * Provides enhanced edit functionality with VIN decoding, photo uploads,
 * and automotive-specific form enhancements.
 */
class DM_VehiclesInventoryViewEdit extends ViewEdit
{
    /**
     * Constructor - Initialize edit view with custom settings
     */
    public function __construct()
    {
        parent::__construct();
        
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewEdit: Initializing vehicle inventory edit view");
    }

    /**
     * Pre-display setup and customizations
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewEdit: Pre-display setup");
        
        // Add custom JavaScript for enhanced edit functionality
        $this->addEditViewJavaScript();
        
        // Setup form enhancements
        $this->setupFormEnhancements();
    }

    /**
     * Display the edit view with enhancements
     */
    public function display()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewEdit: Displaying vehicle inventory edit form");
        
        // Add custom CSS for edit form styling
        echo '<style>';
        echo $this->getCustomEditCSS();
        echo '</style>';
        
        // Display standard edit view
        parent::display();
        
        // Add custom functionality after form
        echo $this->getVINDecoderModal();
        echo $this->getEditEnhancementScript();
    }

    /**
     * Setup form enhancements and validations
     */
    private function setupFormEnhancements()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewEdit: Setting up form enhancements");
        
        // Add VIN decoder button if VIN field exists
        $this->ss->assign('VIN_DECODER_ENABLED', true);
        
        // Add photo upload capabilities
        $this->ss->assign('PHOTO_UPLOAD_ENABLED', true);
        
        // Add feature management
        $this->ss->assign('FEATURE_MANAGEMENT_ENABLED', true);
    }

    /**
     * Add custom JavaScript for edit view enhancements
     */
    private function addEditViewJavaScript()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewEdit: Adding custom JavaScript");
        
        // Add scripts to page
        echo '<script src="modules/DM_VehiclesInventory/js/editview.js"></script>';
    }

    /**
     * Get custom CSS for edit form styling
     */
    private function getCustomEditCSS()
    {
        return '
        .vehicle-edit-form {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .form-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .section-title {
            color: #495057;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        
        .vin-decoder-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .vin-decode-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .vin-decode-btn:hover {
            background: #0056b3;
        }
        
        .field-group {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .field-group .form-field {
            flex: 1;
        }
        
        .required-field {
            border-left: 3px solid #dc3545;
            padding-left: 10px;
        }
        
        .auto-calculated {
            background-color: #e9ecef;
            color: #6c757d;
            font-style: italic;
        }
        
        .photo-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .photo-preview-item {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .photo-preview-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
        }
        
        .photo-remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            cursor: pointer;
        }
        
        .validation-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .success-message {
            color: #28a745;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .feature-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }
        
        .feature-tag {
            background: #007bff;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        ';
    }

    /**
     * Get VIN decoder modal HTML
     */
    private function getVINDecoderModal()
    {
        global $mod_strings;
        
        return '
        <div id="vinDecoderModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">' . $mod_strings['LBL_VIN_DECODER'] . '</h4>
                    </div>
                    <div class="modal-body">
                        <div class="vin-decoder-form">
                            <div class="form-group">
                                <label>' . $mod_strings['LBL_VIN'] . '</label>
                                <input type="text" class="form-control" id="vinToDecodeInput" placeholder="Enter 17-character VIN" maxlength="17">
                                <div class="validation-error" id="vinValidationError" style="display: none;"></div>
                            </div>
                            
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" onclick="decodeVIN()" id="decodeVINBtn">
                                    <i class="fa fa-cog fa-spin" style="display: none;" id="decodeSpinner"></i>
                                    ' . $mod_strings['LBL_DECODE_VIN'] . '
                                </button>
                            </div>
                        </div>
                        
                        <div id="vinDecodeResults" style="display: none; margin-top: 20px;">
                            <h5>' . $mod_strings['LBL_DECODED_INFORMATION'] . '</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="decode-result-item">
                                        <label>' . $mod_strings['LBL_YEAR'] . ':</label>
                                        <span id="decodedYear"></span>
                                    </div>
                                    <div class="decode-result-item">
                                        <label>' . $mod_strings['LBL_MAKE'] . ':</label>
                                        <span id="decodedMake"></span>
                                    </div>
                                    <div class="decode-result-item">
                                        <label>' . $mod_strings['LBL_MODEL'] . ':</label>
                                        <span id="decodedModel"></span>
                                    </div>
                                    <div class="decode-result-item">
                                        <label>' . $mod_strings['LBL_TRIM'] . ':</label>
                                        <span id="decodedTrim"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="decode-result-item">
                                        <label>' . $mod_strings['LBL_BODY_STYLE'] . ':</label>
                                        <span id="decodedBodyStyle"></span>
                                    </div>
                                    <div class="decode-result-item">
                                        <label>' . $mod_strings['LBL_ENGINE'] . ':</label>
                                        <span id="decodedEngine"></span>
                                    </div>
                                    <div class="decode-result-item">
                                        <label>' . $mod_strings['LBL_TRANSMISSION'] . ':</label>
                                        <span id="decodedTransmission"></span>
                                    </div>
                                    <div class="decode-result-item">
                                        <label>' . $mod_strings['LBL_DRIVETRAIN'] . ':</label>
                                        <span id="decodedDrivetrain"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">' . $mod_strings['LBL_CANCEL'] . '</button>
                        <button type="button" class="btn btn-success" onclick="applyDecodedData()" id="applyDataBtn" style="display: none;">
                            ' . $mod_strings['LBL_APPLY_DATA'] . '
                        </button>
                    </div>
                </div>
            </div>
        </div>';
    }

    /**
     * Get JavaScript for edit view enhancements
     */
    private function getEditEnhancementScript()
    {
        return '
        <script>
        var decodedVehicleData = null;
        
        $(document).ready(function() {
            // Initialize edit view enhancements
            initializeVehicleEditView();
        });
        
        function initializeVehicleEditView() {
            console.log("Initializing vehicle edit view enhancements");
            
            // Setup VIN field enhancements
            setupVINField();
            
            // Setup form validations
            setupFormValidations();
            
            // Setup auto-calculations
            setupAutoCalculations();
            
            // Setup duplicate checking
            setupDuplicateChecking();
        }
        
        function setupVINField() {
            var vinField = $("#vin");
            if (vinField.length > 0) {
                // Add VIN decoder button
                vinField.parent().addClass("vin-decoder-container");
                vinField.after("<button type=\"button\" class=\"vin-decode-btn\" onclick=\"openVINDecoder()\">Decode</button>");
                
                // Add VIN validation
                vinField.on("input", function() {
                    validateVIN($(this).val());
                });
                
                // Add duplicate checking
                vinField.on("blur", function() {
                    checkVINDuplicate($(this).val());
                });
            }
        }
        
        function validateVIN(vin) {
            var isValid = true;
            var errorMessage = "";
            
            if (vin.length > 0) {
                if (vin.length !== 17) {
                    isValid = false;
                    errorMessage = "VIN must be exactly 17 characters";
                } else if (/[IOQ]/i.test(vin)) {
                    isValid = false;
                    errorMessage = "VIN cannot contain letters I, O, or Q";
                }
            }
            
            var vinField = $("#vin");
            var errorDiv = vinField.next(".validation-error");
            
            if (!isValid && errorMessage) {
                if (errorDiv.length === 0) {
                    vinField.after("<div class=\"validation-error\">" + errorMessage + "</div>");
                } else {
                    errorDiv.text(errorMessage).show();
                }
                vinField.addClass("error");
            } else {
                errorDiv.hide();
                vinField.removeClass("error");
            }
            
            return isValid;
        }
        
        function checkVINDuplicate(vin) {
            if (!vin || vin.length !== 17) return;
            
            var recordId = $("#id").val() || "";
            
            $.ajax({
                url: "index.php?module=DM_VehiclesInventory&action=duplicatecheck",
                method: "POST",
                data: {
                    vin: vin,
                    exclude_id: recordId
                },
                success: function(response) {
                    if (response.success && response.data.is_duplicate) {
                        alert("Warning: This VIN already exists for vehicle: " + response.data.existing_vehicle.name + " (Stock: " + response.data.existing_vehicle.stock_number + ")");
                    }
                },
                error: function() {
                    console.error("Error checking VIN duplicate");
                }
            });
        }
        
        function setupFormValidations() {
            $("#EditView").on("submit", function(e) {
                var isValid = true;
                
                // Validate required fields
                $(".required-field input, .required-field select").each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass("error");
                    } else {
                        $(this).removeClass("error");
                    }
                });
                
                // Validate VIN
                var vinValue = $("#vin").val();
                if (vinValue && !validateVIN(vinValue)) {
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    alert("Please correct the highlighted errors before saving.");
                }
            });
        }
        
        function setupAutoCalculations() {
            // Auto-calculate days on lot when purchase date changes
            $("#purchase_date").on("change", function() {
                calculateDaysOnLot();
            });
            
            // Auto-generate display name when key fields change
            $("#year, #make, #model").on("change", function() {
                generateDisplayName();
            });
        }
        
        function calculateDaysOnLot() {
            var purchaseDate = $("#purchase_date").val();
            if (purchaseDate) {
                var today = new Date();
                var purchase = new Date(purchaseDate);
                var timeDiff = today.getTime() - purchase.getTime();
                var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                
                $("#days_on_lot").val(daysDiff).addClass("auto-calculated");
            }
        }
        
        function generateDisplayName() {
            var year = $("#year").val();
            var make = $("#make").val();
            var model = $("#model").val();
            
            if (year && make && model) {
                var displayName = year + " " + make + " " + model;
                $("#name").val(displayName).addClass("auto-calculated");
            }
        }
        
        function setupDuplicateChecking() {
            $("#stock_number").on("blur", function() {
                var stockNumber = $(this).val();
                if (stockNumber) {
                    // TODO: Check for duplicate stock numbers
                    console.log("Checking stock number:", stockNumber);
                }
            });
        }
        
        function openVINDecoder() {
            var currentVIN = $("#vin").val();
            if (currentVIN) {
                $("#vinToDecodeInput").val(currentVIN);
            }
            $("#vinDecoderModal").modal("show");
        }
        
        function decodeVIN() {
            var vin = $("#vinToDecodeInput").val().trim().toUpperCase();
            
            if (!validateVINInput(vin)) {
                return;
            }
            
            $("#decodeSpinner").show();
            $("#decodeVINBtn").prop("disabled", true);
            
            $.ajax({
                url: "index.php?module=DM_VehiclesInventory&action=decodevin",
                method: "POST",
                data: { vin: vin },
                success: function(response) {
                    $("#decodeSpinner").hide();
                    $("#decodeVINBtn").prop("disabled", false);
                    
                    if (response.success) {
                        displayDecodedResults(response.data);
                        decodedVehicleData = response.data;
                    } else {
                        alert("Error decoding VIN: " + response.message);
                    }
                },
                error: function() {
                    $("#decodeSpinner").hide();
                    $("#decodeVINBtn").prop("disabled", false);
                    alert("Error connecting to VIN decoder service");
                }
            });
        }
        
        function validateVINInput(vin) {
            var errorElement = $("#vinValidationError");
            
            if (!vin) {
                errorElement.text("Please enter a VIN").show();
                return false;
            }
            
            if (vin.length !== 17) {
                errorElement.text("VIN must be exactly 17 characters").show();
                return false;
            }
            
            if (/[IOQ]/.test(vin)) {
                errorElement.text("VIN cannot contain letters I, O, or Q").show();
                return false;
            }
            
            errorElement.hide();
            return true;
        }
        
        function displayDecodedResults(data) {
            $("#decodedYear").text(data.year || "N/A");
            $("#decodedMake").text(data.make || "N/A");
            $("#decodedModel").text(data.model || "N/A");
            $("#decodedTrim").text(data.trim || "N/A");
            $("#decodedBodyStyle").text(data.body_style || "N/A");
            $("#decodedEngine").text(data.engine_type || "N/A");
            $("#decodedTransmission").text(data.transmission || "N/A");
            $("#decodedDrivetrain").text(data.drivetrain || "N/A");
            
            $("#vinDecodeResults").show();
            $("#applyDataBtn").show();
        }
        
        function applyDecodedData() {
            if (!decodedVehicleData) return;
            
            // Apply decoded data to form fields
            if (decodedVehicleData.year) $("#year").val(decodedVehicleData.year);
            if (decodedVehicleData.make) $("#make").val(decodedVehicleData.make);
            if (decodedVehicleData.model) $("#model").val(decodedVehicleData.model);
            if (decodedVehicleData.trim) $("#trim").val(decodedVehicleData.trim);
            if (decodedVehicleData.body_style) $("#body_style").val(decodedVehicleData.body_style);
            if (decodedVehicleData.engine_type) $("#engine_type").val(decodedVehicleData.engine_type);
            if (decodedVehicleData.transmission) $("#transmission").val(decodedVehicleData.transmission);
            if (decodedVehicleData.drivetrain) $("#drivetrain").val(decodedVehicleData.drivetrain);
            if (decodedVehicleData.fuel_type) $("#fuel_type").val(decodedVehicleData.fuel_type);
            
            // Auto-generate display name
            generateDisplayName();
            
            // Close modal
            $("#vinDecoderModal").modal("hide");
            
            // Show success message
            alert("Vehicle information populated from VIN decoder successfully!");
        }
        </script>';
    }
} 