<?php
/**
 * Project view template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap tgp-project-view">
    <div class="tgp-header">
        <div class="tgp-header-left">
            <a href="<?php echo esc_url(admin_url('admin.php?page=traffic-growth-projection')); ?>" class="button">
                ← Back to Projects
            </a>
            <h1 class="tgp-page-title"><?php echo esc_html($project->name); ?></h1>
        </div>
        <div class="tgp-header-right">
            <button type="button" class="button" id="tgp-import-keywords-btn">
                Import Keywords
            </button>
            <button type="button" class="button" id="tgp-export-report-btn">
                Export Report
            </button>
        </div>
    </div>
    
    <?php if ($project->description): ?>
        <div class="tgp-project-info">
            <p><?php echo esc_html($project->description); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="tgp-stats-row">
        <div class="tgp-stat-card">
            <div class="tgp-stat-label">Conversion Rate Range</div>
            <div class="tgp-stat-value"><?php echo esc_html($project->conversion_rate_low); ?>% - <?php echo esc_html($project->conversion_rate_high); ?>%</div>
        </div>
        <div class="tgp-stat-card">
            <div class="tgp-stat-label">Customer Lifetime Value</div>
            <div class="tgp-stat-value">$<?php echo esc_html(number_format($project->cltv, 2)); ?></div>
        </div>
        <div class="tgp-stat-card">
            <div class="tgp-stat-label">Total Keywords</div>
            <div class="tgp-stat-value" id="tgp-total-keywords">
                <?php 
                $total = 0;
                foreach ($keyword_counts as $cat) {
                    $total += $cat->count;
                }
                echo esc_html($total);
                ?>
            </div>
        </div>
        <div class="tgp-stat-card">
            <div class="tgp-stat-label">Projected Monthly Traffic</div>
            <div class="tgp-stat-value" id="tgp-projected-traffic">-</div>
        </div>
    </div>
    
    <div class="tgp-chart-section">
        <div class="tgp-chart-header">
            <h2>Traffic Growth Projections</h2>
            <div class="tgp-chart-controls">
                <label>
                    <input type="checkbox" class="tgp-chart-toggle" data-category="Current Trajectory" checked>
                    Current Trajectory
                </label>
                <label>
                    <input type="checkbox" class="tgp-chart-toggle" data-category="Existing Keywords" checked>
                    Existing Keywords
                </label>
                <label>
                    <input type="checkbox" class="tgp-chart-toggle" data-category="Must-Have Keywords" checked>
                    Must-Have Keywords
                </label>
                <label>
                    <input type="checkbox" class="tgp-chart-toggle" data-category="New Keywords" checked>
                    New Keywords
                </label>
                <button type="button" class="button" id="tgp-download-chart" style="margin-left: 10px;">
                    Download Chart Image
                </button>
            </div>
        </div>
        
        <div class="tgp-chart-container">
            <canvas id="tgp-traffic-chart"></canvas>
        </div>
    </div>
    
    <div class="tgp-roi-section">
        <h2>ROI Calculator</h2>
        <div class="tgp-roi-inputs">
            <div class="tgp-form-group">
                <label for="tgp-roi-month">Month</label>
                <select id="tgp-roi-month" class="widefat">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo esc_attr($i); ?>" <?php echo $i === 12 ? 'selected' : ''; ?>>
                            Month <?php echo esc_html($i); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="tgp-form-group">
                <label for="tgp-roi-investment">Investment Amount ($)</label>
                <input type="number" id="tgp-roi-investment" class="widefat" step="0.01" min="0" value="0">
            </div>
            <div class="tgp-form-group">
                <button type="button" class="button button-primary" id="tgp-calculate-roi">Calculate ROI</button>
            </div>
        </div>
        
        <div class="tgp-roi-results" id="tgp-roi-results" style="display: none;">
            <div class="tgp-roi-grid">
                <div class="tgp-roi-card">
                    <div class="tgp-roi-label">Expected Traffic</div>
                    <div class="tgp-roi-value" id="tgp-roi-traffic">-</div>
                </div>
                <div class="tgp-roi-card">
                    <div class="tgp-roi-label">Conversions (Low - High)</div>
                    <div class="tgp-roi-value" id="tgp-roi-conversions">-</div>
                </div>
                <div class="tgp-roi-card">
                    <div class="tgp-roi-label">Revenue (Low - High)</div>
                    <div class="tgp-roi-value" id="tgp-roi-revenue">-</div>
                </div>
                <div class="tgp-roi-card">
                    <div class="tgp-roi-label">ROI (Low - High)</div>
                    <div class="tgp-roi-value" id="tgp-roi-percentage">-</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tgp-keywords-section">
        <div class="tgp-keywords-header">
            <h2>Keywords</h2>
            <div class="tgp-keywords-filters">
                <select id="tgp-category-filter" class="widefat">
                    <option value="">All Categories</option>
                    <option value="Existing Keywords (transactional terms only)">Existing Keywords</option>
                    <option value="Must-Have Keywords (limit to 50)">Must-Have Keywords</option>
                    <option value="New Keywords (limit to 100)">New Keywords</option>
                </select>
            </div>
        </div>
        
        <div class="tgp-keywords-table-wrapper">
            <table class="wp-list-table widefat fixed striped" id="tgp-keywords-table">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Search Volume</th>
                        <th>Difficulty</th>
                        <th>Est. Traffic</th>
                        <th>Current Rank</th>
                        <th>Expected Rank</th>
                        <th>Projected Traffic</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tgp-keywords-tbody">
                    <tr>
                        <td colspan="9" style="text-align: center;">Loading keywords...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="tgp-import-modal" class="tgp-modal" style="display: none;">
    <div class="tgp-modal-overlay"></div>
    <div class="tgp-modal-content">
        <div class="tgp-modal-header">
            <h2>Import Keywords</h2>
            <button type="button" class="tgp-modal-close">&times;</button>
        </div>
        
        <div class="tgp-modal-body">
            <div class="tgp-import-instructions">
                <p><strong>CSV Format Requirements:</strong></p>
                <ul>
                    <li>Column A: Keyword</li>
                    <li>Column B: Search Volume (number)</li>
                    <li>Column C: Difficulty (percentage)</li>
                    <li>Column D: Estimated Traffic (number)</li>
                    <li>Column E: Current Ranking (0-100, optional)</li>
                    <li>Column F: Expected Ranking (1-10)</li>
                    <li>Column G: Category (dropdown value)</li>
                </ul>
                <p style="margin-top: 15px;">
                    <a href="<?php echo esc_url(TGP_PLUGIN_URL . 'sample-data.csv'); ?>" download="keyword-import-template.csv" class="button button-secondary">
                        📥 Download Template CSV
                    </a>
                </p>
            </div>
            
            <form id="tgp-import-form" enctype="multipart/form-data">
                <div class="tgp-form-group">
                    <label for="tgp-import-file">Select CSV File</label>
                    <input type="file" id="tgp-import-file" name="tgp_import_file" accept=".csv" required>
                </div>
                
                <div class="tgp-form-group">
                    <label>
                        <input type="checkbox" id="tgp-clear-existing" name="clear_existing" value="yes">
                        Clear existing keywords before import
                    </label>
                </div>
            </form>
        </div>
        
        <div class="tgp-modal-footer">
            <button type="button" class="button" id="tgp-cancel-import">Cancel</button>
            <button type="button" class="button button-primary" id="tgp-start-import">Import Keywords</button>
        </div>
    </div>
</div>

<!-- Edit Keyword Modal -->
<div id="tgp-edit-keyword-modal" class="tgp-modal" style="display: none;">
    <div class="tgp-modal-overlay"></div>
    <div class="tgp-modal-content">
        <div class="tgp-modal-header">
            <h2>Edit Keyword</h2>
            <button type="button" class="tgp-modal-close">&times;</button>
        </div>
        
        <div class="tgp-modal-body">
            <form id="tgp-edit-keyword-form">
                <input type="hidden" id="tgp-edit-keyword-id" value="">
                
                <div class="tgp-form-group">
                    <label for="tgp-edit-keyword-text">Keyword</label>
                    <input type="text" id="tgp-edit-keyword-text" class="widefat" required>
                </div>
                
                <div class="tgp-form-group">
                    <label for="tgp-edit-search-volume">Search Volume</label>
                    <input type="number" id="tgp-edit-search-volume" class="widefat" min="0" required>
                </div>
                
                <div class="tgp-form-group">
                    <label for="tgp-edit-difficulty">Difficulty (%)</label>
                    <input type="number" id="tgp-edit-difficulty" class="widefat" min="0" max="100" step="0.1" required>
                </div>
                
                <div class="tgp-form-group">
                    <label for="tgp-edit-estimated-traffic">Estimated Traffic</label>
                    <input type="number" id="tgp-edit-estimated-traffic" class="widefat" min="0" required>
                </div>
                
                <div class="tgp-form-group">
                    <label for="tgp-edit-current-ranking">Current Ranking (0-100, optional)</label>
                    <input type="number" id="tgp-edit-current-ranking" class="widefat" min="0" max="100">
                </div>
                
                <div class="tgp-form-group">
                    <label for="tgp-edit-expected-ranking">Expected Ranking (1-10)</label>
                    <input type="number" id="tgp-edit-expected-ranking" class="widefat" min="1" max="10" required>
                </div>
                
                <div class="tgp-form-group">
                    <label for="tgp-edit-category">Category</label>
                    <select id="tgp-edit-category" class="widefat" required>
                        <option value="Existing Keywords (transactional terms only)">Existing Keywords</option>
                        <option value="Must-Have Keywords (limit to 50)">Must-Have Keywords</option>
                        <option value="New Keywords (limit to 100)">New Keywords</option>
                    </select>
                </div>
            </form>
        </div>
        
        <div class="tgp-modal-footer">
            <button type="button" class="button" id="tgp-cancel-edit-keyword">Cancel</button>
            <button type="button" class="button button-primary" id="tgp-save-keyword">Save Changes</button>
        </div>
    </div>
</div>

<script type="text/javascript">
var tgpProjectId = <?php echo intval($project->id); ?>;
var tgpProjectData = <?php echo json_encode(array(
    'name' => $project->name,
    'conversion_rate_low' => floatval($project->conversion_rate_low),
    'conversion_rate_high' => floatval($project->conversion_rate_high),
    'cltv' => floatval($project->cltv)
)); ?>;
</script>

