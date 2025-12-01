<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie Policy - {{COMPANY_NAME}}</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #fb923c 50%, #fbbf24 100%);
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
            background: #fffbeb;
            border: 2px solid #fef3c7;
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
            border-bottom: 3px solid #f59e0b;
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
            color: #f59e0b;
            min-width: 28px;
        }
        .toc-list a {
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .toc-list a:hover {
            color: #f59e0b;
            padding-left: 8px;
        }
        h2 {
            font-size: 28px;
            margin-top: 50px;
            margin-bottom: 20px;
            color: #0f172a;
            font-weight: 700;
            padding-bottom: 12px;
            border-bottom: 3px solid #f59e0b;
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
            color: #f59e0b;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.2s;
        }
        a:hover {
            border-bottom-color: #f59e0b;
        }
        code {
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: "Courier New", monospace;
            font-size: 13px;
            color: #dc2626;
        }
        .policy-section {
            margin-bottom: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
        }
        table th {
            background: #f59e0b;
            color: white;
            padding: 14px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }
        table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        table tr:hover {
            background: #fffbeb;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        .cookie-type {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
        }
        .cookie-type-essential {
            background: #dcfce7;
            color: #15803d;
        }
        .cookie-type-analytics {
            background: #dbeafe;
            color: #1e40af;
        }
        .cookie-type-marketing {
            background: #fce7f3;
            color: #be185d;
        }
        .cookie-highlight {
            background: linear-gradient(to right, #d1fae5 0%, #ecfdf5 100%);
            padding: 20px 24px;
            border-left: 5px solid #10b981;
            margin: 25px 0;
            border-radius: 4px;
        }
        @media print {
            body {
                max-width: 100%;
                padding: 20px;
            }
            .policy-header {
                background: #f59e0b !important;
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
            table {
                font-size: 12px;
            }
            table th, table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="policy-header">
        <h1>🍪 Cookie Policy</h1>
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
            <li><a href="#what-are-cookies">What Are Cookies?</a></li>
            <li><a href="#cookies-we-use">Cookies We Use</a></li>
            <li><a href="#cookie-categories">Cookie Categories</a></li>
            <li><a href="#third-party">Third-Party Cookies</a></li>
            <li><a href="#managing">Managing Cookies</a></li>
            <li><a href="#consent">Cookie Consent</a></li>
            <li><a href="#updates">Updates to This Policy</a></li>
            <li><a href="#contact">Contact Information</a></li>
        </ol>
    </div>

    <div id="introduction" class="policy-section">
        {{INTRODUCTION_SECTION}}
    </div>

    <div id="what-are-cookies" class="policy-section">
        {{WHAT_ARE_COOKIES_SECTION}}
    </div>

    <div id="cookies-we-use" class="policy-section">
        {{COOKIES_WE_USE_SECTION}}
    </div>

    <div id="cookie-categories" class="policy-section">
        {{COOKIE_CATEGORIES_SECTION}}
    </div>

    <div id="third-party" class="policy-section">
        {{THIRD_PARTY_COOKIES_SECTION}}
    </div>

    <div id="managing" class="policy-section">
        {{MANAGING_COOKIES_SECTION}}
    </div>

    <div id="consent" class="policy-section">
        {{CONSENT_SECTION}}
    </div>

    <div id="updates" class="policy-section">
        {{UPDATES_SECTION}}
    </div>

    <div id="contact" class="policy-section">
        {{CONTACT_SECTION}}
    </div>

    <div style="margin-top: 60px; padding-top: 30px; border-top: 2px solid #e2e8f0; text-align: center; color: #64748b; font-size: 14px;">
        <p><strong>{{COMPANY_NAME}}</strong> • {{WEBSITE_URL}}</p>
        <p>For questions about cookies, please contact us at the address above.</p>
    </div>
</body>
</html>
