# Installation Process

## Step 1: Download the Plugin

1. Download the ComplyFlow plugin ZIP file from the official source.
2. Extract the ZIP file to a local directory.

## Step 2: Install Dependencies

Run the following commands in the plugin directory:

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

## Step 3: Build Assets

Compile the plugin's assets:

```bash
npm run build
```

This will generate the necessary CSS and JavaScript files in the `assets/dist/` directory.

## Step 4: Upload to WordPress

1. Copy the plugin folder to the `wp-content/plugins/` directory of your WordPress installation.
2. Alternatively, upload the ZIP file via the WordPress admin panel under **Plugins → Add New → Upload Plugin**.

## Step 5: Activate the Plugin

1. Go to **Plugins → Installed Plugins** in the WordPress admin panel.
2. Locate "ComplyFlow" and click **Activate**.

## Step 6: Verify Installation

- Ensure the "ComplyFlow" menu appears in the WordPress admin sidebar.
- Check for any errors in the WordPress debug log.