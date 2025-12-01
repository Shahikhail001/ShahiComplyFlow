
# Data Subject Rights (DSR) Portal

## What is the DSR Portal?
The DSR Portal is a user-facing and admin tool for automating and managing data subject rights requests under GDPR, CCPA, and similar laws. It covers access, rectification, erasure, portability, restriction, objection, and automated decision-making rights.

## Key Features

### 1. Public Portal
- **Shortcode Integration**: Add the portal to any page with `[complyflow_dsr_portal]` or `[complyflow_dsr_form]`.
- **User-Friendly Form**: Users can request data access, correction, deletion, export, and more.
- **Email Verification**: Double opt-in with expiring tokens ensures only legitimate requests are processed.
- **Customizable Fields**: Add or remove fields to match your privacy policy.

### 2. Request Types Supported
1. **Access**: Request a copy of all personal data.
2. **Rectification**: Request corrections to inaccurate data.
3. **Erasure**: Request deletion of personal data ("right to be forgotten").
4. **Portability**: Request export of data in machine-readable format.
5. **Restriction**: Request restriction of processing.
6. **Objection**: Object to certain types of processing.
7. **Automated Decision**: Request review of automated decisions.

### 3. Request Management (Admin)
- **Admin Dashboard**: View, filter, and manage all DSR requests in one place.
- **Status Pipeline**: Track requests through Pending, Verified, In Progress, Completed, and Rejected stages.
- **Bulk Actions**: Approve, reject, or export multiple requests at once.
- **SLA Tracking**: Monitor response deadlines (30/45 days) and overdue requests.
- **Notes & Audit Trail**: Add notes and view a full history of actions for each request.

### 4. Automated Workflows
- **Data Discovery**: Automatically gathers data from users, WooCommerce, forms, comments, and more.
- **Email Notifications**: Notifies users and admins at each stage of the request.
- **Export Formats**: JSON, CSV, XML for easy sharing and compliance.
- **WooCommerce Integration**: Includes order, review, and address data for e-commerce sites.

### 5. Compliance Logging
- **Full Audit Trail**: Every action is logged for compliance and review.
- **Export Logs**: Download logs for regulatory requests or internal audits.

## How to Use
1. **Add the Portal**: Place the shortcode on a public-facing page (e.g., Privacy Policy or dedicated DSR page).
2. **Configure Settings**: Go to ComplyFlow → Settings → DSR Portal to set SLA days, email templates, and anonymization rules.
3. **Monitor Requests**: Use the admin dashboard to process and track requests.
4. **Export Data**: Use the export feature to provide data to users or for compliance.

## Troubleshooting & Tips
- If requests are not received, check email settings and spam filters.
- For missing data, ensure all integrations (WooCommerce, forms) are enabled.
- Use the audit trail to resolve disputes or demonstrate compliance.
- Regularly review and update email templates for clarity and legal accuracy.

## Advanced Usage
- **REST API**: Automate DSR workflows with `/complyflow/v1/dsr/request` endpoints.
- **WP-CLI**: Use `wp complyflow dsr list` and related commands for batch processing.
- **Custom Fields**: Developers can extend the portal with additional fields or workflows.

## Related Documentation
- See Analytics and Reporting for DSR statistics.
- See Admin Settings for configuring DSR workflows and notifications.