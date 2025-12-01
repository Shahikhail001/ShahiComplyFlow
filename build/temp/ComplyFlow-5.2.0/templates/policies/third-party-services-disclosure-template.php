<?php
/**
 * Third-Party Services Disclosure Template
 *
 * @package ComplyFlow
 * @since   4.9.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="complyflow-policy third-party-disclosure">
    <style>
        .third-party-disclosure {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
        }
        .third-party-disclosure h1 {
            color: #2563eb;
            font-size: 32px;
            margin-bottom: 10px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        .third-party-disclosure .policy-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .third-party-disclosure h2 {
            color: #1e40af;
            font-size: 24px;
            margin-top: 40px;
            margin-bottom: 15px;
            padding-left: 15px;
            border-left: 4px solid #3b82f6;
        }
        .third-party-disclosure h3 {
            color: #1e40af;
            font-size: 20px;
            margin-top: 25px;
            margin-bottom: 12px;
        }
        .third-party-disclosure h4 {
            color: #374151;
            font-size: 17px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .third-party-disclosure .policy-section {
            margin-bottom: 35px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }
        .third-party-disclosure ul, 
        .third-party-disclosure ol {
            margin: 15px 0;
            padding-left: 30px;
        }
        .third-party-disclosure li {
            margin: 8px 0;
        }
        .third-party-disclosure .info-box,
        .third-party-disclosure .no-services-box,
        .third-party-disclosure .marketing-notice,
        .third-party-disclosure .data-sharing-box,
        .third-party-disclosure .important-notice,
        .third-party-disclosure .control-promo,
        .third-party-disclosure .contact-box {
            background: #ffffff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .third-party-disclosure .no-services-box {
            background: #f0fdf4;
            border-left-color: #22c55e;
        }
        .third-party-disclosure .marketing-notice,
        .third-party-disclosure .important-notice {
            background: #fef3c7;
            border-left-color: #f59e0b;
        }
        .third-party-disclosure .control-promo {
            background: #eff6ff;
            border-left-color: #2563eb;
        }
        .third-party-disclosure .service-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .third-party-disclosure .service-box.analytics {
            border-left: 4px solid #06b6d4;
        }
        .third-party-disclosure .service-box.marketing {
            border-left: 4px solid #f59e0b;
        }
        .third-party-disclosure .service-box.social {
            border-left: 4px solid #8b5cf6;
        }
        .third-party-disclosure .service-box h3 {
            margin-top: 0;
            color: #1f2937;
        }
        .third-party-disclosure .service-details-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        .third-party-disclosure .service-details-table th {
            text-align: left;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            width: 30%;
            font-weight: 600;
            color: #374151;
        }
        .third-party-disclosure .service-details-table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            color: #1f2937;
        }
        .third-party-disclosure .locations-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            font-size: 14px;
        }
        .third-party-disclosure .locations-table th {
            text-align: left;
            padding: 12px;
            background: #1e40af;
            color: #ffffff;
            border: 1px solid #1e40af;
            font-weight: 600;
        }
        .third-party-disclosure .locations-table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .third-party-disclosure .locations-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        .third-party-disclosure .btn-primary {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }
        .third-party-disclosure .btn-primary:hover {
            background: #1d4ed8;
            text-decoration: none;
        }
        .third-party-disclosure a {
            color: #2563eb;
            text-decoration: underline;
        }
        .third-party-disclosure a:hover {
            color: #1d4ed8;
        }
        .third-party-disclosure strong {
            font-weight: 600;
            color: #1f2937;
        }
        .third-party-disclosure code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #be123c;
        }
        .third-party-disclosure .contact-table {
            width: 100%;
            margin: 15px 0;
        }
        .third-party-disclosure .contact-table th {
            text-align: left;
            padding-right: 20px;
            padding-bottom: 10px;
            font-weight: 600;
            color: #374151;
            white-space: nowrap;
        }
        .third-party-disclosure .contact-table td {
            padding-bottom: 10px;
            color: #1f2937;
        }
    </style>

    <h1>Third-Party Services Disclosure</h1>
    
    <div class="policy-meta">
        <p><strong>Company:</strong> {{COMPANY_NAME}}</p>
        <p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>
        <p><strong>Last Updated:</strong> <?php echo date('F j, Y'); ?></p>
    </div>

    {{OVERVIEW_SECTION}}
    {{ANALYTICS_SERVICES_SECTION}}
    {{MARKETING_SERVICES_SECTION}}
    {{SOCIAL_MEDIA_SECTION}}
    {{DATA_SHARING_SECTION}}
    {{PROCESSING_LOCATIONS_SECTION}}
    {{YOUR_CONTROL_SECTION}}
    {{CONTACT_SECTION}}
</div>
