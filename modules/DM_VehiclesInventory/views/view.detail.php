<?php
/**
 * SuiteCRM Vehicle Inventory System - Detail View
 *
 * This view handles the display of individual vehicle inventory records
 * with enhanced functionality for photo galleries and feature management.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/views/view.detail.php');

/**
 * Vehicle Inventory Detail View Class
 *
 * Provides enhanced detail view functionality with photo galleries,
 * feature displays, and automotive-specific information presentation.
 */
class DM_VehiclesInventoryViewDetail extends ViewDetail
{
    /**
     * Constructor - Initialize detail view with custom settings
     */
    public function __construct()
    {
        parent::__construct();
        
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewDetail: Initializing vehicle inventory detail view");
    }

    /**
     * Pre-display setup and customizations
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewDetail: Pre-display setup");
        
        // Add custom JavaScript for enhanced detail functionality
        $this->addDetailViewJavaScript();
        
        // Setup detail view enhancements
        $this->setupDetailEnhancements();
    }

    /**
     * Display the detail view with enhancements
     */
    public function display()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewDetail: Displaying vehicle inventory detail");
        
        // Add custom CSS for detail view styling
        echo '<style>';
        echo $this->getCustomDetailCSS();
        echo '</style>';
        
        // Display standard detail view
        parent::display();
        
        // Add custom functionality after detail view
        echo $this->getDetailEnhancementScript();
    }

    /**
     * Setup detail view enhancements
     */
    private function setupDetailEnhancements()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewDetail: Setting up detail enhancements");
        
        // Add photo gallery data
        $this->ss->assign('PHOTO_GALLERY_ENABLED', true);
        
        // Add features display data
        $this->ss->assign('FEATURES_DISPLAY_ENABLED', true);
        
        // Add action buttons
        $this->ss->assign('CUSTOM_ACTIONS_ENABLED', true);
        
        // Add vehicle statistics
        $this->addVehicleStatistics();
    }

    /**
     * Add vehicle-specific statistics and calculations
     */
    private function addVehicleStatistics()
    {
        if (!$this->bean || empty($this->bean->id)) {
            return;
        }

        $GLOBALS['log']->debug("DM_VehiclesInventoryViewDetail: Adding vehicle statistics for vehicle: " . $this->bean->id);

        $statistics = array();

        try {
            // Calculate profit margin if both purchase and list price are available
            if (!empty($this->bean->purchase_price) && !empty($this->bean->list_price)) {
                $profitAmount = $this->bean->list_price - $this->bean->purchase_price;
                $profitMargin = ($profitAmount / $this->bean->purchase_price) * 100;
                $statistics['profit_margin'] = round($profitMargin, 2);
                $statistics['profit_amount'] = $profitAmount;
            }

            // Determine aging category
            $daysOnLot = (int)$this->bean->days_on_lot;
            if ($daysOnLot <= 30) {
                $statistics['aging_category'] = 'fresh';
                $statistics['aging_class'] = 'success';
            } elseif ($daysOnLot <= 60) {
                $statistics['aging_category'] = 'aging';
                $statistics['aging_class'] = 'warning';
            } else {
                $statistics['aging_category'] = 'stale';
                $statistics['aging_class'] = 'danger';
            }

            // Count photos
            $photos = json_decode($this->bean->photos ?: '[]', true);
            $statistics['photo_count'] = count($photos);

            // Count features
            $features = json_decode($this->bean->features ?: '[]', true);
            $statistics['feature_count'] = count($features);

            // Market value comparison
            if (!empty($this->bean->market_value) && !empty($this->bean->list_price)) {
                $marketComparison = (($this->bean->list_price - $this->bean->market_value) / $this->bean->market_value) * 100;
                $statistics['market_comparison'] = round($marketComparison, 2);
            }

        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_VehiclesInventoryViewDetail: Error calculating statistics: " . $e->getMessage());
        }

        $this->ss->assign('VEHICLE_STATISTICS', $statistics);
    }

    /**
     * Add custom JavaScript for detail view enhancements
     */
    private function addDetailViewJavaScript()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewDetail: Adding custom JavaScript");
        
        // Add scripts to page
        echo '<script src="modules/DM_VehiclesInventory/js/detailview.js"></script>';
    }

    /**
     * Get custom CSS for detail view styling
     */
    private function getCustomDetailCSS()
    {
        return '
        .vehicle-detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .vehicle-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .vehicle-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .vehicle-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-card.success {
            border-left: 4px solid #28a745;
        }
        
        .stat-card.warning {
            border-left: 4px solid #ffc107;
        }
        
        .stat-card.danger {
            border-left: 4px solid #dc3545;
        }
        
        .stat-card.info {
            border-left: 4px solid #17a2b8;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #495057;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        .action-buttons-bar {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .action-btn {
            margin: 5px;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .action-btn.primary {
            background: #007bff;
            color: white;
        }
        
        .action-btn.success {
            background: #28a745;
            color: white;
        }
        
        .action-btn.warning {
            background: #ffc107;
            color: #212529;
        }
        
        .action-btn.danger {
            background: #dc3545;
            color: white;
        }
        
        .vehicle-photos-section {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .section-header {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        
        .vehicle-features-section {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .pricing-timeline {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .timeline-date {
            font-weight: bold;
            color: #495057;
            width: 120px;
        }
        
        .timeline-event {
            flex: 1;
            color: #6c757d;
        }
        
        .timeline-value {
            font-weight: bold;
            color: #007bff;
        }
        
        .vehicle-qr-code {
            text-align: center;
            padding: 20px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .qr-code-img {
            border: 1px solid #ddd;
            padding: 10px;
            background: white;
        }
        ';
    }

    /**
     * Get JavaScript for detail view enhancements
     */
    private function getDetailEnhancementScript()
    {
        global $mod_strings;
        
        return '
        <script>
        $(document).ready(function() {
            // Initialize detail view enhancements
            initializeVehicleDetailView();
        });
        
        function initializeVehicleDetailView() {
            console.log("Initializing vehicle detail view enhancements");
            
            // Setup action buttons
            setupActionButtons();
            
            // Setup photo gallery
            setupPhotoGallery();
            
            // Setup features display
            setupFeaturesDisplay();
            
            // Setup print functionality
            setupPrintFunctionality();
        }
        
        function setupActionButtons() {
            // Mark as Sold button
            $(".mark-sold-btn").click(function() {
                var vehicleId = $(this).data("vehicle-id");
                var vehicleName = $(this).data("vehicle-name");
                
                if (confirm("Are you sure you want to mark \"" + vehicleName + "\" as sold?")) {
                    markVehicleSold(vehicleId);
                }
            });
            
            // Update Pricing button
            $(".update-pricing-btn").click(function() {
                var vehicleId = $(this).data("vehicle-id");
                openPricingModal(vehicleId);
            });
            
            // Print Window Sticker button
            $(".print-sticker-btn").click(function() {
                var vehicleId = $(this).data("vehicle-id");
                printWindowSticker(vehicleId);
            });
            
            // Generate QR Code button
            $(".generate-qr-btn").click(function() {
                var vehicleId = $(this).data("vehicle-id");
                generateQRCode(vehicleId);
            });
        }
        
        function setupPhotoGallery() {
            console.log("Setting up photo gallery functionality");
            // Photo gallery is handled by PhotoGallery.tpl template
        }
        
        function setupFeaturesDisplay() {
            console.log("Setting up features display functionality");
            // Features display is handled by FeaturesList.tpl template
        }
        
        function setupPrintFunctionality() {
            // Custom print styling for detail view
            $(".print-detail-btn").click(function() {
                window.print();
            });
        }
        
        function markVehicleSold(vehicleId) {
            console.log("Marking vehicle as sold:", vehicleId);
            
            $.ajax({
                url: "index.php?module=DM_VehiclesInventory&action=bulkstatus",
                method: "POST",
                data: {
                    vehicle_ids: [vehicleId],
                    status: "Sold"
                },
                success: function(response) {
                    if (response.success) {
                        alert("Vehicle marked as sold successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function() {
                    alert("Error marking vehicle as sold");
                }
            });
        }
        
        function openPricingModal(vehicleId) {
            console.log("Opening pricing modal for vehicle:", vehicleId);
            // TODO: Implement pricing update modal
            alert("Pricing update functionality coming soon!");
        }
        
        function printWindowSticker(vehicleId) {
            console.log("Printing window sticker for vehicle:", vehicleId);
            
            // Open window sticker in new window/tab
            window.open("index.php?module=DM_VehiclesInventory&action=printwindowsticker&vehicle_id=" + vehicleId, "_blank");
        }
        
        function generateQRCode(vehicleId) {
            console.log("Generating QR code for vehicle:", vehicleId);
            
            // Generate QR code for vehicle detail page URL
            var vehicleUrl = window.location.origin + "/index.php?module=DM_VehiclesInventory&action=DetailView&record=" + vehicleId;
            var qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" + encodeURIComponent(vehicleUrl);
            
            // Display QR code in modal or update existing QR code section
            $("#vehicleQRCode").html("<img src=\"" + qrCodeUrl + "\" alt=\"Vehicle QR Code\" class=\"qr-code-img\">");
            
            // Show success message
            alert("QR Code generated successfully! Scan to view this vehicle on mobile devices.");
        }
        
        // Utility functions for enhanced detail view
        function formatCurrency(amount) {
            return new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "USD"
            }).format(amount);
        }
        
        function formatNumber(number) {
            return new Intl.NumberFormat("en-US").format(number);
        }
        
        function getAgingBadgeClass(days) {
            if (days <= 30) return "badge-success";
            if (days <= 60) return "badge-warning";
            return "badge-danger";
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                console.log("Copied to clipboard:", text);
            }, function(err) {
                console.error("Could not copy text: ", err);
            });
        }
        
        function shareVehicle(vehicleId, vehicleName) {
            if (navigator.share) {
                navigator.share({
                    title: vehicleName,
                    text: "Check out this vehicle: " + vehicleName,
                    url: window.location.href
                });
            } else {
                // Fallback to copying URL
                copyToClipboard(window.location.href);
                alert("Vehicle URL copied to clipboard!");
            }
        }
        </script>';
    }
} 