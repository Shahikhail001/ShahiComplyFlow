<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Protection Policy - {{COMPANY_NAME}}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #1a1a1a;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
        }
        h2 {
            font-size: 24px;
            margin-top: 35px;
            margin-bottom: 15px;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 8px;
        }
        h3 {
            font-size: 20px;
            margin-top: 25px;
            margin-bottom: 10px;
            color: #34495e;
        }
        h4 {
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #555;
        }
        p {
            margin-bottom: 15px;
        }
        ul, ol {
            margin-bottom: 15px;
            padding-left: 30px;
        }
        li {
            margin-bottom: 8px;
        }
        strong {
            font-weight: 600;
            color: #2c3e50;
        }
        a {
            color: #3498db;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.3s ease;
        }
        a:hover {
            border-bottom-color: #3498db;
        }
        .policy-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .policy-header h1 {
            color: white;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 15px;
        }
        .policy-meta {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 10px;
        }
        .policy-section {
            margin-bottom: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .compliance-section {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .compliance-section h3 {
            color: #3498db;
            margin-top: 0;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
            border-radius: 3px;
        }
        .rights-summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .rights-summary-table thead {
            background: #2c3e50;
            color: white;
        }
        .rights-summary-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        .rights-summary-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .rights-summary-table tbody tr:hover {
            background: #f8f9fa;
        }
        .rights-summary-table tbody tr:last-child td {
            border-bottom: none;
        }
        @media print {
            body {
                max-width: 100%;
            }
            .policy-header {
                background: #667eea;
                -webkit-print-color-adjust: exact;
            }
        }
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            h1 {
                font-size: 24px;
            }
            h2 {
                font-size: 20px;
            }
            .rights-summary-table {
                font-size: 14px;
            }
            .rights-summary-table th,
            .rights-summary-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="policy-header">
        <h1>Data Protection Policy</h1>
        <p><strong>{{COMPANY_NAME}}</strong></p>
        <div class="policy-meta">
            <strong>Effective Date:</strong> {{EFFECTIVE_DATE}}<br>
            <strong>Last Updated:</strong> {{LAST_UPDATED}}<br>
            <strong>Website:</strong> <a href="{{WEBSITE_URL}}" style="color: white;">{{WEBSITE_NAME}}</a>
        </div>
    </div>

    <div class="policy-section">
        <h2>Introduction</h2>
        <p>This Data Protection Policy outlines how <strong>{{COMPANY_NAME}}</strong> complies with applicable data protection and privacy laws worldwide. We are committed to protecting the privacy and security of personal data in accordance with the highest international standards.</p>
        
        <p>This policy describes:</p>
        <ul>
            <li>The legal frameworks we comply with</li>
            <li>Your rights under each applicable law</li>
            <li>How we protect your data internationally</li>
            <li>How to contact our Data Protection Officer (if appointed)</li>
            <li>The procedures for exercising your data protection rights</li>
        </ul>
    </div>

    <!-- GDPR Section -->
    {{GDPR_SECTION}}

    <!-- UK GDPR Section -->
    {{UK_GDPR_SECTION}}

    <!-- CCPA Section -->
    {{CCPA_SECTION}}

    <!-- LGPD Section -->
    {{LGPD_SECTION}}

    <!-- PIPEDA Section -->
    {{PIPEDA_SECTION}}

    <!-- PDPA Singapore Section -->
    {{PDPA_SG_SECTION}}

    <!-- PDPA Thailand Section -->
    {{PDPA_TH_SECTION}}

    <!-- APPI Section -->
    {{APPI_SECTION}}

    <!-- POPIA Section -->
    {{POPIA_SECTION}}

    <!-- KVKK Section -->
    {{KVKK_SECTION}}

    <!-- PDPL Section -->
    {{PDPL_SECTION}}

    <!-- DPO Section -->
    <div class="policy-section">
        {{DPO_SECTION}}
    </div>

    <!-- Data Transfers Section -->
    <div class="policy-section">
        {{DATA_TRANSFERS_SECTION}}
    </div>

    <!-- Rights Summary Section -->
    <div class="policy-section">
        {{RIGHTS_SUMMARY_SECTION}}
    </div>

    <!-- Contact Section -->
    <div class="policy-section">
        {{CONTACT_SECTION}}
    </div>

    <div class="policy-section">
        <h2>Updates to This Policy</h2>
        <p>We may update this Data Protection Policy from time to time to reflect changes in our practices or legal requirements. We will notify you of any material changes by:</p>
        <ul>
            <li>Posting the updated policy on our website</li>
            <li>Updating the "Last Updated" date at the top of this policy</li>
            <li>Sending email notifications for significant changes (if required by law)</li>
        </ul>
        <p>We encourage you to review this policy periodically to stay informed about how we protect your data.</p>
    </div>
</body>
</html>
