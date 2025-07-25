<?php
/**
 * SuiteCRM F&I Deal Center - F&I Product Selection View
 * 
 * This view provides product selection functionality for F&I deals,
 * including product recommendations, pricing, and profit calculations.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_FIDealsViewProduct_selection extends SugarView
{
    public $type = 'product_selection';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $GLOBALS['log']->debug("F&I Deal Center: Product selection view initialized");
    }
    
    /**
     * Preprocess the view
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        // Check if user has access to this module
        if (!ACLController::checkAccess('DM_FIDeals', 'edit', true)) {
            ACLController::displayNoAccess(true);
            sugar_cleanup(true);
        }
    }
    
    /**
     * Display the product selection interface
     */
    public function display()
    {
        global $mod_strings, $app_strings, $current_user;
        
        $GLOBALS['log']->debug("F&I Deal Center: Displaying product selection view");
        
        // Get the deal ID from request
        $dealId = $_REQUEST['record'] ?? '';
        
        if (empty($dealId)) {
            $this->displayError('Deal ID is required for product selection');
            return;
        }
        
        // Load the deal
        $deal = BeanFactory::getBean('DM_FIDeals', $dealId);
        if (!$deal || $deal->deleted) {
            $this->displayError('Deal not found or has been deleted');
            return;
        }
        
        // Handle form submission for product updates
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_products'])) {
            $this->handleProductUpdate($deal);
        }
        
        // Load F&I Product class
        require_once('modules/DM_FIDeals/FIProduct.php');
        
        // Get product catalog and recommendations
        $productCatalog = FIProduct::getDefaultProducts();
        $recommendations = $deal->getFIProductRecommendations();
        $currentProducts = $deal->getSelectedProducts();
        
        // Display the product selection interface
        $this->displayProductInterface($deal, $productCatalog, $recommendations, $currentProducts);
    }
    
    /**
     * Handle product selection updates
     */
    private function handleProductUpdate($deal)
    {
        $GLOBALS['log']->debug("F&I Deal Center: Processing product update for deal: " . $deal->id);
        
        try {
            // Get selected products from form
            $selectedProducts = array();
            
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'product_') === 0 && !empty($value)) {
                    $productType = str_replace('product_', '', $key);
                    $tier = $_POST['tier_' . $productType] ?? 'basic';
                    $selectedProducts[$productType] = $tier;
                }
            }
            
            // Calculate products using the new system
            require_once('modules/DM_FIDeals/FIProduct.php');
            
            if (!empty($selectedProducts)) {
                $productCalculation = FIProduct::calculateDealProducts(
                    $selectedProducts,
                    (float)$deal->sales_price,
                    (int)$deal->term_months
                );
                
                // Update deal fields based on calculations
                $this->updateDealFromProducts($deal, $productCalculation);
            } else {
                // Clear all product fields
                $deal->warranty_total = 0.00;
                $deal->gap_amount = 0.00;
                $deal->etch_amount = 0.00;
                $deal->maintenance_amount = 0.00;
                $deal->other_products_total = 0.00;
                $deal->fi_product_cost = 0.00;
                $deal->fi_product_profit = 0.00;
                $deal->fi_product_commission = 0.00;
            }
            
            // Recalculate deal totals
            $deal->recalculateAll();
            
            // Save the deal
            $deal->save();
            
            // Show success message
            echo '<div class="alert alert-success" style="margin: 20px;">Products updated successfully!</div>';
            
            $GLOBALS['log']->debug("F&I Deal Center: Product update completed successfully");
            
        } catch (Exception $e) {
            $errorMessage = "Error updating products: " . $e->getMessage();
            echo '<div class="alert alert-danger" style="margin: 20px;">' . htmlspecialchars($errorMessage) . '</div>';
            $GLOBALS['log']->error("F&I Deal Center: " . $errorMessage);
        }
    }
    
    /**
     * Update deal fields from product calculations
     */
    private function updateDealFromProducts($deal, $productCalculation)
    {
        // Reset product fields
        $deal->warranty_total = 0.00;
        $deal->gap_amount = 0.00;
        $deal->etch_amount = 0.00;
        $deal->maintenance_amount = 0.00;
        $deal->other_products_total = 0.00;
        
        // Update based on selected products
        foreach ($productCalculation['products'] as $productType => $details) {
            switch ($productType) {
                case 'extended_warranty':
                    $deal->warranty_total = $details['price'];
                    break;
                case 'gap_insurance':
                    $deal->gap_amount = $details['price'];
                    break;
                case 'theft_protection':
                    $deal->etch_amount = $details['price'];
                    break;
                case 'maintenance_package':
                    $deal->maintenance_amount = $details['price'];
                    break;
                default:
                    $deal->other_products_total += $details['price'];
                    break;
            }
        }
        
        // Store calculation details
        $deal->fi_product_cost = $productCalculation['total_cost'];
        $deal->fi_product_profit = $productCalculation['total_profit'];
        $deal->fi_product_commission = $productCalculation['total_commission'];
        $deal->fi_product_details = json_encode($productCalculation);
    }
    
    /**
     * Display the product selection interface
     */
    private function displayProductInterface($deal, $productCatalog, $recommendations, $currentProducts)
    {
        echo '<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">';
        
        // Page header
        echo '<div style="border-bottom: 2px solid #ddd; padding-bottom: 15px; margin-bottom: 20px;">';
        echo '<h2>F&I Product Selection - Deal ' . htmlspecialchars($deal->deal_number) . '</h2>';
        echo '<div style="color: #666;">';
        echo 'Customer: ' . htmlspecialchars($deal->customer_name ?? 'N/A') . ' | ';
        echo 'Vehicle Price: $' . number_format($deal->sales_price, 2) . ' | ';
        echo 'Finance Method: ' . htmlspecialchars($deal->finance_method);
        echo '</div>';
        echo '</div>';
        
        echo '<form method="POST" action="index.php?module=DM_FIDeals&action=product_selection&record=' . $deal->id . '">';
        echo '<input type="hidden" name="update_products" value="1">';
        
        // Two-column layout
        echo '<div style="display: flex; gap: 20px;">';
        
        // Left column - Product selection
        echo '<div style="flex: 2;">';
        echo '<h3>Available F&I Products</h3>';
        
        // Group products by category
        $categories = array();
        foreach ($productCatalog as $productKey => $product) {
            $categories[$product['category']][$productKey] = $product;
        }
        
        foreach ($categories as $categoryName => $categoryProducts) {
            echo '<div style="background: #f9f9f9; padding: 15px; margin-bottom: 20px; border-radius: 5px;">';
            echo '<h4 style="margin-top: 0; color: #333;">' . htmlspecialchars($categoryName) . '</h4>';
            
            foreach ($categoryProducts as $productKey => $product) {
                $isSelected = isset($currentProducts[$productKey]);
                $currentTier = $isSelected ? $currentProducts[$productKey] : '';
                $isRecommended = isset($recommendations[$productKey]);
                
                echo '<div style="background: #fff; padding: 15px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 3px;">';
                
                // Product header with checkbox
                echo '<div style="display: flex; align-items: center; margin-bottom: 10px;">';
                echo '<input type="checkbox" name="product_' . $productKey . '" value="1" id="product_' . $productKey . '"';
                if ($isSelected) echo ' checked';
                echo ' onchange="toggleProductOptions(\'' . $productKey . '\')">';
                echo '<label for="product_' . $productKey . '" style="margin-left: 10px; font-weight: bold; cursor: pointer;">';
                echo htmlspecialchars($product['name']);
                if ($isRecommended) {
                    $priority = $recommendations[$productKey]['priority'];
                    $color = $priority === 'high' ? '#e74c3c' : '#f39c12';
                    echo ' <span style="color: ' . $color . '; font-size: 12px;">(' . strtoupper($priority) . ' PRIORITY)</span>';
                }
                echo '</label>';
                echo '</div>';
                
                // Product description
                echo '<div style="color: #666; margin-bottom: 10px; font-size: 14px;">';
                echo htmlspecialchars($product['description']);
                if ($isRecommended) {
                    echo '<br><em>Recommended: ' . htmlspecialchars($recommendations[$productKey]['reason']) . '</em>';
                }
                echo '</div>';
                
                // Tier selection
                echo '<div id="tiers_' . $productKey . '" style="' . ($isSelected ? '' : 'display: none;') . '">';
                echo '<label style="font-weight: bold; margin-bottom: 5px; display: block;">Coverage Level:</label>';
                echo '<select name="tier_' . $productKey . '" style="width: 200px; padding: 5px;" onchange="updateProductPricing(\'' . $productKey . '\')">';
                
                foreach ($product['pricing_tiers'] as $tierKey => $tierData) {
                    $selected = ($tierKey === $currentTier) ? 'selected' : '';
                    echo '<option value="' . $tierKey . '" ' . $selected . '>';
                    echo htmlspecialchars(ucwords(str_replace('_', ' ', $tierKey))) . ' - $' . number_format($tierData['price'], 2);
                    echo '</option>';
                }
                echo '</select>';
                
                // Pricing display
                echo '<div id="pricing_' . $productKey . '" style="margin-top: 10px; font-size: 13px; color: #333;">';
                if ($isSelected && !empty($currentTier) && isset($product['pricing_tiers'][$currentTier])) {
                    $tierData = $product['pricing_tiers'][$currentTier];
                    echo 'Cost: $' . number_format($tierData['cost'], 2) . ' | ';
                    echo 'Price: $' . number_format($tierData['price'], 2) . ' | ';
                    echo 'Profit: $' . number_format($tierData['price'] - $tierData['cost'], 2) . ' | ';
                    echo 'Commission: $' . number_format($tierData['price'] * ($product['commission_percentage'] / 100), 2);
                }
                echo '</div>';
                echo '</div>';
                
                echo '</div>'; // Close product box
            }
            
            echo '</div>'; // Close category
        }
        
        echo '</div>'; // Close left column
        
        // Right column - Summary and totals
        echo '<div style="flex: 1;">';
        
        // Current totals
        echo '<div style="background: #f0f8ff; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
        echo '<h3>Current Totals</h3>';
        
        $currentTotal = (float)$deal->warranty_total + (float)$deal->gap_amount + 
                       (float)$deal->etch_amount + (float)$deal->maintenance_amount + 
                       (float)$deal->other_products_total;
        
        echo '<div><strong>Total Products:</strong> $' . number_format($currentTotal, 2) . '</div>';
        if (!empty($deal->fi_product_cost)) {
            echo '<div><strong>Total Cost:</strong> $' . number_format($deal->fi_product_cost, 2) . '</div>';
            echo '<div><strong>Total Profit:</strong> $' . number_format($deal->fi_product_profit, 2) . '</div>';
            echo '<div><strong>Total Commission:</strong> $' . number_format($deal->fi_product_commission, 2) . '</div>';
            $margin = $deal->fi_product_cost > 0 ? (($deal->fi_product_profit / $deal->fi_product_cost) * 100) : 0;
            echo '<div><strong>Profit Margin:</strong> ' . number_format($margin, 1) . '%</div>';
        }
        echo '</div>';
        
        // Impact on deal
        echo '<div style="background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
        echo '<h3>Impact on Deal</h3>';
        echo '<div><strong>Current Backend Gross:</strong> $' . number_format($deal->backend_gross, 2) . '</div>';
        echo '<div><strong>Current Total Gross:</strong> $' . number_format($deal->total_gross, 2) . '</div>';
        echo '<div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">';
        echo '<div><strong>Amount Financed:</strong> $' . number_format($deal->amount_financed, 2) . '</div>';
        echo '<div><strong>Monthly Payment:</strong> $' . number_format($deal->monthly_payment, 2) . '</div>';
        echo '</div>';
        echo '</div>';
        
        // Action buttons
        echo '<div style="text-align: center;">';
        echo '<button type="submit" style="background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 3px; cursor: pointer; margin-right: 10px;">Update Products</button>';
        echo '<a href="index.php?module=DM_FIDeals&action=DetailView&record=' . $deal->id . '" style="background: #666; color: white; padding: 12px 24px; text-decoration: none; border-radius: 3px;">Back to Deal</a>';
        echo '</div>';
        
        echo '</div>'; // Close right column
        echo '</div>'; // Close two-column layout
        
        echo '</form>';
        echo '</div>'; // Close main container
        
        // Add JavaScript for dynamic interactions
        $this->addProductSelectionJavaScript();
        
        $GLOBALS['log']->debug("F&I Deal Center: Product selection interface displayed successfully");
    }
    
    /**
     * Add JavaScript for product selection interactions
     */
    private function addProductSelectionJavaScript()
    {
        echo '<script type="text/javascript">
            function toggleProductOptions(productKey) {
                var checkbox = document.getElementById("product_" + productKey);
                var tiersDiv = document.getElementById("tiers_" + productKey);
                
                if (checkbox.checked) {
                    tiersDiv.style.display = "block";
                    updateProductPricing(productKey);
                } else {
                    tiersDiv.style.display = "none";
                    var pricingDiv = document.getElementById("pricing_" + productKey);
                    pricingDiv.innerHTML = "";
                }
            }
            
            function updateProductPricing(productKey) {
                // For MVP, pricing is pre-calculated in PHP
                // In a full implementation, this would make AJAX calls
                console.log("F&I Deal Center: Product pricing updated for " + productKey);
            }
            
            // Initialize product display on page load
            document.addEventListener("DOMContentLoaded", function() {
                var checkboxes = document.querySelectorAll("input[type=checkbox][name^=product_]");
                checkboxes.forEach(function(checkbox) {
                    var productKey = checkbox.name.replace("product_", "");
                    toggleProductOptions(productKey);
                });
            });
        </script>';
    }
    
    /**
     * Display error message
     */
    private function displayError($message)
    {
        echo '<div class="alert alert-danger" style="margin: 20px; padding: 15px;">';
        echo '<h3>Error</h3>';
        echo '<p>' . htmlspecialchars($message) . '</p>';
        echo '<a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>';
        echo '</div>';
        
        $GLOBALS['log']->error("F&I Deal Center: Product selection error - " . $message);
    }
} 