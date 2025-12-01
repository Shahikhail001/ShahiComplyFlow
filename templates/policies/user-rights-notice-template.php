<?php
/**
 * User Rights Notice Template
 *
 * @package ComplyFlow
 * @since   4.9.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="complyflow-policy user-rights-notice">
    <style>
        .user-rights-notice {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
        }
        .user-rights-notice h1 {
            color: #2563eb;
            font-size: 32px;
            margin-bottom: 10px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        .user-rights-notice .policy-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .user-rights-notice h2 {
            color: #1e40af;
            font-size: 24px;
            margin-top: 40px;
            margin-bottom: 15px;
            padding-left: 15px;
            border-left: 4px solid #3b82f6;
        }
        .user-rights-notice h3 {
            color: #1e40af;
            font-size: 20px;
            margin-top: 25px;
            margin-bottom: 12px;
        }
        .user-rights-notice h4 {
            color: #374151;
            font-size: 17px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .user-rights-notice .policy-section {
            margin-bottom: 35px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }
        .user-rights-notice ul, 
        .user-rights-notice ol {
            margin: 15px 0;
            padding-left: 30px;
        }
        .user-rights-notice li {
            margin: 8px 0;
        }
        .user-rights-notice .rights-overview-box,
        .user-rights-notice .info-box,
        .user-rights-notice .deletion-process-box,
        .user-rights-notice .portable-data-box,
        .user-rights-notice .objection-type-box,
        .user-rights-notice .restriction-box,
        .user-rights-notice .withdrawal-method-box,
        .user-rights-notice .automated-decision-box,
        .user-rights-notice .ccpa-right-box,
        .user-rights-notice .dsr-portal-promo,
        .user-rights-notice .request-checklist,
        .user-rights-notice .portal-feature-box,
        .user-rights-notice .timeline-box,
        .user-rights-notice .verification-method-box,
        .user-rights-notice .authority-box,
        .user-rights-notice .contact-box {
            background: #ffffff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .user-rights-notice .dsr-portal-promo {
            background: #eff6ff;
            border-left-color: #2563eb;
            text-align: center;
        }
        .user-rights-notice .btn-primary {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }
        .user-rights-notice .btn-primary:hover {
            background: #1d4ed8;
            text-decoration: none;
        }
        .user-rights-notice a {
            color: #2563eb;
            text-decoration: underline;
        }
        .user-rights-notice a:hover {
            color: #1d4ed8;
        }
        .user-rights-notice strong {
            font-weight: 600;
            color: #1f2937;
        }
        .user-rights-notice table {
            font-size: 14px;
        }
    </style>

    <h1>Your Data Privacy Rights</h1>
    
    <div class="policy-meta">
        <p><strong>Company:</strong> {{COMPANY_NAME}}</p>
        <p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>
        <p><strong>Last Updated:</strong> <?php echo date('F j, Y'); ?></p>
    </div>

    {{OVERVIEW_SECTION}}
    {{RIGHT_TO_ACCESS_SECTION}}
    {{RIGHT_TO_RECTIFICATION_SECTION}}
    {{RIGHT_TO_ERASURE_SECTION}}
    {{RIGHT_TO_PORTABILITY_SECTION}}
    {{RIGHT_TO_OBJECT_SECTION}}
    {{RIGHT_TO_RESTRICT_SECTION}}
    {{RIGHT_TO_WITHDRAW_SECTION}}
    {{AUTOMATED_DECISIONS_SECTION}}
    {{CALIFORNIA_RIGHTS_SECTION}}
    {{HOW_TO_EXERCISE_SECTION}}
    {{DSR_PORTAL_SECTION}}
    {{RESPONSE_TIMELINE_SECTION}}
    {{VERIFICATION_SECTION}}
    {{COMPLAINTS_SECTION}}
    {{CONTACT_SECTION}}
</div>
