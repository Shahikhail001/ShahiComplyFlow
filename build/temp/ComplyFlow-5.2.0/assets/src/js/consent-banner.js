/**
 * Consent Banner JavaScript
 *
 * Handles cookie consent banner interactions.
 *
 * @package ComplyFlow
 * @since   1.0.0
 */

(function($) {
    'use strict';

    /**
     * Consent Banner Manager
     */
    var ConsentBanner = {
        /**
         * Initialize
         */
        init: function() {
            this.checkConsent();
            this.bindEvents();
        },

        /**
         * Check if user has already consented
         */
        checkConsent: function() {
            var consent = this.getConsent();
            
            if (consent) {
                // User has consented, hide banner
                $('#complyflow-consent-banner').remove();
            } else {
                // No consent yet, show the banner after 3 seconds
                setTimeout(function() {
                    $('#complyflow-consent-banner').fadeIn(600);
                }, 3000);
            }
        },

        /**
         * Get consent from cookie
         *
         * @return {Object|null} Consent object or null.
         */
        getConsent: function() {
            var name = 'complyflow_consent=';
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) === 0) {
                    try {
                        return JSON.parse(c.substring(name.length, c.length));
                    } catch (e) {
                        return null;
                    }
                }
            }
            
            return null;
        },

        /**
         * Set consent cookie
         *
         * @param {Object} consent Consent object.
         */
        setConsent: function(consent) {
            var duration = complyflowConsent.settings.consentDuration || 365;
            var date = new Date();
            date.setTime(date.getTime() + (duration * 24 * 60 * 60 * 1000));
            
            var expires = 'expires=' + date.toUTCString();
            document.cookie = 'complyflow_consent=' + JSON.stringify(consent) + ';' + expires + ';path=/;SameSite=Lax';
            
            // Trigger event for script unblocking
            var event = new CustomEvent('complyflowConsentUpdated', {
                detail: consent
            });
            document.dispatchEvent(event);
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;

            // Accept all button
            $(document).on('click', '#cf-consent-accept-all', function(e) {
                e.preventDefault();
                self.acceptAll();
            });

            // Reject all button
            $(document).on('click', '#cf-consent-reject-all', function(e) {
                e.preventDefault();
                self.rejectAll();
            });

            // Save preferences button
            $(document).on('click', '#cf-consent-save-preferences', function(e) {
                e.preventDefault();
                self.savePreferences();
            });

            // Cookie settings link
            $(document).on('click', '#cf-cookie-settings', function(e) {
                e.preventDefault();
                self.showPreferences();
            });

            // Toggle switch changes
            $(document).on('change', '.cf-consent-toggle input', function() {
                var $toggle = $(this);
                var category = $toggle.attr('id').replace('complyflow-toggle-', '');
                
                // Visual feedback
                $toggle.closest('.cf-consent-category').toggleClass('active', $toggle.prop('checked'));
            });
        },

        /**
         * Accept all cookies
         */
        acceptAll: function() {
            var self = this;
            var consent = {
                necessary: true,
                analytics: true,
                marketing: true,
                preferences: true
            };

            // Fade out banner before saving
            $('#complyflow-consent-banner').fadeOut(400, function() {
                self.saveConsent(consent);
            });
        },

        /**
         * Reject all cookies
         */
        rejectAll: function() {
            var self = this;
            var consent = {
                necessary: true,
                analytics: false,
                marketing: false,
                preferences: false
            };

            // Fade out banner before saving
            $('#complyflow-consent-banner').fadeOut(400, function() {
                self.saveConsent(consent);
            });
        },

        /**
         * Save custom preferences
         */
        savePreferences: function() {
            var self = this;
            var consent = {
                necessary: true,
                analytics: $('#cf-toggle-analytics').prop('checked'),
                marketing: $('#cf-toggle-marketing').prop('checked'),
                preferences: $('#cf-toggle-preferences').prop('checked')
            };

            // Fade out banner before saving
            $('#complyflow-consent-banner').fadeOut(400, function() {
                self.saveConsent(consent);
            });
        },

        /**
         * Save consent
         *
         * @param {Object} consent Consent object.
         */
        saveConsent: function(consent) {
            var self = this;

            // Show loading state
            this.showLoading();

            // Prepare data
            var ajaxData = {
                action: 'complyflow_save_consent',
                nonce: complyflowConsent.nonce,
                necessary: consent.necessary ? 'true' : 'false',
                analytics: consent.analytics ? 'true' : 'false',
                marketing: consent.marketing ? 'true' : 'false',
                preferences: consent.preferences ? 'true' : 'false'
            };

            // Save to server
            $.ajax({
                url: complyflowConsent.ajaxUrl,
                type: 'POST',
                data: ajaxData,
                success: function(response) {
                    if (response.success) {
                        // Set cookie
                        self.setConsent(consent);

                        // Banner already faded out, just reload
                        setTimeout(function() {
                            window.location.reload();
                        }, 300);
                    } else {
                        console.error('ComplyFlow: Save failed', response);
                        alert(response.data.message || 'Failed to save consent');
                        self.hideLoading();
                        // Show banner again on error
                        $('#complyflow-consent-banner').fadeIn(300);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('ComplyFlow: AJAX error', xhr, status, error);
                    alert('Failed to save consent. Please try again.');
                    self.hideLoading();
                }
            });
        },

        /**
         * Show preferences center
         */
        showPreferences: function() {
            // If already showing categories, just scroll to them
            if ($('.cf-consent-categories').is(':visible')) {
                $('html, body').animate({
                    scrollTop: $('.cf-consent-categories').offset().top - 100
                }, 500);
                return;
            }

            // Show categories section
            $('.cf-consent-categories').slideDown();
            
            // Update button text
            $('#complyflow-cookie-settings').text('Hide Settings');
        },

        /**
         * Hide banner
         */
        hideBanner: function() {
            $('#complyflow-consent-banner').fadeOut(300, function() {
                $(this).remove();
            });
        },

        /**
         * Show loading state
         */
        showLoading: function() {
            $('.cf-consent-actions button').prop('disabled', true).addClass('loading');
        },

        /**
         * Hide loading state
         */
        hideLoading: function() {
            $('.cf-consent-actions button').prop('disabled', false).removeClass('loading');
        }
    };

    /**
     * Cookie Scanner
     */
    var CookieScanner = {
        /**
         * Scan cookies
         *
         * @param {string} url URL to scan.
         * @return {Promise} Promise that resolves with scan results.
         */
        scan: function(url) {
            return $.ajax({
                url: complyflowConsent.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'complyflow_scan_cookies',
                    nonce: complyflowConsent.nonce,
                    url: url || window.location.href
                }
            });
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        ConsentBanner.init();
    });

    // Export to global scope
    window.ComplyFlowConsent = {
        banner: ConsentBanner,
        scanner: CookieScanner
    };

})(jQuery);
