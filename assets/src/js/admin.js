/**
 * Admin JavaScript
 *
 * Main admin functionality for ComplyFlow.
 *
 * @package ComplyFlow
 * @since 1.0.0
 */

(function ($) {
    'use strict';

    /**
     * ComplyFlow Admin Object
     */
    const ComplyFlowAdmin = {
        /**
         * Initialize
         */
        init() {
            this.cacheElements();
            this.bindEvents();
            this.initComponents();
        },

        /**
         * Cache frequently accessed DOM elements
         */
        cacheElements() {
            this.$indicator = $('#complyflow-save-indicator');
            this.$indicatorSpinner = this.$indicator.find('.spinner');
            this.$notices = $('#complyflow-settings-notices');
        },

        /**
         * Bind events
         */
        bindEvents() {
            // Settings form submission
            $(document).on('submit', '.complyflow-settings-form', this.saveSettings.bind(this));

            // Tab navigation
            $(document).on('click', '.complyflow-tab', this.switchTab.bind(this));

            // Run scan button
            $(document).on('click', '.complyflow-run-scan', this.runScan.bind(this));
        },

        /**
         * Initialize components
         */
        initComponents() {
            // Initialize tooltips if present
            if (typeof tippy !== 'undefined') {
                tippy('[data-tippy-content]');
            }

            // Initialize charts if Chart.js is loaded
            if (typeof Chart !== 'undefined') {
                this.initCharts();
            }
        },

        /**
         * Save settings via AJAX
         */
        saveSettings(e) {
            e.preventDefault();

            const $form = $(e.currentTarget);
            const $button = $form.find('button[type="submit"]');
            const buttonText = $button.text();

            // Disable button
            $button.prop('disabled', true).text(complyflowAdmin.strings.saving || 'Saving...');
            this.clearNotices();
            this.setSaveIndicator(true, complyflowAdmin.strings.saving || 'Saving...');

            $.ajax({
                url: complyflowAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'complyflow_save_settings',
                    nonce: complyflowAdmin.nonce,
                    settings: $form.serialize(),
                },
                success(response) {
                    if (response.success) {
                        ComplyFlowAdmin.showNotice('success', complyflowAdmin.strings.saved);
                    } else {
                        ComplyFlowAdmin.showNotice('error', response.data.message || complyflowAdmin.strings.error);
                    }
                },
                error() {
                    ComplyFlowAdmin.showNotice('error', complyflowAdmin.strings.error);
                },
                complete() {
                    $button.prop('disabled', false).text(buttonText);
                    ComplyFlowAdmin.setSaveIndicator(false);
                },
            });
        },

        /**
         * Switch tabs
         */
        switchTab(e) {
            e.preventDefault();

            const $tab = $(e.currentTarget);
            const target = $tab.data('tab');

            // Update active states
            $('.complyflow-tab').removeClass('active');
            $tab.addClass('active');

            // Show target content
            $('.complyflow-tab-content').removeClass('active');
            $(`.complyflow-tab-content[data-tab="${target}"]`).addClass('active');

            // Update URL without reload
            if (history.pushState) {
                const url = new URL(window.location);
                url.searchParams.set('tab', target);
                history.pushState({}, '', url);
            }
        },

        /**
         * Run accessibility scan
         */
        runScan(e) {
            e.preventDefault();

            const $button = $(e.currentTarget);
            const buttonText = $button.text();

            $button.prop('disabled', true).text('Scanning...');

            $.ajax({
                url: complyflowAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'complyflow_run_scan',
                    nonce: complyflowAdmin.nonce,
                },
                success(response) {
                    if (response.success) {
                        ComplyFlowAdmin.showNotice('success', 'Scan completed successfully');
                        // Reload results
                        location.reload();
                    } else {
                        ComplyFlowAdmin.showNotice('error', response.data.message);
                    }
                },
                error() {
                    ComplyFlowAdmin.showNotice('error', 'Scan failed. Please try again.');
                },
                complete() {
                    $button.prop('disabled', false).text(buttonText);
                },
            });
        },

        /**
         * Show admin notice
         */
        showNotice(type, message) {
            const $notice = $(`
                <div class="notice notice-${type} is-dismissible">
                    <p>${message}</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss</span>
                    </button>
                </div>
            `);

            const $container = this.$notices.length ? this.$notices : $('.complyflow-page-header');

            if (this.$notices.length) {
                this.$notices.empty().append($notice);
            } else {
                $container.after($notice);
            }

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                $notice.fadeOut(() => $notice.remove());
            }, 5000);

            // Manual dismiss
            $notice.find('.notice-dismiss').on('click', function () {
                $notice.fadeOut(() => $notice.remove());
            });
        },

        /**
         * Initialize charts
         */
        initCharts() {
            // Compliance score chart
            const $scoreChart = $('#complyflow-score-chart');
            if ($scoreChart.length) {
                const score = parseInt($scoreChart.data('score'), 10) || 0;
                
                new Chart($scoreChart[0], {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [score, 100 - score],
                            backgroundColor: ['#06d6a0', '#e5e7eb'],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        cutout: '80%',
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                });
            }
        },

        /**
         * Clear notices container
         */
        clearNotices() {
            if (this.$notices.length) {
                this.$notices.empty();
            }
        },

        /**
         * Toggle save indicator
         *
         * @param {boolean} isActive
         * @param {string} [text]
         */
        setSaveIndicator(isActive, text = '') {
            if (!this.$indicator || !this.$indicator.length) {
                return;
            }

            if (isActive) {
                this.$indicator.addClass('is-active').attr('aria-hidden', 'false');
                if (this.$indicatorSpinner.length) {
                    this.$indicatorSpinner.addClass('is-active');
                }
                if (text) {
                    this.$indicator.find('.save-text').text(text);
                }
            } else {
                this.$indicator.removeClass('is-active').attr('aria-hidden', 'true');
                if (this.$indicatorSpinner.length) {
                    this.$indicatorSpinner.removeClass('is-active');
                }
            }
        },
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(() => {
        ComplyFlowAdmin.init();
    });

})(jQuery);
