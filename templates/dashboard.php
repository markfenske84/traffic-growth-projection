<?php
/**
 * Dashboard template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap tgp-dashboard">
    <h1 class="tgp-page-title">Traffic Growth Projection Projects</h1>
    
    <div class="tgp-header-actions">
        <button type="button" class="button button-primary" id="tgp-create-project-btn">
            Create New Project
        </button>
        <a href="<?php echo esc_url(TGP_PLUGIN_URL . 'sample-data.csv'); ?>" download="keyword-import-template.csv" class="button">
            📥 Download Import Template
        </a>
    </div>
    
    <div class="tgp-import-help">
        <p><strong>💡 Getting Started:</strong> Download the import template above to see the correct CSV format. Fill it with your keyword data, then import it into your project.<?php if (!empty($projects)): ?> <strong>Tip:</strong> Drag and drop project cards to reorder them.<?php endif; ?></p>
    </div>
    
    <div class="tgp-projects-grid">
        <?php if (empty($projects)): ?>
            <div class="tgp-empty-state">
                <div class="tgp-empty-icon">📊</div>
                <h3>No Projects Yet</h3>
                <p>Create your first traffic growth projection project to get started.</p>
                <div class="tgp-empty-state-actions">
                    <button type="button" class="button button-primary button-hero" id="tgp-create-first-project-btn">
                        Create Your First Project
                    </button>
                    <a href="<?php echo esc_url(TGP_PLUGIN_URL . 'sample-data.csv'); ?>" download="keyword-import-template.csv" class="button button-hero">
                        📥 Download Import Template
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($projects as $project): ?>
                <div class="tgp-project-card" data-project-id="<?php echo esc_attr($project->id); ?>" draggable="true">
                    <div class="tgp-project-card-header">
                        <span class="tgp-drag-handle" title="Drag to reorder">⋮⋮</span>
                        <h3><?php echo esc_html($project->name); ?></h3>
                        <div class="tgp-project-actions">
                            <button type="button" class="button button-small tgp-edit-project" data-project-id="<?php echo esc_attr($project->id); ?>">
                                Edit
                            </button>
                            <button type="button" class="button button-small button-link-delete tgp-delete-project" data-project-id="<?php echo esc_attr($project->id); ?>">
                                Delete
                            </button>
                        </div>
                    </div>
                    
                    <div class="tgp-project-card-body">
                        <?php if ($project->description): ?>
                            <p class="tgp-project-description"><?php echo esc_html($project->description); ?></p>
                        <?php endif; ?>
                        
                        <div class="tgp-project-meta">
                            <div class="tgp-meta-item">
                                <span class="tgp-meta-label">Conversion Rate:</span>
                                <span class="tgp-meta-value"><?php echo esc_html($project->conversion_rate_low); ?>% - <?php echo esc_html($project->conversion_rate_high); ?>%</span>
                            </div>
                            <div class="tgp-meta-item">
                                <span class="tgp-meta-label">CLTV:</span>
                                <span class="tgp-meta-value">$<?php echo esc_html(number_format($project->cltv, 2)); ?></span>
                            </div>
                            <div class="tgp-meta-item">
                                <span class="tgp-meta-label">Created:</span>
                                <span class="tgp-meta-value"><?php echo esc_html(wp_date('M j, Y', strtotime($project->created_at))); ?></span>
                            </div>
                            <div class="tgp-meta-item tgp-meta-shortcode">
                                <span class="tgp-meta-label">Shortcode:</span>
                                <div class="tgp-shortcode-inline">
                                    <code>[traffic_projection id="<?php echo esc_attr($project->id); ?>"]</code>
                                    <button type="button" class="button-link tgp-copy-shortcode-inline" data-shortcode='[traffic_projection id="<?php echo esc_attr($project->id); ?>"]' title="Copy">📋</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tgp-project-card-footer">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=traffic-growth-projection&project_id=' . $project->id)); ?>" class="button button-primary">
                            View Project →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create/Edit Project Modal -->
<div id="tgp-project-modal" class="tgp-modal" style="display: none;">
    <div class="tgp-modal-overlay"></div>
    <div class="tgp-modal-content">
        <div class="tgp-modal-header">
            <h2 id="tgp-modal-title">Create New Project</h2>
            <button type="button" class="tgp-modal-close">&times;</button>
        </div>
        
        <div class="tgp-modal-body">
            <form id="tgp-project-form">
                <input type="hidden" id="tgp-project-id" value="">
                
                <div class="tgp-form-group">
                    <label for="tgp-project-name">Project Name *</label>
                    <input type="text" id="tgp-project-name" class="widefat" required>
                </div>
                
                <div class="tgp-form-group">
                    <label for="tgp-project-description">Description</label>
                    <textarea id="tgp-project-description" class="widefat" rows="3"></textarea>
                </div>
                
                <div class="tgp-form-row">
                    <div class="tgp-form-group">
                        <label for="tgp-conversion-low">Conversion Rate Low (%)</label>
                        <input type="number" id="tgp-conversion-low" class="widefat" step="0.01" min="0" max="100" value="1.00">
                    </div>
                    
                    <div class="tgp-form-group">
                        <label for="tgp-conversion-high">Conversion Rate High (%)</label>
                        <input type="number" id="tgp-conversion-high" class="widefat" step="0.01" min="0" max="100" value="5.00">
                    </div>
                </div>
                
                <div class="tgp-form-group">
                    <label for="tgp-cltv">Customer Lifetime Value ($)</label>
                    <input type="number" id="tgp-cltv" class="widefat" step="0.01" min="0" value="0.00">
                </div>
            </form>
        </div>
        
        <div class="tgp-modal-footer">
            <button type="button" class="button" id="tgp-cancel-project">Cancel</button>
            <button type="button" class="button button-primary" id="tgp-save-project">Save Project</button>
        </div>
    </div>
</div>


