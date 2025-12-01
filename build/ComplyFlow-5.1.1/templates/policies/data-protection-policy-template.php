<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Protection Policy - {{COMPANY_NAME}}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.7;
            color: #1e293b;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
            background: #ffffff;
        }
        .policy-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
            color: white;
            padding: 40px;
            border-radius: 8px 8px 0 0;
            margin-bottom: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .policy-header h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        .policy-header .company-name {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 20px;
            opacity: 0.95;
        }
        .effective-date {
            display: flex;
            gap: 24px;
            font-size: 14px;
            opacity: 0.9;
            flex-wrap: wrap;
        }
        .effective-date span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .toc-container {
            background: #f0fdf4;
            border: 2px solid #d1fae5;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 50px;
        }
        .toc-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #10b981;
        }
        .toc-list {
            list-style: none;
            counter-reset: toc-counter;
        }
        .toc-list li {
            counter-increment: toc-counter;
            margin-bottom: 12px;
            position: relative;
            padding-left: 32px;
        }
        .toc-list li::before {
            content: counter(toc-counter) ".";
            position: absolute;
            left: 0;
            font-weight: 600;
            color: #10b981;
            min-width: 28px;
        }
        .toc-list a {
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .toc-list a:hover {
            color: #10b981;
            padding-left: 8px;
        }
        h2 {
            font-size: 28px;
            margin-top: 50px;
            margin-bottom: 20px;
            color: #0f172a;
            font-weight: 700;
            padding-bottom: 12px;
            border-bottom: 3px solid #10b981;
            scroll-margin-top: 20px;
        }
        h3 {
            font-size: 22px;
            margin-top: 30px;
            margin-bottom: 16px;
            color: #1e293b;
            font-weight: 600;
        }
        p {
            margin-bottom: 18px;
            line-height: 1.8;
        }
        ul, ol {
            margin-bottom: 20px;
            padding-left: 32px;
        }
        li {
            margin-bottom: 10px;
            line-height: 1.7;
        }
        strong {
            font-weight: 600;
            color: #0f172a;
        }
        a {
            color: #10b981;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.2s;
        }
        a:hover {
            border-bottom-color: #10b981;
        }
        .policy-section {
            margin-bottom: 50px;
        }
        .compliance-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 600;
            margin: 4px;
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .info-box {
            background: linear-gradient(to right, #dbeafe 0%, #eff6ff 100%);
            padding: 20px 24px;
            border-left: 5px solid #3b82f6;
            margin: 25px 0;
            border-radius: 4px;
        }
        .dpo-contact {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 24px;
            margin: 20px 0;
        }
        .dpo-contact h4 {
            color: #10b981;
            margin-bottom: 12px;
        }
        @media print {
            body {
                max-width: 100%;
                padding: 20px;
            }
            .policy-header {
                background: #10b981 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .toc-container {
                page-break-after: always;
            }
        }
        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }
            .policy-header {
                padding: 30px 20px;
            }
            .policy-header h1 {
                font-size: 28px;
            }
            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="policy-header">
        <h1>🔒 Data Protection Policy</h1>
        <p class="company-name">{{COMPANY_NAME}}</p>
        <div class="effective-date">
            <span>📅 <strong>Effective:</strong> {{EFFECTIVE_DATE}}</span>
            <span>🔄 <strong>Last Updated:</strong> {{LAST_UPDATED}}</span>
        </div>
    </div>

    <div class="toc-container">
        <div class="toc-title">📋 Table of Contents</div>
        <ol class="toc-list">
            <li><a href="#overview">Policy Overview</a></li>
            <li><a href="#dpo">Data Protection Officer</a></li>
            <li><a href="#processing">Data Processing Activities</a></li>
            <li><a href="#legal-basis">Legal Basis for Processing</a></li>
            <li><a href="#security">Data Security Measures</a></li>
            <li><a href="#breach">Data Breach Notification Procedures</a></li>
            <li><a href="#retention">Data Retention Policy</a></li>
            <li><a href="#automated">Automated Decision-Making</a></li>
            <li><a href="#audit">Records of Processing Activities</a></li>
            <li><a href="#gdpr">GDPR Compliance (EU)</a></li>
            <li><a href="#uk-gdpr">UK GDPR Compliance</a></li>
            <li><a href="#ccpa">CCPA Compliance (California)</a></li>
            <li><a href="#lgpd">LGPD Compliance (Brazil)</a></li>
            <li><a href="#pipeda">PIPEDA Compliance (Canada)</a></li>
            <li><a href="#pdpa-sg">PDPA Compliance (Singapore)</a></li>
            <li><a href="#pdpa-th">PDPA Compliance (Thailand)</a></li>
            <li><a href="#appi">APPI Compliance (Japan)</a></li>
            <li><a href="#popia">POPIA Compliance (South Africa)</a></li>
            <li><a href="#kvkk">KVKK Compliance (Turkey)</a></li>
            <li><a href="#pdpl">PDPL Compliance (Saudi Arabia)</a></li>
            <li><a href="#australia">Australia Privacy Act</a></li>
            <li><a href="#transfers">International Data Transfers</a></li>
            <li><a href="#rights">Data Subject Rights</a></li>
            <li><a href="#contact">Contact Information</a></li>
        </ol>
    </div>

    <div id="overview" class="policy-section">
        <h2>Policy Overview</h2>
        <p>This Data Protection Policy outlines {{COMPANY_NAME}}'s commitment to protecting personal data in compliance with applicable data protection laws worldwide.</p>
        
        <div class="info-box">
            <strong>🌍 Our Compliance Framework</strong>
            <p style="margin-top: 12px;">We maintain compliance with the following data protection regulations:</p>
            <div style="margin-top: 16px;">
                {{GDPR_BADGE}}
                {{UK_GDPR_BADGE}}
                {{CCPA_BADGE}}
                {{LGPD_BADGE}}
                {{PIPEDA_BADGE}}
                {{PDPA_SG_BADGE}}
                {{PDPA_TH_BADGE}}
                {{APPI_BADGE}}
                {{POPIA_BADGE}}
                {{KVKK_BADGE}}
                {{PDPL_BADGE}}
                {{AUSTRALIA_BADGE}}
            </div>
        </div>
    </div>

    <div id="dpo" class="policy-section">
        {{DPO_SECTION}}
    </div>

    <div id="processing" class="policy-section">
        {{DATA_PROCESSING_SECTION}}
    </div>

    <div id="legal-basis" class="policy-section">
        {{LEGAL_BASIS_SECTION}}
    </div>

    <div id="security" class="policy-section">
        {{DATA_SECURITY_SECTION}}
    </div>

    <div id="breach" class="policy-section">
        {{BREACH_PROCEDURES_SECTION}}
    </div>

    <div id="retention" class="policy-section">
        {{RETENTION_POLICY_SECTION}}
    </div>

    <div id="automated" class="policy-section">
        {{AUTOMATED_DECISIONS_SECTION}}
    </div>

    <div id="audit" class="policy-section">
        {{AUDIT_RECORDS_SECTION}}
    </div>

    <div id="gdpr" class="policy-section">
        {{GDPR_SECTION}}
    </div>

    <div id="uk-gdpr" class="policy-section">
        {{UK_GDPR_SECTION}}
    </div>

    <div id="ccpa" class="policy-section">
        {{CCPA_SECTION}}
    </div>

    <div id="lgpd" class="policy-section">
        {{LGPD_SECTION}}
    </div>

    <div id="pipeda" class="policy-section">
        {{PIPEDA_SECTION}}
    </div>

    <div id="pdpa-sg" class="policy-section">
        {{PDPA_SG_SECTION}}
    </div>

    <div id="pdpa-th" class="policy-section">
        {{PDPA_TH_SECTION}}
    </div>

    <div id="appi" class="policy-section">
        {{APPI_SECTION}}
    </div>

    <div id="popia" class="policy-section">
        {{POPIA_SECTION}}
    </div>

    <div id="kvkk" class="policy-section">
        {{KVKK_SECTION}}
    </div>

    <div id="pdpl" class="policy-section">
        {{PDPL_SECTION}}
    </div>

    <div id="australia" class="policy-section">
        {{AUSTRALIA_SECTION}}
    </div>

    <div id="transfers" class="policy-section">
        {{DATA_TRANSFERS_SECTION}}
    </div>

    <div id="rights" class="policy-section">
        {{RIGHTS_SUMMARY_SECTION}}
    </div>

    <div id="contact" class="policy-section">
        {{CONTACT_SECTION}}
    </div>

    <div style="margin-top: 60px; padding-top: 30px; border-top: 2px solid #e2e8f0; text-align: center; color: #64748b; font-size: 14px;">
        <p><strong>{{COMPANY_NAME}}</strong> • {{WEBSITE_URL}}</p>
        <p>Committed to protecting your data rights across all jurisdictions.</p>
    </div>
</body>
</html>
