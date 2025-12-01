# 🚀 ComplyFlow Quick Start Guide

## Installation & Setup (5 Minutes)

### 1. Install Dependencies

```powershell
# Navigate to plugin directory
cd d:\ShahiComplyFlow

# Install Composer dependencies (PHP)
composer install

# Install NPM dependencies (JavaScript)
npm install
```

### 2. Build Assets

```powershell
# Build CSS and JavaScript files
npm run build
```

This creates the `assets/dist/` folder with compiled files:
- `admin.css` & `admin.js`
- `frontend.css` & `frontend.js`
- `consent-banner.js`

### 3. Install Plugin in WordPress

**Option A: Copy to plugins folder**
```powershell
# Copy entire folder to WordPress plugins directory
xcopy /E /I d:\ShahiComplyFlow "C:\path\to\wordpress\wp-content\plugins\complyflow"
```

**Option B: Create symlink (for development)**
```powershell
# Run as Administrator
mklink /D "C:\path\to\wordpress\wp-content\plugins\complyflow" "d:\ShahiComplyFlow"
```

### 4. Activate Plugin

1. Go to WordPress Admin: `http://yoursite.local/wp-admin`
2. Navigate to **Plugins → Installed Plugins**
3. Find "ComplyFlow" and click **Activate**

### 5. Verify Installation

After activation, check:
- ✅ New menu item "ComplyFlow" appears in admin sidebar
- ✅ No errors in WordPress admin
- ✅ Dashboard loads at **ComplyFlow → Dashboard**

## Testing the Plugin

### Test Dashboard
1. Go to **ComplyFlow → Dashboard**
2. You should see:
   - Compliance score widget (0%)
   - Pending DSR requests (0)
   - Accessibility status
   - Consent statistics
   - Quick actions buttons

### Test Settings
1. Go to **ComplyFlow → Settings**
2. Toggle modules on/off
3. Click **Save Settings**
4. Verify success notice appears

### Test DSR Portal (Frontend)
1. Create a new page in WordPress
2. Add shortcode: `[complyflow_dsr_portal]`
3. Publish and view the page
4. You should see the DSR request form

## Development Mode

### Watch for Changes (Auto-rebuild)

```powershell
# Starts Vite dev server with hot reload
npm run dev
```

Keep this running while developing. It will automatically rebuild assets when you save changes.

### Code Quality Checks

```powershell
# Check PHP coding standards
composer phpcs

# Fix PHP coding standards
composer phpcbf

# Run static analysis
composer phpstan

# Check JavaScript
npm run lint
```

## Common Commands Reference

| Command | Description |
|---------|-------------|
| `npm run build` | Build assets for production |
| `npm run dev` | Start development server |
| `npm run watch` | Watch files and rebuild |
| `composer phpcs` | Check PHP code standards |
| `composer phpcbf` | Fix PHP code standards |
| `composer phpstan` | Run static analysis |

## File Structure Overview

```
complyflow/
├── 📁 assets/src/        → Source files (edit these)
│   ├── css/              → Tailwind CSS files
│   └── js/               → JavaScript files
├── 📁 assets/dist/       → Built files (auto-generated)
├── 📁 includes/
│   ├── Core/             → Plugin core classes
│   ├── Modules/          → Feature modules
│   └── Admin/            → Admin UI files
├── 📁 templates/         → Frontend templates
├── 📁 languages/         → Translation files
├── 📄 complyflow.php    → Main plugin file
└── 📄 composer.json     → PHP dependencies
```

## Troubleshooting

### "Plugin could not be activated"
- **Check PHP version**: Must be 8.0 or higher
- **Check WP version**: Must be 6.4 or higher
- **Check error log**: `wp-content/debug.log`

### "Assets not loading"
- **Run**: `npm run build`
- **Check**: `assets/dist/` folder exists
- **Clear cache**: Browser and WordPress cache

### "Class not found" errors
- **Run**: `composer install`
- **Check**: `vendor/` folder exists
- **Verify**: Autoloader is working

### Database tables not created
- **Deactivate** and **reactivate** plugin
- **Check**: Database user has CREATE TABLE permissions
- **Verify**: No SQL errors in debug log

## Next Steps

Now that Phase 0 is complete, you can:

1. **Start Phase 1** - Enhance core architecture
2. **Customize admin UI** - Modify views in `includes/Admin/views/`
3. **Add module functionality** - Implement features in `includes/Modules/`
4. **Style customization** - Edit CSS in `assets/src/css/`

## Need Help?

- 📖 See `DEVELOPMENT_PLAN.md` for detailed roadmap
- 📋 See `PHASE_0_COMPLETE.md` for what's been built
- 🔍 Check WordPress Codex for API reference

## Quick Test Checklist

- [ ] Composer dependencies installed
- [ ] NPM dependencies installed
- [ ] Assets built (`assets/dist/` exists)
- [ ] Plugin copied to WordPress
- [ ] Plugin activated without errors
- [ ] Admin dashboard loads
- [ ] Settings page works
- [ ] DSR portal shortcode works
- [ ] No console errors in browser
- [ ] No PHP errors in debug log

---

**You're ready to build! 🎉**

Start with Phase 1 (Core Architecture) from the development plan.
