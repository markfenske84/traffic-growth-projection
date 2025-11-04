<?php
/**
 * Traffic and ROI calculation class
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGP_Calculator {
    
    /**
     * Traffic capture rates by ranking position
     */
    private static $capture_rates = array(
        1 => 0.60,
        2 => 0.50,
        3 => 0.40,
        4 => 0.30,
        5 => 0.25,
        6 => 0.20,
        7 => 0.15,
        8 => 0.12,
        9 => 0.08,
        10 => 0.05
    );
    
    /**
     * Get capture rate for ranking position
     */
    public static function get_capture_rate($ranking) {
        $ranking = intval($ranking);
        
        if ($ranking < 1) {
            return 0;
        }
        
        if ($ranking > 10) {
            return 0;
        }
        
        return self::$capture_rates[$ranking] ?? 0;
    }
    
    /**
     * Calculate traffic for a keyword based on ranking
     */
    public static function calculate_keyword_traffic($estimated_traffic, $ranking) {
        $capture_rate = self::get_capture_rate($ranking);
        return round($estimated_traffic * $capture_rate);
    }
    
    /**
     * Calculate monthly projection for keywords
     */
    public static function calculate_monthly_projection($keywords, $months = 12) {
        $projection = array();
        
        // Calculate total traffic per month (assuming linear growth over 12 months)
        for ($month = 1; $month <= $months; $month++) {
            $total_traffic = 0;
            
            foreach ($keywords as $keyword) {
                $ranking = $keyword->expected_ranking ?? $keyword->current_ranking;
                if ($ranking) {
                    // Progressive growth over time
                    $growth_factor = $month / $months;
                    $traffic = self::calculate_keyword_traffic($keyword->estimated_traffic, $ranking);
                    $total_traffic += ($traffic * $growth_factor);
                }
            }
            
            $projection[] = array(
                'month' => $month,
                'traffic' => round($total_traffic)
            );
        }
        
        return $projection;
    }
    
    /**
     * Calculate current trajectory based on current rankings
     */
    public static function calculate_current_trajectory($keywords, $months = 12) {
        $projection = array();
        
        for ($month = 1; $month <= $months; $month++) {
            $total_traffic = 0;
            
            foreach ($keywords as $keyword) {
                if ($keyword->current_ranking) {
                    $traffic = self::calculate_keyword_traffic($keyword->estimated_traffic, $keyword->current_ranking);
                    // Current trajectory remains flat (no growth assumed)
                    $total_traffic += $traffic;
                }
            }
            
            $projection[] = array(
                'month' => $month,
                'traffic' => round($total_traffic)
            );
        }
        
        return $projection;
    }
    
    /**
     * Calculate projections by category
     */
    public static function calculate_projections_by_category($project_id, $months = 12) {
        $db = TGP_Database::get_instance();
        
        $categories = array(
            'Current Trajectory' => array(),
            'Existing Keywords' => $db->get_keywords($project_id, 'Existing Keywords (transactional terms only)'),
            'Must-Have Keywords' => $db->get_keywords($project_id, 'Must-Have Keywords (limit to 50)'),
            'New Keywords' => $db->get_keywords($project_id, 'New Keywords (limit to 100)')
        );
        
        // Get all keywords for current trajectory
        $all_keywords = $db->get_keywords($project_id);
        
        $results = array();
        
        // Current trajectory uses all keywords with current rankings
        $results['Current Trajectory'] = self::calculate_current_trajectory($all_keywords, $months);
        
        // Other categories use expected rankings
        foreach ($categories as $category => $keywords) {
            if ($category !== 'Current Trajectory' && !empty($keywords)) {
                $results[$category] = self::calculate_monthly_projection($keywords, $months);
            }
        }
        
        return $results;
    }
    
    /**
     * Calculate ROI metrics
     */
    public static function calculate_roi($traffic, $conversion_rate_low, $conversion_rate_high, $cltv, $investment = 0) {
        // Calculate conversions
        $conversions_low = $traffic * ($conversion_rate_low / 100);
        $conversions_high = $traffic * ($conversion_rate_high / 100);
        
        // Calculate revenue
        $revenue_low = $conversions_low * $cltv;
        $revenue_high = $conversions_high * $cltv;
        
        // Calculate ROI as multiplier (revenue / investment)
        $roi_low = $investment > 0 ? ($revenue_low / $investment) : 0;
        $roi_high = $investment > 0 ? ($revenue_high / $investment) : 0;
        
        return array(
            'conversions_low' => round($conversions_low, 2),
            'conversions_high' => round($conversions_high, 2),
            'revenue_low' => round($revenue_low, 2),
            'revenue_high' => round($revenue_high, 2),
            'roi_low' => round($roi_low, 2),
            'roi_high' => round($roi_high, 2),
            'lead_value_low' => $cltv,
            'lead_value_high' => $cltv
        );
    }
    
    /**
     * Calculate total projected traffic for all categories
     */
    public static function calculate_total_projection($project_id, $months = 12) {
        $projections = self::calculate_projections_by_category($project_id, $months);
        
        $total = array();
        
        for ($month = 1; $month <= $months; $month++) {
            $monthly_traffic = 0;
            
            foreach ($projections as $category => $projection) {
                if ($category !== 'Current Trajectory' && isset($projection[$month - 1])) {
                    $monthly_traffic += $projection[$month - 1]['traffic'];
                }
            }
            
            $total[] = array(
                'month' => $month,
                'traffic' => $monthly_traffic
            );
        }
        
        return $total;
    }
}

