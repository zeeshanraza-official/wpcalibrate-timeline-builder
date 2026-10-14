(function (wp) {
    'use strict';

    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor ? wp.blockEditor.InspectorControls : wp.editor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var SelectControl = wp.components.SelectControl;
    var ServerSideRender = wp.serverSideRender || wp.components.ServerSideRender;

    var timelinesList = (window.wpcalBlockData && window.wpcalBlockData.timelines) ? window.wpcalBlockData.timelines : [];

    var timelineOptions = [
        { label: __('-- Select a Timeline --', 'wpcalibrate-timeline-builder'), value: 0 }
    ];

    timelinesList.forEach(function (tl) {
        timelineOptions.push({
            label: tl.title + ' (#' + tl.id + ')',
            value: tl.id
        });
    });

    registerBlockType('wpcalibrate/timeline', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var timelineId = parseInt(attributes.timelineId, 10) || 0;

            var inspector = el(
                InspectorControls,
                { key: 'inspector' },
                el(
                    PanelBody,
                    { title: __('Timeline Settings', 'wpcalibrate-timeline-builder'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Select Timeline', 'wpcalibrate-timeline-builder'),
                        value: timelineId,
                        options: timelineOptions,
                        onChange: function (val) {
                            setAttributes({ timelineId: parseInt(val, 10) || 0 });
                        }
                    }),
                    el(SelectControl, {
                        label: __('Layout Override (Optional)', 'wpcalibrate-timeline-builder'),
                        value: attributes.layout || '',
                        options: [
                            { label: __('Default from timeline', 'wpcalibrate-timeline-builder'), value: '' },
                            { label: __('Vertical', 'wpcalibrate-timeline-builder'), value: 'vertical' },
                            { label: __('Horizontal', 'wpcalibrate-timeline-builder'), value: 'horizontal' }
                        ],
                        onChange: function (val) {
                            setAttributes({ layout: val });
                        }
                    }),
                    el(SelectControl, {
                        label: __('Preset Override (Optional)', 'wpcalibrate-timeline-builder'),
                        value: attributes.preset || '',
                        options: [
                            { label: __('Default from timeline', 'wpcalibrate-timeline-builder'), value: '' },
                            { label: __('Clean', 'wpcalibrate-timeline-builder'), value: 'clean' },
                            { label: __('Cards', 'wpcalibrate-timeline-builder'), value: 'cards' },
                            { label: __('Minimal', 'wpcalibrate-timeline-builder'), value: 'minimal' },
                            { label: __('Modern', 'wpcalibrate-timeline-builder'), value: 'modern' },
                            { label: __('Compact', 'wpcalibrate-timeline-builder'), value: 'compact' }
                        ],
                        onChange: function (val) {
                            setAttributes({ preset: val });
                        }
                    })
                )
            );

            var content;
            if (!timelineId) {
                content = el(
                    'div',
                    { className: 'wpcal-block-placeholder' },
                    el('div', { className: 'wpcal-block-placeholder-icon' }, '⏱'),
                    el('h4', null, __('WPCalibrate Timeline', 'wpcalibrate-timeline-builder')),
                    el(
                        'p',
                        null,
                        timelinesList.length > 0
                            ? __('Please select a timeline from the block settings in the sidebar.', 'wpcalibrate-timeline-builder')
                            : __('No timelines found. Please create a timeline under WPCalibrate > Timeline Builder.', 'wpcalibrate-timeline-builder')
                    )
                );
            } else if (ServerSideRender) {
                content = el(ServerSideRender, {
                    block: 'wpcalibrate/timeline',
                    attributes: attributes
                });
            } else {
                content = el(
                    'div',
                    { className: 'wpcal-block-preview' },
                    el('p', null, __('WPCalibrate Timeline #' + timelineId, 'wpcalibrate-timeline-builder'))
                );
            }

            return el(
                wp.element.Fragment,
                null,
                inspector,
                content
            );
        },

        save: function () {
            // Dynamic block rendered via PHP server-side callback.
            return null;
        }
    });
})(window.wp);
