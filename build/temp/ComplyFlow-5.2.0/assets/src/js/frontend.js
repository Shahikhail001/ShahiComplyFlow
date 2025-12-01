/**
 * Frontend JavaScript
 *
 * Public-facing functionality for ComplyFlow.
 *
 * @package ComplyFlow
 * @since 1.0.0
 */

(function () {
    'use strict';

    /**
     * ComplyFlow Frontend Object
     */
    const ComplyFlow = {
        /**
         * Initialize
         */
        init() {
            this.initConsentManager();
        },

        /**
         * Initialize consent manager
         */
        initConsentManager() {
            // Check if consent already given
            if (this.hasConsent()) {
                this.loadConsentedScripts();
            }
        },

        /**
         * Check if user has given consent
         */
        hasConsent() {
            const consent = this.getCookie('complyflow_consent');
            return consent !== null;
        },

        /**
         * Get cookie value
         */
        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) {
                return parts.pop().split(';').shift();
            }
            return null;
        },

        /**
         * Load scripts that require consent
         */
        loadConsentedScripts() {
            const consent = JSON.parse(this.getCookie('complyflow_consent') || '{}');

            // Find all blocked scripts
            document.querySelectorAll('[data-complyflow-blocked]').forEach((placeholder) => {
                const category = placeholder.dataset.category;

                // Check if category is consented
                if (consent[category]) {
                    this.injectScript(placeholder);
                }
            });
        },

        /**
         * Inject blocked script
         */
        injectScript(placeholder) {
            const scriptSrc = placeholder.dataset.src;
            const scriptType = placeholder.dataset.type || 'text/javascript';

            const script = document.createElement('script');
            script.src = scriptSrc;
            script.type = scriptType;
            script.async = true;

            placeholder.replaceWith(script);
        },
    };

    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => ComplyFlow.init());
    } else {
        ComplyFlow.init();
    }

    // Expose to global scope
    window.ComplyFlow = ComplyFlow;

})();
