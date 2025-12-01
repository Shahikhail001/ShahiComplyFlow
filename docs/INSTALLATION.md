# ComplyFlow Installation Guide

This guide walks through installing, activating, and configuring ComplyFlow in several environments. It also covers required dependencies, asset builds, and optional developer tooling.

---

## 1. Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| WordPress | 6.4 | 6.7 |
| PHP       | 8.0 | 8.2 or 8.3 |
| MySQL     | 5.7 | 8.0 |
| Web Server | Apache or Nginx | Apache + mod_rewrite |

Additional tooling for developers:

- Node.js 18+ and npm 9+ (for building assets)
- Composer 2.5+ (for dev dependencies and QA tooling)
- WP-CLI (optional for CLI commands)

---

## 2. Downloading the Plugin

1. Clone the repository or download the compressed release package.
2. If you received a CodeCanyon ZIP, extract it locally and locate the `complyflow/` folder.
3. Verify that the folder contains `complyflow.php`, `assets/`, `includes/`, and the documentation files.

---

## 3. Installation Methods

### 3.1 WordPress Admin Upload

1. Compress the `complyflow/` folder into `complyflow.zip`.
2. Log in to the WordPress admin and navigate to `Plugins → Add New`.
3. Click **Upload Plugin**, choose `complyflow.zip`, then click **Install Now**.
4. After the upload completes, click **Activate Plugin**.

### 3.2 FTP / File Manager

1. Connect to the server using FTP, SFTP, or a control panel file manager.
2. Upload the `complyflow/` directory to `wp-content/plugins/`.
3. Log in to WordPress admin and activate ComplyFlow from `Plugins`.

### 3.3 WP-CLI

```bash
wp plugin install ./complyflow.zip --activate
```

---

## 4. Post-Activation Checklist

After activation, complete the following steps:

- Navigate to `ComplyFlow → Settings` and enter required organization details.
- Configure accessibility scan defaults (target post types, schedule).
- Customize the consent banner text, branding, and categories.
- Review legal document templates and publish the relevant pages.
- Run the onboarding wizard if prompted to set up initial data.

---

## 5. Building Frontend Assets (Developers)

Production-ready builds are already included in release packages. Only run these steps when developing or modifying assets.

```bash
npm install
npm run build
```

Outputs appear under `assets/dist/` and include source maps for debugging.

---

## 6. Installing Composer Dependencies (Developers)

Composer dev dependencies power QA tooling such as PHPCS and PHPStan.

```bash
composer install
```

Available scripts:

- `composer run phpcs`
- `composer run phpstan`
- `composer run test`

---

## 7. CLI Commands (Optional)

Once activated, ComplyFlow registers WP-CLI commands:

- `wp complyflow scan run` — trigger an accessibility scan.
- `wp complyflow consent sync` — synchronize consent logs.
- `wp complyflow dsr list` — view data subject requests.

Run `wp help complyflow` for the full command list.

---

## 8. Troubleshooting

- **White screen or fatal error?** Ensure the server meets the minimum PHP version.
- **Styles or scripts missing?** Clear site caches and confirm files exist in `assets/dist/`.
- **Installer aborts?** Check file permissions (recommended `755` directories, `644` files).
- **CLI errors?** Ensure WP-CLI runs under the same PHP version configured for WordPress.

For additional support, email `support@complyflow.com` or open a ticket via the ComplyFlow support portal.
