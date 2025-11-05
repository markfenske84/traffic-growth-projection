document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // Chart instance
    let trafficChart = null;
    let projectionsData = null;
    
    // Initialize project view
    if (typeof tgpProjectId !== 'undefined') {
        initProjectView();
    }
    
    /**
     * Initialize project view
     */
    function initProjectView() {
        loadProjections();
        loadKeywords();
        
        // Chart toggle handlers
        const chartToggles = document.querySelectorAll('.tgp-chart-toggle');
        chartToggles.forEach(function(toggle) {
            toggle.addEventListener('change', updateChart);
        });
        
        // Import keywords handler
        const importBtn = document.getElementById('tgp-import-keywords-btn');
        if (importBtn) {
            importBtn.addEventListener('click', function() {
                document.getElementById('tgp-import-modal').style.display = 'block';
            });
        }
        
        // Close import modal
        const closeElements = document.querySelectorAll('.tgp-modal-close, #tgp-cancel-import, .tgp-modal-overlay');
        closeElements.forEach(function(el) {
            el.addEventListener('click', function() {
                document.getElementById('tgp-import-modal').style.display = 'none';
            });
        });
        
        // Start import
        const startImportBtn = document.getElementById('tgp-start-import');
        if (startImportBtn) {
            startImportBtn.addEventListener('click', handleImport);
        }
        
        // Category filter
        const categoryFilter = document.getElementById('tgp-category-filter');
        if (categoryFilter) {
            categoryFilter.addEventListener('change', function() {
                loadKeywords(this.value);
            });
        }
        
        // Calculate ROI
        const calculateRoiBtn = document.getElementById('tgp-calculate-roi');
        if (calculateRoiBtn) {
            calculateRoiBtn.addEventListener('click', calculateROI);
        }
        
        // Export report
        const exportBtn = document.getElementById('tgp-export-report-btn');
        if (exportBtn) {
            exportBtn.addEventListener('click', exportReport);
        }
        
        // Download chart
        const downloadChartBtn = document.getElementById('tgp-download-chart');
        if (downloadChartBtn) {
            downloadChartBtn.addEventListener('click', downloadChart);
        }
        
        // Close edit keyword modal
        const closeEditElements = document.querySelectorAll('#tgp-edit-keyword-modal .tgp-modal-close, #tgp-cancel-edit-keyword');
        closeEditElements.forEach(function(el) {
            el.addEventListener('click', function() {
                document.getElementById('tgp-edit-keyword-modal').style.display = 'none';
            });
        });
        
        // Save keyword
        const saveKeywordBtn = document.getElementById('tgp-save-keyword');
        if (saveKeywordBtn) {
            saveKeywordBtn.addEventListener('click', saveKeyword);
        }
    }
    
    // Copy shortcode functionality (for both dashboard and project view)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('tgp-copy-shortcode') || e.target.classList.contains('tgp-copy-shortcode-inline')) {
            e.preventDefault();
            const shortcode = e.target.dataset.shortcode;
            
            // Create temporary textarea to copy text
            const textarea = document.createElement('textarea');
            textarea.value = shortcode;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                document.execCommand('copy');
                const originalText = e.target.textContent;
                e.target.textContent = '✓';
                setTimeout(function() {
                    e.target.textContent = originalText;
                }, 2000);
            } catch (err) {
                alert('Failed to copy. Please copy manually: ' + shortcode);
            }
            
            document.body.removeChild(textarea);
        }
        
        // Edit keyword handler
        if (e.target.classList.contains('tgp-edit-keyword')) {
            const keywordId = e.target.dataset.keywordId;
            openEditKeywordModal(keywordId);
        }
        
        // Delete keyword handler
        if (e.target.classList.contains('tgp-delete-keyword')) {
            const keywordId = e.target.dataset.keywordId;
            deleteKeyword(keywordId);
        }
    });
    
    /**
     * Initialize project view
     */
    if (typeof tgpProjectId !== 'undefined') {
        initProjectView();
    }
    
    /**
     * Initialize dashboard
     */
    // Open create modal
    const createBtns = document.querySelectorAll('#tgp-create-project-btn, #tgp-create-first-project-btn');
    createBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('tgp-modal-title').textContent = 'Create New Project';
            document.getElementById('tgp-project-form').reset();
            document.getElementById('tgp-project-id').value = '';
            document.getElementById('tgp-project-modal').style.display = 'block';
        });
    });
    
    // Close modal
    const modalCloseElements = document.querySelectorAll('.tgp-modal-close, #tgp-cancel-project, .tgp-modal-overlay');
    modalCloseElements.forEach(function(el) {
        el.addEventListener('click', function() {
            const modal = document.getElementById('tgp-project-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    // Edit project
    const editBtns = document.querySelectorAll('.tgp-edit-project');
    editBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const projectId = this.dataset.projectId;
            const projectCard = this.closest('.tgp-project-card');
            
            // Get project data from the card
            const name = projectCard.querySelector('h3').textContent.trim();
            const description = projectCard.querySelector('.tgp-project-description');
            const metaItems = projectCard.querySelectorAll('.tgp-meta-value');
            
            // Parse conversion rates (format: "1.00% - 5.00%")
            const conversionText = metaItems[0].textContent.trim();
            const conversionMatch = conversionText.match(/(\d+\.?\d*)\s*%\s*-\s*(\d+\.?\d*)\s*%/);
            const conversionLow = conversionMatch ? conversionMatch[1] : '1.00';
            const conversionHigh = conversionMatch ? conversionMatch[2] : '5.00';
            
            // Parse CLTV (format: "$1,234.56")
            const cltvText = metaItems[1].textContent.trim();
            const cltv = cltvText.replace(/[$,]/g, '');
            
            // Populate form
            document.getElementById('tgp-modal-title').textContent = 'Edit Project';
            document.getElementById('tgp-project-id').value = projectId;
            document.getElementById('tgp-project-name').value = name;
            document.getElementById('tgp-project-description').value = description ? description.textContent.trim() : '';
            document.getElementById('tgp-conversion-low').value = conversionLow;
            document.getElementById('tgp-conversion-high').value = conversionHigh;
            document.getElementById('tgp-cltv').value = cltv;
            
            // Show modal
            document.getElementById('tgp-project-modal').style.display = 'block';
        });
    });
    
    // Save project
    const saveProjectBtn = document.getElementById('tgp-save-project');
    if (saveProjectBtn) {
        saveProjectBtn.addEventListener('click', function() {
            const projectId = document.getElementById('tgp-project-id').value;
            const action = projectId ? 'tgp_update_project' : 'tgp_create_project';
            
            const formData = new FormData();
            formData.append('action', action);
            formData.append('nonce', tgpAjax.nonce);
            formData.append('name', document.getElementById('tgp-project-name').value);
            formData.append('description', document.getElementById('tgp-project-description').value);
            formData.append('conversion_rate_low', document.getElementById('tgp-conversion-low').value);
            formData.append('conversion_rate_high', document.getElementById('tgp-conversion-high').value);
            formData.append('cltv', document.getElementById('tgp-cltv').value);
            
            if (projectId) {
                formData.append('project_id', projectId);
            }
            
            fetch(tgpAjax.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.data);
                }
            });
        });
    }
    
    // Delete project
    const deleteBtns = document.querySelectorAll('.tgp-delete-project');
    deleteBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to delete this project? All keywords will be permanently deleted.')) {
                return;
            }
            
            const projectId = this.dataset.projectId;
            
            const formData = new FormData();
            formData.append('action', 'tgp_delete_project');
            formData.append('nonce', tgpAjax.nonce);
            formData.append('project_id', projectId);
            
            fetch(tgpAjax.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.data);
                }
            });
        });
    });
    
    // Initialize drag and drop for project cards
    initProjectDragAndDrop();
    
    /**
     * Initialize drag and drop for project cards
     */
    function initProjectDragAndDrop() {
        const projectCards = document.querySelectorAll('.tgp-project-card');
        let draggedElement = null;
        
        projectCards.forEach(function(card) {
            card.addEventListener('dragstart', function(e) {
                draggedElement = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', this.innerHTML);
            });
            
            card.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                
                // Remove all drag-over classes
                document.querySelectorAll('.tgp-project-card').forEach(function(c) {
                    c.classList.remove('drag-over');
                });
                
                // Save new order
                saveProjectOrder();
            });
            
            card.addEventListener('dragover', function(e) {
                if (e.preventDefault) {
                    e.preventDefault();
                }
                e.dataTransfer.dropEffect = 'move';
                
                if (this !== draggedElement) {
                    this.classList.add('drag-over');
                }
                
                return false;
            });
            
            card.addEventListener('dragenter', function() {
                if (this !== draggedElement) {
                    this.classList.add('drag-over');
                }
            });
            
            card.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });
            
            card.addEventListener('drop', function(e) {
                if (e.stopPropagation) {
                    e.stopPropagation();
                }
                
                if (draggedElement !== this) {
                    // Get the parent container
                    const container = this.parentNode;
                    const allCards = Array.from(container.querySelectorAll('.tgp-project-card'));
                    const draggedIndex = allCards.indexOf(draggedElement);
                    const targetIndex = allCards.indexOf(this);
                    
                    // Reorder in DOM
                    if (draggedIndex < targetIndex) {
                        this.parentNode.insertBefore(draggedElement, this.nextSibling);
                    } else {
                        this.parentNode.insertBefore(draggedElement, this);
                    }
                }
                
                this.classList.remove('drag-over');
                
                return false;
            });
        });
    }
    
    /**
     * Save project order
     */
    function saveProjectOrder() {
        const projectCards = document.querySelectorAll('.tgp-project-card');
        const order = {};
        
        projectCards.forEach(function(card, index) {
            const projectId = card.dataset.projectId;
            order[projectId] = index;
        });
        
        const formData = new FormData();
        formData.append('action', 'tgp_update_project_order');
        formData.append('nonce', tgpAjax.nonce);
        formData.append('order', JSON.stringify(order));
        
        fetch(tgpAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(function(data) {
            if (!data.success) {
                console.error('Failed to save project order:', data.data);
            }
        })
        .catch(function(error) {
            console.error('Error saving project order:', error);
        });
    }
    
    /**
     * Load projections
     */
    function loadProjections() {
        const formData = new FormData();
        formData.append('action', 'tgp_get_projections');
        formData.append('nonce', tgpAjax.nonce);
        formData.append('project_id', tgpProjectId);
        formData.append('months', 12);
        
        fetch(tgpAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(function(response) {
            if (response.success) {
                projectionsData = response.data;
                renderChart(response.data);
                updateProjectedTraffic(response.data);
            }
        });
    }
    
    /**
     * Render chart
     */
    function renderChart(data) {
        const ctx = document.getElementById('tgp-traffic-chart');
        
        if (!ctx) return;
        
        // Prepare datasets
        const datasets = [];
        const colors = {
            'Current Trajectory': '#6c757d',
            'Existing Keywords': '#007bff',
            'Must-Have Keywords': '#28a745',
            'New Keywords': '#ffc107'
        };
        
        Object.keys(data.projections).forEach(function(category) {
            const projection = data.projections[category];
            const traffic = projection.map(function(item) { return item.traffic; });
            
            datasets.push({
                label: category,
                data: traffic,
                borderColor: colors[category] || '#333',
                backgroundColor: colors[category] || '#333',
                borderWidth: 2,
                fill: false,
                tension: 0.4
            });
        });
        
        // Destroy existing chart
        if (trafficChart) {
            trafficChart.destroy();
        }
        
        // Create new chart
        trafficChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Month 1', 'Month 2', 'Month 3', 'Month 4', 'Month 5', 'Month 6', 
                        'Month 7', 'Month 8', 'Month 9', 'Month 10', 'Month 11', 'Month 12'],
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Traffic Growth Projection'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' visits';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    
    /**
     * Update chart based on toggles
     */
    function updateChart() {
        if (!trafficChart || !projectionsData) return;
        
        const activeCategories = [];
        const checkedToggles = document.querySelectorAll('.tgp-chart-toggle:checked');
        checkedToggles.forEach(function(toggle) {
            activeCategories.push(toggle.dataset.category);
        });
        
        trafficChart.data.datasets.forEach(function(dataset) {
            dataset.hidden = !activeCategories.includes(dataset.label);
        });
        
        trafficChart.update();
    }
    
    /**
     * Update projected traffic display
     */
    function updateProjectedTraffic(data) {
        if (data.total && data.total.length > 0) {
            const lastMonth = data.total[data.total.length - 1];
            const projectedEl = document.getElementById('tgp-projected-traffic');
            if (projectedEl) {
                projectedEl.textContent = lastMonth.traffic.toLocaleString() + ' visits/month';
            }
        }
    }
    
    /**
     * Load keywords
     */
    function loadKeywords(category) {
        const formData = new FormData();
        formData.append('action', 'tgp_get_keywords');
        formData.append('nonce', tgpAjax.nonce);
        formData.append('project_id', tgpProjectId);
        
        if (category) {
            formData.append('category', category);
        }
        
        fetch(tgpAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(function(response) {
            if (response.success) {
                renderKeywordsTable(response.data.keywords);
            }
        });
    }
    
    /**
     * Render keywords table
     */
    function renderKeywordsTable(keywords) {
        const tbody = document.getElementById('tgp-keywords-tbody');
        tbody.innerHTML = '';
        
        if (keywords.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align: center;">No keywords found. Import keywords to get started.</td></tr>';
            return;
        }
        
        keywords.forEach(function(keyword) {
            const captureRate = getCaptureRate(keyword.expected_ranking || keyword.current_ranking);
            const projectedTraffic = Math.round(keyword.estimated_traffic * captureRate);
            
            const row = document.createElement('tr');
            row.innerHTML = '<td>' + escapeHtml(keyword.keyword) + '</td>' +
                '<td>' + parseInt(keyword.search_volume).toLocaleString() + '</td>' +
                '<td>' + parseFloat(keyword.difficulty).toFixed(1) + '%</td>' +
                '<td>' + parseInt(keyword.estimated_traffic).toLocaleString() + '</td>' +
                '<td>' + (keyword.current_ranking || '-') + '</td>' +
                '<td>' + (keyword.expected_ranking || '-') + '</td>' +
                '<td>' + projectedTraffic.toLocaleString() + '</td>' +
                '<td>' + escapeHtml(keyword.category) + '</td>' +
                '<td>' +
                    '<button type="button" class="button button-small tgp-edit-keyword" data-keyword-id="' + keyword.id + '">Edit</button> ' +
                    '<button type="button" class="button button-small tgp-delete-keyword" data-keyword-id="' + keyword.id + '">Delete</button>' +
                '</td>';
            
            tbody.appendChild(row);
        });
    }
    
    /**
     * Get capture rate by ranking
     */
    function getCaptureRate(ranking) {
        const rates = {
            1: 0.60, 2: 0.50, 3: 0.40, 4: 0.30, 5: 0.25,
            6: 0.20, 7: 0.15, 8: 0.12, 9: 0.08, 10: 0.05
        };
        return rates[parseInt(ranking)] || 0;
    }
    
    /**
     * Handle import
     */
    function handleImport() {
        const fileInput = document.getElementById('tgp-import-file');
        
        if (!fileInput.files || !fileInput.files[0]) {
            alert('Please select a file to import');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'tgp_import_keywords');
        formData.append('nonce', tgpAjax.nonce);
        formData.append('project_id', tgpProjectId);
        formData.append('tgp_import_file', fileInput.files[0]);
        
        const clearExisting = document.getElementById('tgp-clear-existing');
        if (clearExisting && clearExisting.checked) {
            formData.append('clear_existing', 'yes');
        }
        
        const startImportBtn = document.getElementById('tgp-start-import');
        startImportBtn.disabled = true;
        startImportBtn.textContent = 'Importing...';
        
        fetch(tgpAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(function(response) {
            if (response.success) {
                alert('Successfully imported ' + response.data.imported + ' keywords');
                document.getElementById('tgp-import-modal').style.display = 'none';
                document.getElementById('tgp-import-form').reset();
                loadProjections();
                loadKeywords();
            } else {
                alert('Import failed: ' + response.data);
            }
        })
        .catch(function() {
            alert('Import failed due to a server error');
        })
        .finally(function() {
            startImportBtn.disabled = false;
            startImportBtn.textContent = 'Import Keywords';
        });
    }
    
    /**
     * Calculate ROI
     */
    function calculateROI() {
        if (!projectionsData) {
            alert('Please wait for projections to load');
            return;
        }
        
        const month = parseInt(document.getElementById('tgp-roi-month').value);
        const monthlyInvestment = parseFloat(document.getElementById('tgp-roi-investment').value);
        
        // Calculate total investment (monthly investment * number of months)
        const totalInvestment = monthlyInvestment * month;
        
        // Get traffic for selected month
        let traffic = 0;
        if (projectionsData.total && projectionsData.total[month - 1]) {
            traffic = projectionsData.total[month - 1].traffic;
        }
        
        const formData = new FormData();
        formData.append('action', 'tgp_calculate_roi');
        formData.append('nonce', tgpAjax.nonce);
        formData.append('traffic', traffic);
        formData.append('conversion_rate_low', tgpProjectData.conversion_rate_low);
        formData.append('conversion_rate_high', tgpProjectData.conversion_rate_high);
        formData.append('cltv', tgpProjectData.cltv);
        formData.append('investment', totalInvestment);
        
        fetch(tgpAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(function(response) {
            if (response.success) {
                displayROI(response.data, traffic);
            }
        });
    }
    
    /**
     * Display ROI results
     */
    function displayROI(roi, traffic) {
        document.getElementById('tgp-roi-traffic').textContent = traffic.toLocaleString() + ' visits';
        document.getElementById('tgp-roi-conversions').textContent = 
            roi.conversions_low.toLocaleString() + ' - ' + roi.conversions_high.toLocaleString();
        document.getElementById('tgp-roi-revenue').textContent = 
            '$' + roi.revenue_low.toLocaleString() + ' - $' + roi.revenue_high.toLocaleString();
        
        // Format ROI as multiplier
        let roiLowText = roi.roi_low.toFixed(1) + 'x';
        let roiHighText = roi.roi_high.toFixed(1) + 'x';
        
        document.getElementById('tgp-roi-percentage').textContent = roiLowText + ' - ' + roiHighText;
        
        document.getElementById('tgp-roi-results').style.display = 'block';
    }
    
    /**
     * Export report
     */
    function exportReport() {
        if (!projectionsData) {
            alert('No data to export');
            return;
        }
        
        // Create CSV content
        let csv = 'Month,Current Trajectory,Existing Keywords,Must-Have Keywords,New Keywords,Total\n';
        
        for (let i = 0; i < 12; i++) {
            const row = [i + 1];
            
            const categories = ['Current Trajectory', 'Existing Keywords', 'Must-Have Keywords', 'New Keywords'];
            categories.forEach(function(cat) {
                let value = 0;
                if (projectionsData.projections[cat] && projectionsData.projections[cat][i]) {
                    value = projectionsData.projections[cat][i].traffic;
                }
                row.push(value);
            });
            
            let totalValue = 0;
            if (projectionsData.total && projectionsData.total[i]) {
                totalValue = projectionsData.total[i].traffic;
            }
            row.push(totalValue);
            
            csv += row.join(',') + '\n';
        }
        
        // Generate filename with project name and date/time
        const filename = generateFileName(tgpProjectData.name, 'projections', 'csv');
        
        // Download CSV
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }
    
    /**
     * Download chart as image
     */
    function downloadChart() {
        if (!trafficChart) {
            alert('No chart available to download');
            return;
        }
        
        // Get the canvas as a data URL
        const canvas = document.getElementById('tgp-traffic-chart');
        
        // Create a temporary canvas with white background
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = canvas.width;
        tempCanvas.height = canvas.height;
        const ctx = tempCanvas.getContext('2d');
        
        // Fill with white background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
        
        // Draw the chart on top
        ctx.drawImage(canvas, 0, 0);
        
        // Generate filename with project name and date/time
        const filename = generateFileName(tgpProjectData.name, 'chart', 'jpg');
        
        // Convert to JPEG and download
        tempCanvas.toBlob(function(blob) {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }, 'image/jpeg', 0.95);
    }
    
    /**
     * Open edit keyword modal
     */
    function openEditKeywordModal(keywordId) {
        const formData = new FormData();
        formData.append('action', 'tgp_get_keyword');
        formData.append('nonce', tgpAjax.nonce);
        formData.append('keyword_id', keywordId);
        
        fetch(tgpAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(function(response) {
            if (response.success) {
                const keyword = response.data;
                
                // Populate form
                document.getElementById('tgp-edit-keyword-id').value = keyword.id;
                document.getElementById('tgp-edit-keyword-text').value = keyword.keyword;
                document.getElementById('tgp-edit-search-volume').value = keyword.search_volume;
                document.getElementById('tgp-edit-difficulty').value = keyword.difficulty;
                document.getElementById('tgp-edit-estimated-traffic').value = keyword.estimated_traffic;
                document.getElementById('tgp-edit-current-ranking').value = keyword.current_ranking || '';
                document.getElementById('tgp-edit-expected-ranking').value = keyword.expected_ranking || '';
                document.getElementById('tgp-edit-category').value = keyword.category;
                
                // Show modal
                document.getElementById('tgp-edit-keyword-modal').style.display = 'block';
            } else {
                alert('Error loading keyword: ' + response.data);
            }
        })
        .catch(function() {
            alert('Failed to load keyword data');
        });
    }
    
    /**
     * Save keyword
     */
    function saveKeyword() {
        const keywordId = document.getElementById('tgp-edit-keyword-id').value;
        
        const formData = new FormData();
        formData.append('action', 'tgp_update_keyword');
        formData.append('nonce', tgpAjax.nonce);
        formData.append('keyword_id', keywordId);
        formData.append('keyword', document.getElementById('tgp-edit-keyword-text').value);
        formData.append('search_volume', document.getElementById('tgp-edit-search-volume').value);
        formData.append('difficulty', document.getElementById('tgp-edit-difficulty').value);
        formData.append('estimated_traffic', document.getElementById('tgp-edit-estimated-traffic').value);
        formData.append('current_ranking', document.getElementById('tgp-edit-current-ranking').value);
        formData.append('expected_ranking', document.getElementById('tgp-edit-expected-ranking').value);
        formData.append('category', document.getElementById('tgp-edit-category').value);
        
        const saveBtn = document.getElementById('tgp-save-keyword');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
        
        fetch(tgpAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(function(response) {
            if (response.success) {
                document.getElementById('tgp-edit-keyword-modal').style.display = 'none';
                loadProjections();
                loadKeywords();
            } else {
                alert('Error: ' + response.data);
            }
        })
        .catch(function() {
            alert('Failed to save keyword');
        })
        .finally(function() {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Changes';
        });
    }
    
    /**
     * Delete keyword
     */
    function deleteKeyword(keywordId) {
        if (!confirm('Are you sure you want to delete this keyword? This action cannot be undone.')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'tgp_delete_keyword');
        formData.append('nonce', tgpAjax.nonce);
        formData.append('keyword_id', keywordId);
        
        fetch(tgpAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(function(response) {
            if (response.success) {
                loadProjections();
                loadKeywords();
            } else {
                alert('Error: ' + response.data);
            }
        })
        .catch(function() {
            alert('Failed to delete keyword');
        });
    }
    
    /**
     * Generate filename with project name and date/time
     */
    function generateFileName(projectName, type, extension) {
        // Sanitize project name for filename
        const sanitizedName = projectName
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        
        // Format date and time (YYYY-MM-DD-HHMMSS)
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        const dateTime = year + '-' + month + '-' + day + '-' + hours + minutes + seconds;
        
        return sanitizedName + '-' + type + '-' + dateTime + '.' + extension;
    }
    
    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
