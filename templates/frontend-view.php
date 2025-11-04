<?php
/**
 * Frontend project view template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="tgp-frontend-wrapper">
    <div class="tgp-frontend-container">
        
        <!-- Header -->
        <div class="tgp-frontend-header">
            <h1 class="tgp-frontend-title"><?php echo esc_html($project->name); ?></h1>
            <?php if ($project->description): ?>
                <p class="tgp-frontend-description"><?php echo esc_html($project->description); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Stats Cards -->
        <div class="tgp-frontend-stats">
            <div class="tgp-frontend-stat-card">
                <div class="tgp-stat-icon">📈</div>
                <div class="tgp-stat-content">
                    <div class="tgp-stat-label">Current Traffic</div>
                    <div class="tgp-stat-value"><?php echo number_format($current_traffic); ?></div>
                    <div class="tgp-stat-sublabel">visits/month</div>
                </div>
            </div>
            
            <div class="tgp-frontend-stat-card tgp-stat-highlight">
                <div class="tgp-stat-icon">🚀</div>
                <div class="tgp-stat-content">
                    <div class="tgp-stat-label">Projected Traffic</div>
                    <div class="tgp-stat-value"><?php echo number_format($projected_traffic); ?></div>
                    <div class="tgp-stat-sublabel">visits/month</div>
                </div>
            </div>
            
            <div class="tgp-frontend-stat-card">
                <div class="tgp-stat-icon">📊</div>
                <div class="tgp-stat-content">
                    <div class="tgp-stat-label">Growth Potential</div>
                    <div class="tgp-stat-value"><?php echo number_format($growth_percentage, 1); ?>%</div>
                    <div class="tgp-stat-sublabel">increase</div>
                </div>
            </div>
            
            <div class="tgp-frontend-stat-card">
                <div class="tgp-stat-icon">💰</div>
                <div class="tgp-stat-content">
                    <div class="tgp-stat-label">Est. Revenue Range</div>
                    <div class="tgp-stat-value tgp-stat-small">$<?php echo number_format($roi_data['revenue_low']); ?> - $<?php echo number_format($roi_data['revenue_high']); ?></div>
                    <div class="tgp-stat-sublabel">per month</div>
                </div>
            </div>
        </div>
        
        <!-- Chart Section -->
        <div class="tgp-frontend-chart-section">
            <h2 class="tgp-section-title">12-Month Traffic Growth Projection</h2>
            
            <div class="tgp-chart-filters">
                <label class="tgp-filter-checkbox">
                    <input type="checkbox" class="tgp-frontend-chart-toggle" data-category="Current Trajectory" checked>
                    <span class="tgp-filter-label">
                        <span class="tgp-filter-dot" style="background: #6c757d;"></span>
                        Current Trajectory
                    </span>
                </label>
                <label class="tgp-filter-checkbox">
                    <input type="checkbox" class="tgp-frontend-chart-toggle" data-category="Existing Keywords" checked>
                    <span class="tgp-filter-label">
                        <span class="tgp-filter-dot" style="background: #007bff;"></span>
                        Existing Keywords
                    </span>
                </label>
                <label class="tgp-filter-checkbox">
                    <input type="checkbox" class="tgp-frontend-chart-toggle" data-category="Must-Have Keywords" checked>
                    <span class="tgp-filter-label">
                        <span class="tgp-filter-dot" style="background: #28a745;"></span>
                        Must-Have Keywords
                    </span>
                </label>
                <label class="tgp-filter-checkbox">
                    <input type="checkbox" class="tgp-frontend-chart-toggle" data-category="New Keywords" checked>
                    <span class="tgp-filter-label">
                        <span class="tgp-filter-dot" style="background: #ffc107;"></span>
                        New Keywords
                    </span>
                </label>
            </div>
            
            <div class="tgp-frontend-chart-container">
                <canvas id="tgp-frontend-traffic-chart"></canvas>
            </div>
        </div>
        
        <!-- ROI Section -->
        <div class="tgp-frontend-roi-section">
            <h2 class="tgp-section-title">ROI Projections</h2>
            
            <div class="tgp-roi-grid">
                <div class="tgp-roi-card">
                    <div class="tgp-roi-label">Conversion Rate</div>
                    <div class="tgp-roi-value"><?php echo esc_html($project->conversion_rate_low); ?>% - <?php echo esc_html($project->conversion_rate_high); ?>%</div>
                </div>
                <div class="tgp-roi-card">
                    <div class="tgp-roi-label">Expected Conversions</div>
                    <div class="tgp-roi-value"><?php echo number_format($roi_data['conversions_low']); ?> - <?php echo number_format($roi_data['conversions_high']); ?></div>
                </div>
                <div class="tgp-roi-card">
                    <div class="tgp-roi-label">Customer Lifetime Value</div>
                    <div class="tgp-roi-value">$<?php echo number_format($project->cltv, 2); ?></div>
                </div>
                <div class="tgp-roi-card tgp-roi-highlight">
                    <div class="tgp-roi-label">Projected Revenue</div>
                    <div class="tgp-roi-value">$<?php echo number_format($roi_data['revenue_low']); ?> - $<?php echo number_format($roi_data['revenue_high']); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Keywords Summary -->
        <div class="tgp-frontend-keywords-section">
            <h2 class="tgp-section-title">Keyword Summary</h2>
            
            <div class="tgp-keywords-summary">
                <div class="tgp-summary-stat">
                    <span class="tgp-summary-number"><?php echo count($keywords); ?></span>
                    <span class="tgp-summary-label">Total Keywords</span>
                </div>
                
                <?php
                $keyword_counts = $db->get_keyword_counts($project_id);
                $categories = array(
                    'Existing Keywords (transactional terms only)' => 'Existing',
                    'Must-Have Keywords (limit to 50)' => 'Must-Have',
                    'New Keywords (limit to 100)' => 'New'
                );
                
                foreach ($categories as $full_cat => $short_cat):
                    $count = isset($keyword_counts[$full_cat]) ? $keyword_counts[$full_cat]->count : 0;
                    if ($count > 0):
                ?>
                <div class="tgp-summary-stat">
                    <span class="tgp-summary-number"><?php echo esc_html($count); ?></span>
                    <span class="tgp-summary-label"><?php echo esc_html($short_cat); ?></span>
                </div>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>
        
        <!-- Footer Note -->
        <div class="tgp-frontend-footer">
            <p><strong>Note:</strong> These projections are based on SEO best practices and keyword analysis. Actual results may vary based on implementation, competition, and market conditions.</p>
        </div>
        
    </div>
</div>

<!-- Hidden data for JavaScript -->
<script type="text/javascript">
    var tgpFrontendData = <?php echo wp_json_encode(array(
        'projections' => $projections,
        'total' => $total
    )); ?>;
</script>

