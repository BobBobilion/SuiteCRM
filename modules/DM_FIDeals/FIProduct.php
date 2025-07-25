<?php
/**
 * SuiteCRM F&I Deal Center - F&I Product Management Class
 * 
 * This class handles F&I products like warranties, GAP insurance,
 * protection packages, and other aftermarket products for Phase 4 MVP.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class FIProduct
{
    /**
     * Default F&I product catalog for MVP implementation
     */
    public static function getDefaultProducts()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Loading default F&I product catalog");
        
        return array(
            'extended_warranty' => array(
                'name' => 'Extended Warranty',
                'category' => 'Service Contract',
                'description' => 'Extended service protection beyond manufacturer warranty',
                'base_cost' => 1200.00,
                'markup_percentage' => 40.00,
                'commission_percentage' => 15.00,
                'pricing_tiers' => array(
                    'basic' => array('cost' => 1200.00, 'price' => 1680.00),
                    'premium' => array('cost' => 1800.00, 'price' => 2520.00),
                    'platinum' => array('cost' => 2400.00, 'price' => 3360.00),
                ),
            ),
            'gap_insurance' => array(
                'name' => 'GAP Insurance',
                'category' => 'Insurance',
                'description' => 'Guaranteed Asset Protection for loan/lease coverage',
                'base_cost' => 350.00,
                'markup_percentage' => 60.00,
                'commission_percentage' => 25.00,
                'pricing_tiers' => array(
                    'standard' => array('cost' => 350.00, 'price' => 560.00),
                    'enhanced' => array('cost' => 450.00, 'price' => 720.00),
                ),
            ),
            'paint_protection' => array(
                'name' => 'Paint & Fabric Protection',
                'category' => 'Protection',
                'description' => 'Interior and exterior protection package',
                'base_cost' => 200.00,
                'markup_percentage' => 100.00,
                'commission_percentage' => 20.00,
                'pricing_tiers' => array(
                    'basic' => array('cost' => 200.00, 'price' => 400.00),
                    'premium' => array('cost' => 300.00, 'price' => 600.00),
                ),
            ),
            'theft_protection' => array(
                'name' => 'Theft Protection',
                'category' => 'Protection',
                'description' => 'VIN etching and theft deterrent system',
                'base_cost' => 75.00,
                'markup_percentage' => 200.00,
                'commission_percentage' => 30.00,
                'pricing_tiers' => array(
                    'standard' => array('cost' => 75.00, 'price' => 225.00),
                    'premium' => array('cost' => 125.00, 'price' => 375.00),
                ),
            ),
            'maintenance_package' => array(
                'name' => 'Prepaid Maintenance',
                'category' => 'Service',
                'description' => 'Prepaid maintenance program',
                'base_cost' => 800.00,
                'markup_percentage' => 25.00,
                'commission_percentage' => 10.00,
                'pricing_tiers' => array(
                    '2_year' => array('cost' => 800.00, 'price' => 1000.00),
                    '3_year' => array('cost' => 1200.00, 'price' => 1500.00),
                    '5_year' => array('cost' => 2000.00, 'price' => 2500.00),
                ),
            ),
            'credit_life' => array(
                'name' => 'Credit Life Insurance',
                'category' => 'Insurance',
                'description' => 'Credit life and disability insurance',
                'base_cost' => 400.00,
                'markup_percentage' => 50.00,
                'commission_percentage' => 35.00,
                'pricing_tiers' => array(
                    'life_only' => array('cost' => 400.00, 'price' => 600.00),
                    'life_disability' => array('cost' => 600.00, 'price' => 900.00),
                ),
            ),
            'tire_wheel' => array(
                'name' => 'Tire & Wheel Protection',
                'category' => 'Protection',
                'description' => 'Coverage for tire and wheel damage',
                'base_cost' => 250.00,
                'markup_percentage' => 80.00,
                'commission_percentage' => 25.00,
                'pricing_tiers' => array(
                    'basic' => array('cost' => 250.00, 'price' => 450.00),
                    'comprehensive' => array('cost' => 350.00, 'price' => 630.00),
                ),
            ),
        );
    }
    
    /**
     * Calculate product pricing for a deal
     * 
     * @param string $productType Product type key
     * @param string $tier Pricing tier
     * @param float $vehiclePrice Vehicle price for percentage-based calculations
     * @param int $termMonths Loan term for insurance calculations
     * @return array Product pricing information
     */
    public static function calculateProductPricing($productType, $tier = 'basic', $vehiclePrice = 0, $termMonths = 60)
    {
        $GLOBALS['log']->debug("F&I Deal Center: Calculating pricing for product: $productType, tier: $tier");
        
        $products = self::getDefaultProducts();
        
        if (!isset($products[$productType])) {
            $GLOBALS['log']->error("F&I Deal Center: Product type not found: $productType");
            return null;
        }
        
        $product = $products[$productType];
        
        // Get tier pricing or use base cost
        if (isset($product['pricing_tiers'][$tier])) {
            $tierPricing = $product['pricing_tiers'][$tier];
            $cost = $tierPricing['cost'];
            $price = $tierPricing['price'];
        } else {
            $cost = $product['base_cost'];
            $price = $cost * (1 + ($product['markup_percentage'] / 100));
        }
        
        // Apply vehicle-based adjustments for certain products
        if ($productType === 'gap_insurance' && $vehiclePrice > 0) {
            // GAP pricing often based on vehicle value
            $gapRate = min(0.06, max(0.02, $vehiclePrice / 100000 * 0.04)); // 2-6% of vehicle price
            $adjustedPrice = $vehiclePrice * $gapRate;
            if ($adjustedPrice > $price) {
                $price = $adjustedPrice;
            }
        }
        
        if ($productType === 'credit_life' && $vehiclePrice > 0 && $termMonths > 0) {
            // Credit life based on loan amount and term
            $monthlyRate = 0.50; // $0.50 per $100 per month (typical rate)
            $adjustedPrice = ($vehiclePrice / 100) * $monthlyRate * $termMonths;
            if ($adjustedPrice > $price) {
                $price = $adjustedPrice;
            }
        }
        
        // Calculate profit and commission
        $profit = $price - $cost;
        $profitMargin = $cost > 0 ? ($profit / $cost) * 100 : 0;
        $commission = $price * ($product['commission_percentage'] / 100);
        
        $result = array(
            'product_name' => $product['name'],
            'category' => $product['category'],
            'description' => $product['description'],
            'tier' => $tier,
            'cost' => $cost,
            'price' => $price,
            'profit' => $profit,
            'profit_margin' => $profitMargin,
            'commission' => $commission,
            'commission_percentage' => $product['commission_percentage'],
        );
        
        $GLOBALS['log']->debug("F&I Deal Center: Product pricing calculated - Cost: $" . number_format($cost, 2) . 
                              ", Price: $" . number_format($price, 2) . 
                              ", Profit: $" . number_format($profit, 2));
        
        return $result;
    }
    
    /**
     * Get all available product categories
     * 
     * @return array List of product categories
     */
    public static function getProductCategories()
    {
        $products = self::getDefaultProducts();
        $categories = array();
        
        foreach ($products as $product) {
            if (!in_array($product['category'], $categories)) {
                $categories[] = $product['category'];
            }
        }
        
        sort($categories);
        return $categories;
    }
    
    /**
     * Get products by category
     * 
     * @param string $category Product category
     * @return array Products in the specified category
     */
    public static function getProductsByCategory($category)
    {
        $products = self::getDefaultProducts();
        $categoryProducts = array();
        
        foreach ($products as $key => $product) {
            if ($product['category'] === $category) {
                $categoryProducts[$key] = $product;
            }
        }
        
        return $categoryProducts;
    }
    
    /**
     * Calculate total F&I product sales for a deal
     * 
     * @param array $selectedProducts Array of selected products with tiers
     * @param float $vehiclePrice Vehicle price for calculations
     * @param int $termMonths Loan term
     * @return array Total calculations
     */
    public static function calculateDealProducts($selectedProducts, $vehiclePrice = 0, $termMonths = 60)
    {
        $GLOBALS['log']->debug("F&I Deal Center: Calculating total product sales for deal");
        
        $totals = array(
            'total_cost' => 0,
            'total_price' => 0,
            'total_profit' => 0,
            'total_commission' => 0,
            'products' => array(),
        );
        
        if (empty($selectedProducts)) {
            return $totals;
        }
        
        foreach ($selectedProducts as $productType => $tier) {
            $productCalc = self::calculateProductPricing($productType, $tier, $vehiclePrice, $termMonths);
            
            if ($productCalc) {
                $totals['total_cost'] += $productCalc['cost'];
                $totals['total_price'] += $productCalc['price'];
                $totals['total_profit'] += $productCalc['profit'];
                $totals['total_commission'] += $productCalc['commission'];
                $totals['products'][$productType] = $productCalc;
            }
        }
        
        // Calculate overall margin
        $totals['profit_margin'] = $totals['total_cost'] > 0 ? 
                                  ($totals['total_profit'] / $totals['total_cost']) * 100 : 0;
        
        $GLOBALS['log']->debug("F&I Deal Center: Total product calculations - Cost: $" . number_format($totals['total_cost'], 2) . 
                              ", Price: $" . number_format($totals['total_price'], 2) . 
                              ", Profit: $" . number_format($totals['total_profit'], 2));
        
        return $totals;
    }
    
    /**
     * Get recommended products based on deal characteristics
     * 
     * @param float $vehiclePrice Vehicle price
     * @param string $financeMethod Finance method (Cash/Finance/Lease)
     * @param int $termMonths Loan term
     * @param float $customerCreditScore Customer credit score (if available)
     * @return array Recommended products
     */
    public static function getRecommendedProducts($vehiclePrice, $financeMethod = 'Finance', $termMonths = 60, $customerCreditScore = 700)
    {
        $GLOBALS['log']->debug("F&I Deal Center: Getting recommended products for deal");
        
        $recommendations = array();
        
        // Always recommend GAP for financed vehicles
        if ($financeMethod === 'Finance' || $financeMethod === 'Lease') {
            $recommendations['gap_insurance'] = array(
                'priority' => 'high',
                'reason' => 'Recommended for all financed vehicles',
                'suggested_tier' => 'standard'
            );
        }
        
        // Extended warranty for higher-value vehicles
        if ($vehiclePrice > 15000) {
            $tier = $vehiclePrice > 30000 ? 'premium' : 'basic';
            $recommendations['extended_warranty'] = array(
                'priority' => 'high',
                'reason' => 'Valuable protection for higher-value vehicles',
                'suggested_tier' => $tier
            );
        }
        
        // Paint protection for new/newer vehicles
        if ($vehiclePrice > 20000) {
            $recommendations['paint_protection'] = array(
                'priority' => 'medium',
                'reason' => 'Protects investment in vehicle appearance',
                'suggested_tier' => 'basic'
            );
        }
        
        // Theft protection for all vehicles
        $recommendations['theft_protection'] = array(
            'priority' => 'medium',
            'reason' => 'Affordable theft deterrent',
            'suggested_tier' => 'standard'
        );
        
        // Maintenance package for new vehicles
        if ($vehiclePrice > 25000) {
            $recommendations['maintenance_package'] = array(
                'priority' => 'medium',
                'reason' => 'Convenient prepaid maintenance',
                'suggested_tier' => '3_year'
            );
        }
        
        // Credit life for financed deals with lower credit scores
        if (($financeMethod === 'Finance' || $financeMethod === 'Lease') && $customerCreditScore < 650) {
            $recommendations['credit_life'] = array(
                'priority' => 'medium',
                'reason' => 'Financial protection for family',
                'suggested_tier' => 'life_disability'
            );
        }
        
        $GLOBALS['log']->debug("F&I Deal Center: Generated " . count($recommendations) . " product recommendations");
        
        return $recommendations;
    }
} 