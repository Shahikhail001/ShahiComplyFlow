<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - {{WEBSITE_NAME}}</title>
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
        .policy-container {
            background: #fff;
            border-radius: 8px;
        }
        .policy-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #0ea5e9 100%);
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
            background: #f8fafc;
            border: 2px solid #e2e8f0;
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
            border-bottom: 3px solid #2563eb;
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
            color: #2563eb;
            min-width: 28px;
        }
        .toc-list a {
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .toc-list a:hover {
            color: #2563eb;
            padding-left: 8px;
        }
        h2 {
            font-size: 28px;
            margin-top: 50px;
            margin-bottom: 20px;
            color: #0f172a;
            font-weight: 700;
            padding-bottom: 12px;
            border-bottom: 3px solid #2563eb;
            scroll-margin-top: 20px;
        }
        h3 {
            font-size: 22px;
            margin-top: 30px;
            margin-bottom: 16px;
            color: #1e293b;
            font-weight: 600;
        }
        h4 {
            font-size: 18px;
            margin-top: 24px;
            margin-bottom: 12px;
            color: #334155;
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
            color: #2563eb;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.2s;
        }
        a:hover {
            border-bottom-color: #2563eb;
        }
        .policy-section {
            margin-bottom: 50px;
        }
        .highlight {
            background: linear-gradient(to right, #fef3c7 0%, #fef9e7 100%);
            padding: 20px 24px;
            border-left: 5px solid #f59e0b;
            margin: 25px 0;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.1);
        }
        .info-box {
            background: linear-gradient(to right, #dbeafe 0%, #eff6ff 100%);
            padding: 20px 24px;
            border-left: 5px solid #3b82f6;
            margin: 25px 0;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }
        .contact-info {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 24px;
            margin-top: 20px;
        }
        .contact-info ul {
            list-style: none;
            padding-left: 0;
        }
        .contact-info li {
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .contact-info li:last-child {
            border-bottom: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        table th {
            background: #2563eb;
            color: white;
            padding: 14px;
            text-align: left;
            font-weight: 600;
        }
        table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        table tr:hover {
            background: #f8fafc;
        }
        @media print {
            body {
                max-width: 100%;
                padding: 20px;
            }
            .policy-header {
                background: #2563eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .toc-container {
                page-break-after: always;
            }
            h2 {
                page-break-after: avoid;
            }
            .policy-section {
                page-break-inside: avoid;
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
            .toc-container {
                padding: 20px;
            }
            h2 {
                font-size: 24px;
            }
            .effective-date {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="policy-container">
        <div class="policy-header">
            <h1>Privacy Policy</h1>
            <p class="company-name">{{COMPANY_NAME}}</p>
            <div class="effective-date">
                <span>📅 <strong>Effective:</strong> {{EFFECTIVE_DATE}}</span>
                <span>🔄 <strong>Last Updated:</strong> {{LAST_UPDATED}}</span>
            </div>
        </div>

        <div class="toc-container">
            <div class="toc-title">📋 Table of Contents</div>
            <ol class="toc-list">
                <li><a href="#introduction">Introduction</a></li>
                <li><a href="#information-collect">Information We Collect</a></li>
                <li><a href="#how-we-use">How We Use Your Information</a></li>
                <li><a href="#cookies">Cookies and Tracking Technologies</a></li>
                <li><a href="#third-party">Third-Party Services</a></li>
                <li><a href="#data-storage">Data Storage and Security</a></li>
                <li><a href="#your-rights">Your Rights and Choices</a></li>
                <li><a href="#dpo">Data Protection Officer</a></li>
                <li><a href="#children">Children's Privacy</a></li>
                <li><a href="#regional">Regional Compliance</a></li>
                <li><a href="#changes">Changes to This Policy</a></li>
                <li><a href="#contact">Contact Information</a></li>
            </ol>
        </div>

        <div id="introduction" class="policy-section">
            {{INTRODUCTION_SECTION}}
        </div>

        <div id="information-collect" class="policy-section">
            <h2>Information We Collect</h2>
            {{DATA_COLLECTION_SECTION}}
        </div>

        <div id="how-we-use" class="policy-section">
            <h2>How We Use Your Information</h2>
            {{DATA_USAGE_SECTION}}
        </div>

        <div id="cookies" class="policy-section">
            <h2>Cookies and Tracking Technologies</h2>
            {{COOKIES_SECTION}}
        </div>

        <div id="third-party" class="policy-section">
            <h2>Third-Party Services</h2>
            {{THIRD_PARTY_SECTION}}
        </div>

        <div id="data-storage" class="policy-section">
            <h2>Data Storage and Security</h2>
            {{DATA_STORAGE_SECTION}}
        </div>

        <div id="your-rights" class="policy-section">
            {{USER_RIGHTS_SECTION}}
        </div>

        <div id="dpo" class="policy-section">
            {{DPO_SECTION}}
        </div>

        <div id="children" class="policy-section">
            {{CHILDREN_SECTION}}
        </div>

        <div id="regional" class="policy-section">
            {{REGIONAL_COMPLIANCE_SECTION}}
        </div>

        <div id="changes" class="policy-section">
            {{CHANGES_SECTION}}
        </div>

        <div id="contact" class="policy-section">
            {{CONTACT_SECTION}}
        </div>

        <div style="margin-top: 60px; padding-top: 30px; border-top: 2px solid #e2e8f0; text-align: center; color: #64748b; font-size: 14px;">
            <p><strong>{{COMPANY_NAME}}</strong> • {{WEBSITE_URL}}</p>
            <p>This document was generated on {{LAST_UPDATED}} and is subject to change.</p>
        </div>
    </div>
</body>
</html>
