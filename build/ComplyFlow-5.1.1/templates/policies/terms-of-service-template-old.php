<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - {{COMPANY_NAME}}</title>
    <style>
        .complyflow-terms {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .complyflow-terms h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }

        .complyflow-terms h2 {
            font-size: 24px;
            margin-top: 40px;
            margin-bottom: 15px;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 8px;
        }

        .complyflow-terms h3 {
            font-size: 20px;
            margin-top: 30px;
            margin-bottom: 10px;
            color: #34495e;
        }

        .complyflow-terms h4 {
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #34495e;
        }

        .complyflow-terms p {
            margin-bottom: 15px;
        }

        .complyflow-terms ul,
        .complyflow-terms ol {
            margin-bottom: 15px;
            padding-left: 30px;
        }

        .complyflow-terms li {
            margin-bottom: 8px;
        }

        .complyflow-terms strong {
            color: #2c3e50;
            font-weight: 600;
        }

        .complyflow-terms a {
            color: #3498db;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.3s ease;
        }

        .complyflow-terms a:hover {
            border-bottom-color: #3498db;
        }

        .complyflow-terms .terms-meta {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .complyflow-terms .terms-highlight {
            background: #fff9e6;
            border-left: 4px solid #f39c12;
            padding: 15px;
            margin: 20px 0;
            border-radius: 3px;
        }

        .complyflow-terms .terms-important {
            background: #ffe6e6;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 20px 0;
            border-radius: 3px;
        }

        .complyflow-terms .terms-section {
            margin-bottom: 30px;
        }

        @media print {
            .complyflow-terms {
                max-width: 100%;
                padding: 0;
            }

            .complyflow-terms h1 {
                page-break-after: avoid;
            }

            .complyflow-terms h2,
            .complyflow-terms h3 {
                page-break-after: avoid;
                page-break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .complyflow-terms {
                padding: 15px;
            }

            .complyflow-terms h1 {
                font-size: 28px;
            }

            .complyflow-terms h2 {
                font-size: 22px;
            }

            .complyflow-terms h3 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="complyflow-terms">
        <h1>Terms of Service</h1>

        <div class="terms-meta">
            <p><strong>Company:</strong> {{COMPANY_NAME}}</p>
            <p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>
            <p><strong>Last Updated:</strong> {{LAST_UPDATED}}</p>
        </div>

        <div class="terms-section">
            {{INTRODUCTION_SECTION}}
        </div>

        <div class="terms-section">
            {{ACCEPTANCE_SECTION}}
        </div>

        <div class="terms-section">
            {{ELIGIBILITY_SECTION}}
        </div>

        <div class="terms-section">
            {{ACCOUNT_SECTION}}
        </div>

        <div class="terms-section">
            {{ECOMMERCE_SECTION}}
        </div>

        <div class="terms-section">
            {{INTELLECTUAL_PROPERTY_SECTION}}
        </div>

        <div class="terms-section">
            {{USER_CONTENT_SECTION}}
        </div>

        <div class="terms-section">
            {{PROHIBITED_CONDUCT_SECTION}}
        </div>

        <div class="terms-section">
            {{DISCLAIMERS_SECTION}}
        </div>

        <div class="terms-section">
            {{LIABILITY_SECTION}}
        </div>

        <div class="terms-section">
            {{INDEMNIFICATION_SECTION}}
        </div>

        <div class="terms-section">
            {{TERMINATION_SECTION}}
        </div>

        <div class="terms-section">
            {{GOVERNING_LAW_SECTION}}
        </div>

        <div class="terms-section">
            {{DISPUTE_RESOLUTION_SECTION}}
        </div>

        <div class="terms-section">
            {{CHANGES_SECTION}}
        </div>

        <div class="terms-section">
            {{CONTACT_SECTION}}
        </div>
    </div>
</body>
</html>
