<?php
/**
 * Consent Management Policy Template
 *
 * @package ComplyFlow
 * @since   4.9.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="complyflow-policy consent-management-policy">
    <style>
        .consent-management-policy {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
        }
        .consent-management-policy h1 {
            color: #2563eb;
            font-size: 32px;
            margin-bottom: 10px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        .consent-management-policy .policy-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .consent-management-policy h2 {
            color: #1e40af;
            font-size: 24px;
            margin-top: 40px;
            margin-bottom: 15px;
            padding-left: 15px;
            border-left: 4px solid #3b82f6;
        }
        .consent-management-policy h3 {
            color: #1e40af;
            font-size: 20px;
            margin-top: 25px;
            margin-bottom: 12px;
        }
        .consent-management-policy h4 {
            color: #374151;
            font-size: 17px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .consent-management-policy .policy-section {
            margin-bottom: 35px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }
        .consent-management-policy ul, 
        .consent-management-policy ol {
            margin: 15px 0;
            padding-left: 30px;
        }
        .consent-management-policy li {
            margin: 8px 0;
        }
        .consent-management-policy .consent-mode-box,
        .consent-management-policy .cookie-category-detail,
        .consent-management-policy .consent-action-box,
        .consent-management-policy .preferences-link-box,
        .consent-management-policy .technical-details-box,
        .consent-management-policy .consent-log-box,
        .consent-management-policy .log-detail-box,
        .consent-management-policy .log-purpose-box,
        .consent-management-policy .blocked-service-category,
        .consent-management-policy .geo-region-box,
        .consent-management-policy .framework-detail,
        .consent-management-policy .compliance-status-box,
        .consent-management-policy .contact-box {
            background: #ffffff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .consent-management-policy .consent-mode-box h4,
        .consent-management-policy .geo-region-box h4 {
            margin-top: 0;
            color: #1e40af;
        }
        .consent-management-policy .cookie-category-detail {
            border-left-color: #10b981;
        }
        .consent-management-policy .consent-action-box {
            border-left-color: #8b5cf6;
        }
        .consent-management-policy .preferences-link-box {
            background: #eff6ff;
            border-left-color: #2563eb;
            text-align: center;
        }
        .consent-management-policy .btn-primary {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }
        .consent-management-policy .btn-primary:hover {
            background: #1d4ed8;
            text-decoration: none;
        }
        .consent-management-policy code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #dc2626;
        }
        .consent-management-policy pre {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .consent-management-policy pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }
        .consent-management-policy a {
            color: #2563eb;
            text-decoration: underline;
        }
        .consent-management-policy a:hover {
            color: #1d4ed8;
        }
        .consent-management-policy strong {
            font-weight: 600;
            color: #1f2937;
        }
    </style>

    <h1>Consent Management Policy</h1>
    
    <div class="policy-meta">
        <p><strong>Company:</strong> {{COMPANY_NAME}}</p>
        <p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>
        <p><strong>Last Updated:</strong> <?php echo date('F j, Y'); ?></p>
    </div>

    {{OVERVIEW_SECTION}}
    
    {{HOW_CONSENT_WORKS_SECTION}}
    
    {{COOKIE_CATEGORIES_SECTION}}
    
    {{CONSENT_BANNER_SECTION}}
    
    {{PREFERENCES_CENTER_SECTION}}
    
    {{WITHDRAWING_CONSENT_SECTION}}
    
    {{CONSENT_STORAGE_SECTION}}
    
    {{GEO_TARGETING_SECTION}}
    
    {{SCRIPT_BLOCKING_SECTION}}
    
    {{CONSENT_LOGGING_SECTION}}
    
    {{COMPLIANCE_MODES_SECTION}}
    
    {{UPDATES_SECTION}}
    
    {{CONTACT_SECTION}}
</div>
