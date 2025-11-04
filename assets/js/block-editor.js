(function(blocks, element, blockEditor, components, i18n, apiFetch) {
    var el = element.createElement;
    var Fragment = element.Fragment;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var Placeholder = components.Placeholder;
    var Spinner = components.Spinner;
    var __ = i18n.__;
    var useState = element.useState;
    var useEffect = element.useEffect;
    
    blocks.registerBlockType('traffic-growth-projection/project-display', {
        title: __('Traffic Growth Projection', 'traffic-growth-projection'),
        description: __('Display a traffic growth projection for clients', 'traffic-growth-projection'),
        icon: 'chart-line',
        category: 'widgets',
        attributes: {
            projectId: {
                type: 'string',
                default: ''
            }
        },
        
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var projectId = attributes.projectId;
            
            var projects = useState([]);
            var loading = useState(true);
            var selectedProject = useState(null);
            
            // Fetch projects on mount
            useEffect(function() {
                apiFetch({
                    path: '/traffic-growth-projection/v1/projects'
                }).then(function(data) {
                    projects[1](data);
                    loading[1](false);
                    
                    // Find selected project
                    if (projectId) {
                        var found = data.find(function(p) { return p.value === projectId; });
                        if (found) {
                            selectedProject[1](found);
                        }
                    }
                }).catch(function(error) {
                    console.error('Error fetching projects:', error);
                    loading[1](false);
                });
            }, []);
            
            // Update selected project when projectId changes
            useEffect(function() {
                if (projectId && projects[0].length > 0) {
                    var found = projects[0].find(function(p) { return p.value === projectId; });
                    if (found) {
                        selectedProject[1](found);
                    }
                }
            }, [projectId, projects[0]]);
            
            function onChangeProject(value) {
                setAttributes({ projectId: value });
                var found = projects[0].find(function(p) { return p.value === value; });
                if (found) {
                    selectedProject[1](found);
                }
            }
            
            var projectOptions = [
                { value: '', label: __('Select a project...', 'traffic-growth-projection') }
            ].concat(projects[0]);
            
            return el(
                Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        {
                            title: __('Project Settings', 'traffic-growth-projection'),
                            initialOpen: true
                        },
                        el(SelectControl, {
                            label: __('Select Project', 'traffic-growth-projection'),
                            value: projectId,
                            options: projectOptions,
                            onChange: onChangeProject,
                            help: __('Choose which traffic projection to display', 'traffic-growth-projection')
                        })
                    )
                ),
                el(
                    'div',
                    { className: 'tgp-block-editor' },
                    loading[0] ? el(
                        Placeholder,
                        {
                            icon: 'chart-line',
                            label: __('Traffic Growth Projection', 'traffic-growth-projection')
                        },
                        el(Spinner)
                    ) : projectId && selectedProject[0] ? el(
                        'div',
                        { className: 'tgp-block-preview' },
                        el('div', { className: 'tgp-block-preview-header' },
                            el('span', { className: 'tgp-block-icon' }, '📊'),
                            el('div', { className: 'tgp-block-preview-content' },
                                el('h3', {}, __('Traffic Growth Projection', 'traffic-growth-projection')),
                                el('p', {}, selectedProject[0].label)
                            )
                        ),
                        el('div', { className: 'tgp-block-preview-info' },
                            el('p', {}, 
                                el('strong', {}, __('Project ID:', 'traffic-growth-projection') + ' '),
                                projectId
                            ),
                            el('p', { className: 'tgp-block-preview-note' },
                                __('Preview is shown in the editor. The full interactive projection will display on the frontend.', 'traffic-growth-projection')
                            )
                        )
                    ) : el(
                        Placeholder,
                        {
                            icon: 'chart-line',
                            label: __('Traffic Growth Projection', 'traffic-growth-projection'),
                            instructions: __('Select a project from the block settings to display.', 'traffic-growth-projection')
                        },
                        projects[0].length === 0 ? el(
                            'p',
                            { style: { color: '#d63638' } },
                            __('No projects found. Create a project first in the Traffic Growth admin area.', 'traffic-growth-projection')
                        ) : null
                    )
                )
            );
        },
        
        save: function() {
            // Rendered by PHP
            return null;
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n,
    window.wp.apiFetch
);

