<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie Policy - {{COMPANY_NAME}}</title>
    <style>
        .complyflow-cookie-policy {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .complyflow-cookie-policy h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }

        .complyflow-cookie-policy h2 {
            font-size: 24px;
            margin-top: 40px;
            margin-bottom: 15px;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 8px;
        }

        .complyflow-cookie-policy h3 {
            font-size: 20px;
            margin-top: 30px;
            margin-bottom: 10px;
            color: #34495e;
        }

        .complyflow-cookie-policy p {
            margin-bottom: 15px;
        }

        .complyflow-cookie-policy ul,
        .complyflow-cookie-policy ol {
            margin-bottom: 15px;
            padding-left: 30px;
        }

        .complyflow-cookie-policy li {
            margin-bottom: 8px;
        }

        .complyflow-cookie-policy strong {
            color: #2c3e50;
            font-weight: 600;
        }

        .complyflow-cookie-policy a {
            color: #3498db;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.3s ease;
        }

        .complyflow-cookie-policy a:hover {
            border-bottom-color: #3498db;
        }

        .complyflow-cookie-policy code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: "Courier New", Courier, monospace;
            font-size: 14px;
            color: #e74c3c;
        }

        .complyflow-cookie-policy .cookie-meta {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .complyflow-cookie-policy .cookie-highlight {
            background: #e8f5e9;
            border-left: 4px solid #27ae60;
            padding: 15px;
            margin: 20px 0;
            border-radius: 3px;
        }

        .complyflow-cookie-policy .cookie-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .complyflow-cookie-policy .cookie-table thead {
            background: #3498db;
            color: #fff;
        }

        .complyflow-cookie-policy .cookie-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        .complyflow-cookie-policy .cookie-table td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }

        .complyflow-cookie-policy .cookie-table tbody tr:hover {
            background: #f8f9fa;
        }

        .complyflow-cookie-policy .cookie-table tbody tr:last-child td {
            border-bottom: none;
        }

        @media print {
            .complyflow-cookie-policy {
                max-width: 100%;
                padding: 0;
            }

            .complyflow-cookie-policy h1 {
                page-break-after: avoid;
            }

            .complyflow-cookie-policy h2,
            .complyflow-cookie-policy h3 {
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            .complyflow-cookie-policy .cookie-table {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .complyflow-cookie-policy {
                padding: 15px;
            }

            .complyflow-cookie-policy h1 {
                font-size: 28px;
            }

            .complyflow-cookie-policy h2 {
                font-size: 22px;
            }

            .complyflow-cookie-policy h3 {
                font-size: 18px;
            }

            .complyflow-cookie-policy .cookie-table {
                font-size: 14px;
            }

            .complyflow-cookie-policy .cookie-table th,
            .complyflow-cookie-policy .cookie-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="complyflow-cookie-policy">
        <h1>Cookie Policy</h1>

        <div class="cookie-meta">
            <p><strong>Company:</strong> {{COMPANY_NAME}}</p>
            <p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>
            <p><strong>Last Updated:</strong> {{LAST_UPDATED}}</p>
        </div>

        <div class="cookie-section">
            {{INTRODUCTION_SECTION}}
        </div>

        <div class="cookie-section">
            {{WHAT_ARE_COOKIES_SECTION}}
        </div>

        <div class="cookie-section">
            {{COOKIES_WE_USE_SECTION}}
        </div>

        <div class="cookie-section">
            {{COOKIE_CATEGORIES_SECTION}}
        </div>

        <div class="cookie-section">
            {{THIRD_PARTY_COOKIES_SECTION}}
        </div>

        <div class="cookie-section">
            {{MANAGING_COOKIES_SECTION}}
        </div>

        <div class="cookie-section">
            {{CONSENT_SECTION}}
        </div>

        <div class="cookie-section">
            {{UPDATES_SECTION}}
        </div>

        <div class="cookie-section">
            {{CONTACT_SECTION}}
        </div>
    </div>
</body>
</html>
