<?php
/**
 * Cookie Categories Reference Template
 *
 * @package ComplyFlow
 * @since   4.9.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="complyflow-policy cookie-categories-reference">
    <style>
        .cookie-categories-reference {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
        }
        .cookie-categories-reference h1 {
            color: #2563eb;
            font-size: 32px;
            margin-bottom: 10px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        .cookie-categories-reference .policy-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .cookie-categories-reference h2 {
            color: #1e40af;
            font-size: 24px;
            margin-top: 40px;
            margin-bottom: 15px;
            padding-left: 15px;
            border-left: 4px solid #3b82f6;
        }
        .cookie-categories-reference h3 {
            color: #1e40af;
            font-size: 20px;
            margin-top: 25px;
            margin-bottom: 12px;
        }
        .cookie-categories-reference h4 {
            color: #374151;
            font-size: 17px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .cookie-categories-reference .policy-section {
            margin-bottom: 35px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }
        .cookie-categories-reference ul, 
        .cookie-categories-reference ol {
            margin: 15px 0;
            padding-left: 30px;
        }
        .cookie-categories-reference li {
            margin: 8px 0;
        }
        .cookie-categories-reference .info-box,
        .cookie-categories-reference .consent-notice,
        .cookie-categories-reference .privacy-notice,
        .cookie-categories-reference .privacy-warning,
        .cookie-categories-reference .blocking-options,
        .cookie-categories-reference .management-promo,
        .cookie-categories-reference .recommendations-box,
        .cookie-categories-reference .contact-box {
            background: #ffffff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .cookie-categories-reference .management-promo {
            background: #eff6ff;
            border-left-color: #2563eb;
            text-align: center;
        }
        .cookie-categories-reference .recommendations-box {
            background: #f0fdf4;
            border-left-color: #22c55e;
        }
        .cookie-categories-reference .privacy-warning {
            background: #fef3c7;
            border-left-color: #f59e0b;
        }
        .cookie-categories-reference .category-status-box {
            background: #ffffff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: center;
        }
        .cookie-categories-reference .category-status-box.necessary {
            background: #f0fdf4;
            border-left-color: #22c55e;
        }
        .cookie-categories-reference .category-status-box.analytics-detected {
            background: #dbeafe;
            border-left-color: #06b6d4;
        }
        .cookie-categories-reference .category-status-box.analytics-none {
            background: #f0fdf4;
            border-left-color: #22c55e;
        }
        .cookie-categories-reference .category-status-box.marketing {
            background: #fef3c7;
            border-left-color: #f59e0b;
        }
        .cookie-categories-reference .category-status-box.preferences {
            background: #ede9fe;
            border-left-color: #8b5cf6;
        }
        .cookie-categories-reference .cookie-detail-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .cookie-categories-reference .cookie-detail-box h4 {
            margin-top: 0;
            color: #1f2937;
        }
        .cookie-categories-reference .cookie-table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
            font-size: 14px;
        }
        .cookie-categories-reference .cookie-table th {
            text-align: left;
            padding: 8px 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            width: 35%;
            font-weight: 600;
            color: #374151;
        }
        .cookie-categories-reference .cookie-table td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            color: #1f2937;
        }
        .cookie-categories-reference .control-table,
        .cookie-categories-reference .comparison-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            font-size: 14px;
        }
        .cookie-categories-reference .control-table th,
        .cookie-categories-reference .comparison-table th {
            text-align: left;
            padding: 12px;
            background: #1e40af;
            color: #ffffff;
            border: 1px solid #1e40af;
            font-weight: 600;
        }
        .cookie-categories-reference .control-table td,
        .cookie-categories-reference .comparison-table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .cookie-categories-reference .control-table tbody tr:nth-child(even),
        .cookie-categories-reference .comparison-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        .cookie-categories-reference .btn-primary {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }
        .cookie-categories-reference .btn-primary:hover {
            background: #1d4ed8;
            text-decoration: none;
        }
        .cookie-categories-reference a {
            color: #2563eb;
            text-decoration: underline;
        }
        .cookie-categories-reference a:hover {
            color: #1d4ed8;
        }
        .cookie-categories-reference strong {
            font-weight: 600;
            color: #1f2937;
        }
        .cookie-categories-reference code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #be123c;
        }
        .cookie-categories-reference .contact-table {
            width: 100%;
            margin: 15px 0;
        }
        .cookie-categories-reference .contact-table th {
            text-align: left;
            padding-right: 20px;
            padding-bottom: 10px;
            font-weight: 600;
            color: #374151;
            white-space: nowrap;
        }
        .cookie-categories-reference .contact-table td {
            padding-bottom: 10px;
            color: #1f2937;
        }
    </style>

    <h1>Cookie Categories Reference</h1>
    
    <div class="policy-meta">
        <p><strong>Company:</strong> {{COMPANY_NAME}}</p>
        <p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>
        <p><strong>Last Updated:</strong> <?php echo date('F j, Y'); ?></p>
    </div>

    {{OVERVIEW_SECTION}}
    {{NECESSARY_COOKIES_SECTION}}
    {{ANALYTICS_COOKIES_SECTION}}
    {{MARKETING_COOKIES_SECTION}}
    {{PREFERENCES_COOKIES_SECTION}}
    {{HOW_TO_MANAGE_SECTION}}
    {{CONTACT_SECTION}}
</div>
