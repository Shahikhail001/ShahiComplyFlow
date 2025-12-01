<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - {{COMPANY_NAME}}</title>
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
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #c026d3 100%);
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
            background: #faf5ff;
            border: 2px solid #e9d5ff;
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
            border-bottom: 3px solid #a855f7;
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
            color: #a855f7;
            min-width: 28px;
        }
        .toc-list a {
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .toc-list a:hover {
            color: #a855f7;
            padding-left: 8px;
        }
        h2 {
            font-size: 28px;
            margin-top: 50px;
            margin-bottom: 20px;
            color: #0f172a;
            font-weight: 700;
            padding-bottom: 12px;
            border-bottom: 3px solid #a855f7;
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
            color: #a855f7;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.2s;
        }
        a:hover {
            border-bottom-color: #a855f7;
        }
        .policy-section {
            margin-bottom: 50px;
        }
        .terms-important {
            background: linear-gradient(to right, #fee2e2 0%, #fef2f2 100%);
            padding: 20px 24px;
            border-left: 5px solid #ef4444;
            margin: 25px 0;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.1);
        }
        .terms-highlight {
            background: linear-gradient(to right, #fef3c7 0%, #fef9e7 100%);
            padding: 20px 24px;
            border-left: 5px solid #f59e0b;
            margin: 25px 0;
            border-radius: 4px;
        }
        @media print {
            body {
                max-width: 100%;
                padding: 20px;
            }
            .policy-header {
                background: #a855f7 !important;
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
        <h1>Terms of Service</h1>
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
            <li><a href="#acceptance">Acceptance of Terms</a></li>
            <li><a href="#eligibility">Eligibility</a></li>
            <li><a href="#account">Account Terms</a></li>
            <li><a href="#ecommerce">E-commerce Terms</a></li>
            <li><a href="#intellectual-property">Intellectual Property</a></li>
            <li><a href="#user-content">User Content</a></li>
            <li><a href="#prohibited">Prohibited Conduct</a></li>
            <li><a href="#disclaimers">Disclaimers</a></li>
            <li><a href="#liability">Limitation of Liability</a></li>
            <li><a href="#indemnification">Indemnification</a></li>
            <li><a href="#termination">Termination</a></li>
            <li><a href="#governing-law">Governing Law</a></li>
            <li><a href="#dispute">Dispute Resolution</a></li>
            <li><a href="#changes">Changes to Terms</a></li>
            <li><a href="#contact">Contact Information</a></li>
        </ol>
    </div>

    <div id="introduction" class="policy-section">
        {{INTRODUCTION_SECTION}}
    </div>

    <div id="acceptance" class="policy-section">
        {{ACCEPTANCE_SECTION}}
    </div>

    <div id="eligibility" class="policy-section">
        {{ELIGIBILITY_SECTION}}
    </div>

    <div id="account" class="policy-section">
        {{ACCOUNT_SECTION}}
    </div>

    <div id="ecommerce" class="policy-section">
        {{ECOMMERCE_SECTION}}
    </div>

    <div id="intellectual-property" class="policy-section">
        {{INTELLECTUAL_PROPERTY_SECTION}}
    </div>

    <div id="user-content" class="policy-section">
        {{USER_CONTENT_SECTION}}
    </div>

    <div id="prohibited" class="policy-section">
        {{PROHIBITED_CONDUCT_SECTION}}
    </div>

    <div id="disclaimers" class="policy-section">
        {{DISCLAIMERS_SECTION}}
    </div>

    <div id="liability" class="policy-section">
        {{LIABILITY_SECTION}}
    </div>

    <div id="indemnification" class="policy-section">
        {{INDEMNIFICATION_SECTION}}
    </div>

    <div id="termination" class="policy-section">
        {{TERMINATION_SECTION}}
    </div>

    <div id="governing-law" class="policy-section">
        {{GOVERNING_LAW_SECTION}}
    </div>

    <div id="dispute" class="policy-section">
        {{DISPUTE_RESOLUTION_SECTION}}
    </div>

    <div id="changes" class="policy-section">
        {{CHANGES_SECTION}}
    </div>

    <div id="contact" class="policy-section">
        {{CONTACT_SECTION}}
    </div>

    <div style="margin-top: 60px; padding-top: 30px; border-top: 2px solid #e2e8f0; text-align: center; color: #64748b; font-size: 14px;">
        <p><strong>{{COMPANY_NAME}}</strong> • {{WEBSITE_URL}}</p>
        <p>By using our services, you agree to these Terms of Service.</p>
    </div>
</body>
</html>
