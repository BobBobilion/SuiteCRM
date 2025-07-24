<?php
/**
 * SuiteCRM Vehicle Inventory System - List View
 *
 * This view handles the display of vehicle inventory in list format with
 * advanced filtering, search, and bulk operation capabilities.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/views/view.list.php');

/**
 * Vehicle Inventory List View Class
 *
 * Provides enhanced list view functionality with automotive-specific filters
 * and bulk operations for vehicle inventory management.
 */
class DM_VehiclesInventoryViewList extends ViewList
{
    /**
     * Constructor - Initialize list view with custom settings
     */
    public function __construct()
    {
        parent::__construct();
        
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewList: Initializing vehicle inventory list view");
    }

    /**
     * Pre-display setup and customizations
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewList: Pre-display setup");
        
        // Add custom JavaScript for enhanced list functionality
        $this->addListViewJavaScript();
        
        // Set custom list view options
        $this->setupCustomListOptions();
    }

    /**
     * Display the list view with enhancements
     */
    public function display()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewList: Displaying vehicle inventory list");
        
        // Add custom CSS for vehicle list styling
        echo '<style>';
        echo $this->getCustomListCSS();
        echo '</style>';
        
        // Display standard list view
        parent::display();
        
        // Add custom functionality after list
        echo $this->getBulkActionModal();
        echo $this->getListEnhancementScript();
    }

    /**
     * Setup custom list view options and filters
     */
    private function setupCustomListOptions()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewList: Setting up custom list options");
        
        // Enable mass update for bulk operations
        $this->ss->assign('MASS_UPDATE', true);
        
        // Add custom quick filter buttons
        $this->ss->assign('QUICK_FILTERS', $this->getQuickFilters());
        
        // Add inventory summary stats
        $this->ss->assign('INVENTORY_STATS', $this->getInventoryStats());
    }

    /**
     * Get quick filter buttons for common searches
     */
    private function getQuickFilters()
    {
        global $mod_strings;
        
        return array(
            'available' => array(
                'label' => $mod_strings['LBL_AVAILABLE_VEHICLES'],
                'filter' => 'status="Available"',
                'class' => 'btn-success'
            ),
            'new' => array(
                'label' => $mod_strings['LBL_NEW_VEHICLES'],
                'filter' => 'condition_type="New"',
                'class' => 'btn-info'
            ),
            'aging' => array(
                'label' => $mod_strings['LBL_AGING_INVENTORY'],
                'filter' => 'days_on_lot>30',
                'class' => 'btn-warning'
            ),
            'stale' => array(
                'label' => $mod_strings['LBL_STALE_INVENTORY'],
                'filter' => 'days_on_lot>60',
                'class' => 'btn-danger'
            )
        );
    }

    /**
     * Get inventory summary statistics
     */
    private function getInventoryStats()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewList: Calculating inventory statistics");
        
        $bean = BeanFactory::getBean('DM_VehiclesInventory');
        
        try {
            // Total vehicles
            $totalVehicles = $bean->get_list('', "deleted=0", 0, 1, -1, 0, null, false);
            $totalCount = $totalVehicles['row_count'];
            
            // Available vehicles
            $availableVehicles = $bean->get_list('', "deleted=0 AND status='Available'", 0, 1, -1, 0, null, false);
            $availableCount = $availableVehicles['row_count'];
            
            // Sold vehicles
            $soldVehicles = $bean->get_list('', "deleted=0 AND status='Sold'", 0, 1, -1, 0, null, false);
            $soldCount = $soldVehicles['row_count'];
            
            // Calculate average days on lot
            $avgQuery = "SELECT AVG(days_on_lot) as avg_days FROM dm_vehiclesinventory WHERE deleted=0 AND status='Available'";
            $avgResult = $bean->db->query($avgQuery);
            $avgRow = $bean->db->fetchByAssoc($avgResult);
            $avgDays = round($avgRow['avg_days'] ?? 0, 1);
            
            return array(
                'total' => $totalCount,
                'available' => $availableCount,
                'sold' => $soldCount,
                'avg_days' => $avgDays
            );
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_VehiclesInventoryViewList: Error calculating stats: " . $e->getMessage());
            return array('total' => 0, 'available' => 0, 'sold' => 0, 'avg_days' => 0);
        }
    }

    /**
     * Add custom JavaScript for list view enhancements
     */
    private function addListViewJavaScript()
    {
        $GLOBALS['log']->debug("DM_VehiclesInventoryViewList: Adding custom JavaScript");
        
        // Add scripts to page
        echo '<script src="modules/DM_VehiclesInventory/js/listview.js"></script>';
    }

    /**
     * Get custom CSS for vehicle list styling
     */
    private function getCustomListCSS()
    {
        return '
        .vehicle-list-stats {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #495057;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .quick-filters {
            margin-bottom: 15px;
        }
        
        .quick-filter-btn {
            margin-right: 10px;
            margin-bottom: 5px;
        }
        
        .vehicle-list-row {
            position: relative;
        }
        
        .vehicle-list-row.aging {
            background-color: #fff3cd;
        }
        
        .vehicle-list-row.stale {
            background-color: #f8d7da;
        }
        
        .days-on-lot-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .days-fresh {
            background-color: #d4edda;
            color: #155724;
        }
        
        .days-aging {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .days-stale {
            background-color: #f8d7da;
            color: #721c24;
        }
        ';
    }

    /**
     * Get bulk action modal HTML
     */
    private function getBulkActionModal()
    {
        global $mod_strings;
        
        return '
        <div id="bulkActionModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">' . $mod_strings['LBL_BULK_ACTIONS'] . '</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>' . $mod_strings['LBL_SELECT_ACTION'] . '</label>
                            <select class="form-control" id="bulkActionType">
                                <option value="">' . $mod_strings['LBL_SELECT_ACTION'] . '</option>
                                <option value="status">' . $mod_strings['LBL_UPDATE_STATUS'] . '</option>
                                <option value="delete">' . $mod_strings['LBL_DELETE_SELECTED'] . '</option>
                                <option value="export">' . $mod_strings['LBL_EXPORT_SELECTED'] . '</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="statusUpdateGroup" style="display: none;">
                            <label>' . $mod_strings['LBL_NEW_STATUS'] . '</label>
                            <select class="form-control" id="newStatus">
                                <option value="Available">' . $mod_strings['LBL_STATUS_AVAILABLE'] . '</option>
                                <option value="Sold">' . $mod_strings['LBL_STATUS_SOLD'] . '</option>
                                <option value="Pending">' . $mod_strings['LBL_STATUS_PENDING'] . '</option>
                                <option value="Service">' . $mod_strings['LBL_STATUS_SERVICE'] . '</option>
                            </select>
                        </div>
                        
                        <div class="alert alert-info">
                            <span id="selectedCountText">0 vehicles selected</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">' . $mod_strings['LBL_CANCEL'] . '</button>
                        <button type="button" class="btn btn-primary" onclick="executeBulkAction()">' . $mod_strings['LBL_EXECUTE'] . '</button>
                    </div>
                </div>
            </div>
        </div>';
    }

    /**
     * Get JavaScript for list view enhancements
     */
    private function getListEnhancementScript()
    {
        return '
        <script>
        $(document).ready(function() {
            // Initialize list view enhancements
            initializeVehicleListView();
        });
        
        function initializeVehicleListView() {
            console.log("Initializing vehicle list view enhancements");
            
            // Add days on lot badges
            addDaysOnLotBadges();
            
            // Setup quick filters
            setupQuickFilters();
            
            // Setup bulk actions
            setupBulkActions();
        }
        
        function addDaysOnLotBadges() {
            $("table.list.view tr").each(function() {
                var daysOnLot = $(this).find("td").eq(7).text(); // Adjust column index as needed
                if (daysOnLot && !isNaN(daysOnLot)) {
                    var days = parseInt(daysOnLot);
                    var badgeClass = "days-fresh";
                    var badgeText = days + " days";
                    
                    if (days > 60) {
                        badgeClass = "days-stale";
                        $(this).addClass("stale");
                    } else if (days > 30) {
                        badgeClass = "days-aging";
                        $(this).addClass("aging");
                    }
                    
                    $(this).append("<span class=\"days-on-lot-badge " + badgeClass + "\">" + badgeText + "</span>");
                }
            });
        }
        
        function setupQuickFilters() {
            $(".quick-filter-btn").click(function() {
                var filter = $(this).data("filter");
                applyQuickFilter(filter);
            });
        }
        
        function applyQuickFilter(filter) {
            console.log("Applying quick filter:", filter);
            // TODO: Implement filter application
            window.location.href = "index.php?module=DM_VehiclesInventory&action=index&search_form_only=false&" + filter;
        }
        
        function setupBulkActions() {
            $("#bulkActionType").change(function() {
                var actionType = $(this).val();
                if (actionType === "status") {
                    $("#statusUpdateGroup").show();
                } else {
                    $("#statusUpdateGroup").hide();
                }
            });
        }
        
        function showBulkActions() {
            var selectedCount = $("input[name=\"mass[]\"]:checked").length;
            $("#selectedCountText").text(selectedCount + " vehicles selected");
            $("#bulkActionModal").modal("show");
        }
        
        function executeBulkAction() {
            var actionType = $("#bulkActionType").val();
            var selectedIds = [];
            
            $("input[name=\"mass[]\"]:checked").each(function() {
                selectedIds.push($(this).val());
            });
            
            if (selectedIds.length === 0) {
                alert("Please select vehicles to update");
                return;
            }
            
            if (actionType === "status") {
                var newStatus = $("#newStatus").val();
                executeBulkStatusUpdate(selectedIds, newStatus);
            } else if (actionType === "delete") {
                if (confirm("Are you sure you want to delete " + selectedIds.length + " vehicles?")) {
                    executeBulkDelete(selectedIds);
                }
            }
        }
        
        function executeBulkStatusUpdate(vehicleIds, newStatus) {
            console.log("Executing bulk status update:", vehicleIds, newStatus);
            
            $.ajax({
                url: "index.php?module=DM_VehiclesInventory&action=bulkstatus",
                method: "POST",
                data: {
                    vehicle_ids: vehicleIds,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        alert("Successfully updated " + response.data.updated_count + " vehicles");
                        location.reload();
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function() {
                    alert("Error performing bulk update");
                }
            });
            
            $("#bulkActionModal").modal("hide");
        }
        
        function executeBulkDelete(vehicleIds) {
            console.log("Executing bulk delete:", vehicleIds);
            // TODO: Implement bulk delete functionality
            alert("Bulk delete functionality not yet implemented");
        }
        </script>';
    }
} 