<?php
/**
 * SuiteCRM Lead Attribution Center - ROI Calculator
 * 
 * This class handles ROI calculations and attribution analysis for lead tracking
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class ROICalculator
{
    /**
     * Calculate ROI percentage
     * 
     * @param float $revenue Total revenue
     * @param float $cost Total cost
     * @return float ROI percentage
     */
    public function calculateROI($revenue, $cost)
    {
        if ($cost <= 0) {
            return 0;
        }
        
        $roi = (($revenue - $cost) / $cost) * 100;
        return round($roi, 2);
    }
    
    /**
     * Calculate Cost Per Lead (CPL)
     * 
     * @param float $totalCost Total campaign cost
     * @param int $leadCount Number of leads generated
     * @return float Cost per lead
     */
    public function calculateCPL($totalCost, $leadCount)
    {
        if ($leadCount <= 0) {
            return 0;
        }
        
        return round($totalCost / $leadCount, 2);
    }
    
    /**
     * Calculate Customer Acquisition Cost (CAC)
     * 
     * @param float $totalCost Total campaign cost
     * @param int $customerCount Number of customers acquired
     * @return float Customer acquisition cost
     */
    public function calculateCAC($totalCost, $customerCount)
    {
        if ($customerCount <= 0) {
            return 0;
        }
        
        return round($totalCost / $customerCount, 2);
    }
    
    /**
     * Calculate Lead-to-Customer conversion rate
     * 
     * @param int $customerCount Number of customers
     * @param int $leadCount Number of leads
     * @return float Conversion rate percentage
     */
    public function calculateConversionRate($customerCount, $leadCount)
    {
        if ($leadCount <= 0) {
            return 0;
        }
        
        return round(($customerCount / $leadCount) * 100, 2);
    }
    
    /**
     * Calculate Customer Lifetime Value (CLV)
     * 
     * @param float $averageOrderValue Average order value
     * @param float $purchaseFrequency Purchase frequency per year
     * @param float $customerLifespan Customer lifespan in years
     * @return float Customer lifetime value
     */
    public function calculateCLV($averageOrderValue, $purchaseFrequency, $customerLifespan)
    {
        return round($averageOrderValue * $purchaseFrequency * $customerLifespan, 2);
    }
    
    /**
     * Calculate attribution-based ROI for different models
     * 
     * @param array $attributionData Attribution data for multiple touchpoints
     * @param string $model Attribution model (first-touch, last-touch, linear, time-decay)
     * @return array ROI breakdown by touchpoint
     */
    public function calculateAttributionROI($attributionData, $model = 'last-touch')
    {
        switch ($model) {
            case 'first-touch':
                return $this->calculateFirstTouchROI($attributionData);
                
            case 'last-touch':
                return $this->calculateLastTouchROI($attributionData);
                
            case 'linear':
                return $this->calculateLinearROI($attributionData);
                
            case 'time-decay':
                return $this->calculateTimeDecayROI($attributionData);
                
            case 'position-based':
                return $this->calculatePositionBasedROI($attributionData);
                
            default:
                return $this->calculateLastTouchROI($attributionData);
        }
    }
    
    /**
     * Calculate first-touch attribution ROI
     */
    private function calculateFirstTouchROI($attributionData)
    {
        if (empty($attributionData)) {
            return array();
        }
        
        $firstTouch = reset($attributionData);
        $totalRevenue = $this->getTotalRevenue($attributionData);
        $totalCost = $this->getTotalCost($attributionData);
        
        return array(
            'model' => 'first-touch',
            'touchpoints' => array(
                array(
                    'source' => $firstTouch['source'] ?? '',
                    'medium' => $firstTouch['medium'] ?? '',
                    'campaign' => $firstTouch['campaign'] ?? '',
                    'attribution_credit' => 1.0,
                    'attributed_revenue' => $totalRevenue,
                    'attributed_cost' => $totalCost,
                    'roi' => $this->calculateROI($totalRevenue, $totalCost)
                )
            ),
            'total_roi' => $this->calculateROI($totalRevenue, $totalCost)
        );
    }
    
    /**
     * Calculate last-touch attribution ROI
     */
    private function calculateLastTouchROI($attributionData)
    {
        if (empty($attributionData)) {
            return array();
        }
        
        $lastTouch = end($attributionData);
        $totalRevenue = $this->getTotalRevenue($attributionData);
        $totalCost = $this->getTotalCost($attributionData);
        
        return array(
            'model' => 'last-touch',
            'touchpoints' => array(
                array(
                    'source' => $lastTouch['source'] ?? '',
                    'medium' => $lastTouch['medium'] ?? '',
                    'campaign' => $lastTouch['campaign'] ?? '',
                    'attribution_credit' => 1.0,
                    'attributed_revenue' => $totalRevenue,
                    'attributed_cost' => $totalCost,
                    'roi' => $this->calculateROI($totalRevenue, $totalCost)
                )
            ),
            'total_roi' => $this->calculateROI($totalRevenue, $totalCost)
        );
    }
    
    /**
     * Calculate linear attribution ROI
     */
    private function calculateLinearROI($attributionData)
    {
        if (empty($attributionData)) {
            return array();
        }
        
        $touchpointCount = count($attributionData);
        $creditPerTouch = 1.0 / $touchpointCount;
        $totalRevenue = $this->getTotalRevenue($attributionData);
        $totalCost = $this->getTotalCost($attributionData);
        
        $touchpoints = array();
        foreach ($attributionData as $touchpoint) {
            $attributedRevenue = $totalRevenue * $creditPerTouch;
            $attributedCost = ($touchpoint['cost'] ?? 0);
            
            $touchpoints[] = array(
                'source' => $touchpoint['source'] ?? '',
                'medium' => $touchpoint['medium'] ?? '',
                'campaign' => $touchpoint['campaign'] ?? '',
                'attribution_credit' => $creditPerTouch,
                'attributed_revenue' => $attributedRevenue,
                'attributed_cost' => $attributedCost,
                'roi' => $this->calculateROI($attributedRevenue, $attributedCost)
            );
        }
        
        return array(
            'model' => 'linear',
            'touchpoints' => $touchpoints,
            'total_roi' => $this->calculateROI($totalRevenue, $totalCost)
        );
    }
    
    /**
     * Calculate time-decay attribution ROI
     */
    private function calculateTimeDecayROI($attributionData)
    {
        if (empty($attributionData)) {
            return array();
        }
        
        $totalRevenue = $this->getTotalRevenue($attributionData);
        $totalCost = $this->getTotalCost($attributionData);
        
        // Calculate time decay weights (more recent touchpoints get higher weight)
        $weights = $this->calculateTimeDecayWeights($attributionData);
        
        $touchpoints = array();
        foreach ($attributionData as $index => $touchpoint) {
            $weight = $weights[$index];
            $attributedRevenue = $totalRevenue * $weight;
            $attributedCost = ($touchpoint['cost'] ?? 0);
            
            $touchpoints[] = array(
                'source' => $touchpoint['source'] ?? '',
                'medium' => $touchpoint['medium'] ?? '',
                'campaign' => $touchpoint['campaign'] ?? '',
                'attribution_credit' => $weight,
                'attributed_revenue' => $attributedRevenue,
                'attributed_cost' => $attributedCost,
                'roi' => $this->calculateROI($attributedRevenue, $attributedCost)
            );
        }
        
        return array(
            'model' => 'time-decay',
            'touchpoints' => $touchpoints,
            'total_roi' => $this->calculateROI($totalRevenue, $totalCost)
        );
    }
    
    /**
     * Calculate position-based attribution ROI (40% first, 40% last, 20% middle)
     */
    private function calculatePositionBasedROI($attributionData)
    {
        if (empty($attributionData)) {
            return array();
        }
        
        $touchpointCount = count($attributionData);
        $totalRevenue = $this->getTotalRevenue($attributionData);
        $totalCost = $this->getTotalCost($attributionData);
        
        $touchpoints = array();
        
        foreach ($attributionData as $index => $touchpoint) {
            // Position-based attribution weights
            if ($index === 0) {
                // First touchpoint gets 40%
                $credit = 0.4;
            } elseif ($index === $touchpointCount - 1) {
                // Last touchpoint gets 40%
                $credit = 0.4;
            } else {
                // Middle touchpoints share 20%
                $middleCount = max(1, $touchpointCount - 2);
                $credit = 0.2 / $middleCount;
            }
            
            $attributedRevenue = $totalRevenue * $credit;
            $attributedCost = ($touchpoint['cost'] ?? 0);
            
            $touchpoints[] = array(
                'source' => $touchpoint['source'] ?? '',
                'medium' => $touchpoint['medium'] ?? '',
                'campaign' => $touchpoint['campaign'] ?? '',
                'attribution_credit' => $credit,
                'attributed_revenue' => $attributedRevenue,
                'attributed_cost' => $attributedCost,
                'roi' => $this->calculateROI($attributedRevenue, $attributedCost)
            );
        }
        
        return array(
            'model' => 'position-based',
            'touchpoints' => $touchpoints,
            'total_roi' => $this->calculateROI($totalRevenue, $totalCost)
        );
    }
    
    /**
     * Calculate time decay weights
     */
    private function calculateTimeDecayWeights($attributionData)
    {
        $weights = array();
        $touchpointCount = count($attributionData);
        
        // Use exponential decay with half-life of 7 days
        $halfLife = 7 * 24 * 3600; // 7 days in seconds
        $lambda = log(2) / $halfLife;
        
        $now = time();
        $totalWeight = 0;
        
        // Calculate raw weights
        foreach ($attributionData as $touchpoint) {
            $timestamp = strtotime($touchpoint['timestamp'] ?? 'now');
            $age = $now - $timestamp;
            $weight = exp(-$lambda * $age);
            $weights[] = $weight;
            $totalWeight += $weight;
        }
        
        // Normalize weights to sum to 1
        if ($totalWeight > 0) {
            for ($i = 0; $i < count($weights); $i++) {
                $weights[$i] = $weights[$i] / $totalWeight;
            }
        }
        
        return $weights;
    }
    
    /**
     * Get total revenue from attribution data
     */
    private function getTotalRevenue($attributionData)
    {
        $totalRevenue = 0;
        foreach ($attributionData as $touchpoint) {
            $totalRevenue += ($touchpoint['revenue'] ?? 0);
        }
        return $totalRevenue;
    }
    
    /**
     * Get total cost from attribution data
     */
    private function getTotalCost($attributionData)
    {
        $totalCost = 0;
        foreach ($attributionData as $touchpoint) {
            $totalCost += ($touchpoint['cost'] ?? 0);
        }
        return $totalCost;
    }
    
    /**
     * Calculate channel performance metrics
     * 
     * @param array $channelData Channel performance data
     * @return array Channel metrics
     */
    public function calculateChannelMetrics($channelData)
    {
        $metrics = array();
        
        foreach ($channelData as $channel => $data) {
            $leads = $data['leads'] ?? 0;
            $revenue = $data['revenue'] ?? 0;
            $cost = $data['cost'] ?? 0;
            $conversions = $data['conversions'] ?? 0;
            
            $metrics[$channel] = array(
                'leads' => $leads,
                'revenue' => $revenue,
                'cost' => $cost,
                'conversions' => $conversions,
                'roi' => $this->calculateROI($revenue, $cost),
                'cpl' => $this->calculateCPL($cost, $leads),
                'cac' => $this->calculateCAC($cost, $conversions),
                'conversion_rate' => $this->calculateConversionRate($conversions, $leads),
                'revenue_per_lead' => $leads > 0 ? round($revenue / $leads, 2) : 0
            );
        }
        
        return $metrics;
    }
    
    /**
     * Calculate campaign performance metrics
     * 
     * @param array $campaignData Campaign data
     * @return array Campaign metrics
     */
    public function calculateCampaignMetrics($campaignData)
    {
        $metrics = array();
        
        foreach ($campaignData as $campaign => $data) {
            $leads = $data['leads'] ?? 0;
            $revenue = $data['revenue'] ?? 0;
            $cost = $data['cost'] ?? 0;
            $conversions = $data['conversions'] ?? 0;
            $impressions = $data['impressions'] ?? 0;
            $clicks = $data['clicks'] ?? 0;
            
            $metrics[$campaign] = array(
                'leads' => $leads,
                'revenue' => $revenue,
                'cost' => $cost,
                'conversions' => $conversions,
                'impressions' => $impressions,
                'clicks' => $clicks,
                'roi' => $this->calculateROI($revenue, $cost),
                'cpl' => $this->calculateCPL($cost, $leads),
                'cac' => $this->calculateCAC($cost, $conversions),
                'conversion_rate' => $this->calculateConversionRate($conversions, $leads),
                'ctr' => $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0,
                'cpc' => $clicks > 0 ? round($cost / $clicks, 2) : 0,
                'lead_conversion_rate' => $clicks > 0 ? round(($leads / $clicks) * 100, 2) : 0
            );
        }
        
        return $metrics;
    }
    
    /**
     * Calculate overall attribution summary
     * 
     * @param array $attributionRecords Array of attribution records
     * @return array Summary metrics
     */
    public function calculateAttributionSummary($attributionRecords)
    {
        $summary = array(
            'total_leads' => 0,
            'total_revenue' => 0,
            'total_cost' => 0,
            'total_conversions' => 0,
            'channels' => array(),
            'campaigns' => array(),
            'attribution_models' => array()
        );
        
        foreach ($attributionRecords as $record) {
            $summary['total_leads']++;
            $summary['total_revenue'] += ($record['conversion_value'] ?? 0);
            $summary['total_cost'] += ($record['total_cost'] ?? 0);
            
            if (!empty($record['conversion_value'])) {
                $summary['total_conversions']++;
            }
            
            // Group by channel
            $channel = $record['first_touch_source'] ?? 'Unknown';
            if (!isset($summary['channels'][$channel])) {
                $summary['channels'][$channel] = array(
                    'leads' => 0,
                    'revenue' => 0,
                    'cost' => 0,
                    'conversions' => 0
                );
            }
            
            $summary['channels'][$channel]['leads']++;
            $summary['channels'][$channel]['revenue'] += ($record['conversion_value'] ?? 0);
            $summary['channels'][$channel]['cost'] += ($record['total_cost'] ?? 0);
            
            if (!empty($record['conversion_value'])) {
                $summary['channels'][$channel]['conversions']++;
            }
            
            // Group by campaign
            $campaign = $record['first_touch_campaign'] ?? 'Unknown';
            if (!isset($summary['campaigns'][$campaign])) {
                $summary['campaigns'][$campaign] = array(
                    'leads' => 0,
                    'revenue' => 0,
                    'cost' => 0,
                    'conversions' => 0
                );
            }
            
            $summary['campaigns'][$campaign]['leads']++;
            $summary['campaigns'][$campaign]['revenue'] += ($record['conversion_value'] ?? 0);
            $summary['campaigns'][$campaign]['cost'] += ($record['total_cost'] ?? 0);
            
            if (!empty($record['conversion_value'])) {
                $summary['campaigns'][$campaign]['conversions']++;
            }
        }
        
        // Calculate overall metrics
        $summary['overall_roi'] = $this->calculateROI($summary['total_revenue'], $summary['total_cost']);
        $summary['overall_cpl'] = $this->calculateCPL($summary['total_cost'], $summary['total_leads']);
        $summary['overall_cac'] = $this->calculateCAC($summary['total_cost'], $summary['total_conversions']);
        $summary['overall_conversion_rate'] = $this->calculateConversionRate($summary['total_conversions'], $summary['total_leads']);
        
        // Calculate channel metrics
        $summary['channel_metrics'] = $this->calculateChannelMetrics($summary['channels']);
        
        // Calculate campaign metrics
        $summary['campaign_metrics'] = $this->calculateCampaignMetrics($summary['campaigns']);
        
        return $summary;
    }
}