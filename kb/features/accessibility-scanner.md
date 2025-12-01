
# Accessibility Scanner

## What is the Accessibility Scanner?
The Accessibility Scanner is a powerful tool that automatically checks your website for compliance with WCAG 2.2 standards. It helps you identify, understand, and fix accessibility issues, making your site usable for everyone and reducing legal risk.

## Key Features

### 1. Automated Scanning
- **On-Demand Scans**: Instantly scan any page, post, or custom URL from the admin dashboard.
- **Scheduled Scans**: Set up daily, weekly, or custom-interval scans to monitor your site over time.
- **Supports Page Builders**: Works with Elementor, WPBakery, Divi, and more.
- **Severity Classification**: Issues are categorized as Critical, Serious, Moderate, or Minor for easy prioritization.

### 2. 11 Specialized Checkers
The scanner uses 11 dedicated checkers to cover all major WCAG 2.2 requirements:
1. **ImageChecker**: Checks alt text, image maps, SVG metadata.
2. **HeadingChecker**: Detects missing, multiple, skipped, or empty headings.
3. **FormChecker**: Validates labels, required fields, fieldsets, and buttons.
4. **LinkChecker**: Finds empty, ambiguous, or broken links.
5. **AriaChecker**: Flags invalid ARIA roles and attributes.
6. **KeyboardChecker**: Ensures keyboard navigation and tabindex order.
7. **SemanticChecker**: Checks for language attributes and title elements.
8. **MultimediaChecker**: Verifies captions and transcripts for audio/video.
9. **TableChecker**: Checks table headers, captions, and structure.
10. **ColorContrastChecker**: (Planned) Will check color contrast for text and UI elements.
11. **BaseChecker**: Abstract base for all checkers, ensuring consistency.

### 3. Detailed Reports
- **Issue Breakdown**: Each issue includes a description, affected element, and recommended fix.
- **WCAG Mapping**: Every issue is linked to the relevant WCAG criterion for reference.
- **Remediation Guidance**: Step-by-step instructions to resolve each problem.
- **CSV Export**: Download scan results for offline review or sharing with your team.

### 4. Notifications & Integrations
- **Email Alerts**: Automatically sends scan results to specified recipients.
- **Dashboard Updates**: Summaries and trends are displayed in the Compliance Dashboard.
- **REST API**: Trigger scans and retrieve results programmatically.
- **WP-CLI**: Run scans and export results from the command line.

## How to Use
1. **Run a Scan**: Go to ComplyFlow → Accessibility, select a page or enter a URL, and click “Scan Now.”
2. **Schedule Scans**: In Settings → Accessibility, set your preferred scan frequency and notification emails.
3. **Review Results**: View issues in the admin UI, filter by severity, and export as needed.
4. **Fix Issues**: Follow the remediation guidance for each issue. Re-scan to verify fixes.

## Settings & Customization
- **WCAG Level**: Choose A, AA, or AAA compliance.
- **Scan Frequency**: Daily, weekly, fortnightly, or monthly.
- **Notification Recipients**: Add emails to receive scan reports.
- **Auto-Fix Options**: (Planned) Enable automatic remediation for certain issues.

## Troubleshooting & Tips
- If scans fail, check your site’s public accessibility and ensure no firewall is blocking requests.
- For missing issues, verify that all modules and checkers are enabled in settings.
- Use the CSV export to share results with developers or accessibility consultants.
- Schedule regular scans to maintain compliance as your site evolves.

## Advanced Usage
- **REST API**: Use `/complyflow/v1/scan` endpoints to automate scans from external tools.
- **WP-CLI**: Run `wp complyflow scan run --url=<url>` for headless or batch scanning.
- **Integration**: Results can be linked to ticketing systems or QA workflows.

## Related Documentation
- See Compliance Dashboard for how scan results are visualized.
- See Admin Settings for configuring scan schedules and notifications.