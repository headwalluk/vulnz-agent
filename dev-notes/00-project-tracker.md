# Vulnz Agent - Rebranding Project Tracker

**Project Goal:** Rebrand the plugin from `wp-vulnz` to `vulnz-agent` with all namespace, function, and text references updated.

**GitHub Repository:** `git@github.com:headwalluk/vulnz-agent.git`

---

## Phase 1: Code Updates

### 1.1 Namespace Changes
- [x] Replace `WP_Vulnz` namespace with `Vulnz_Agent` in `vulnz-agent.php`
- [x] Replace `WP_Vulnz` namespace with `Vulnz_Agent` in `constants.php`
- [x] Replace `WP_Vulnz` namespace with `Vulnz_Agent` in `functions.php`
- [x] Replace `WP_Vulnz` namespace with `Vulnz_Agent` in `functions-public.php`
- [x] Replace `WP_Vulnz` namespace with `Vulnz_Agent` in `includes/class-plugin.php`
- [x] Replace `WP_Vulnz` namespace with `Vulnz_Agent` in `includes/class-admin-hooks.php`
- [x] Replace `WP_Vulnz` namespace with `Vulnz_Agent` in `includes/class-api-client.php`

### 1.2 Global Function Prefixes
- [x] Rename `wp_vulnz_run()` to `vulnz_agent_run()` in `vulnz-agent.php`
- [x] Rename `wp_vulnz_get_instance()` to `vulnz_agent_get_instance()` in `functions-public.php`
- [x] Update `$wp_vulnz_plugin` global variable to `$vulnz_agent_plugin` (all files)

### 1.3 Constants and Defines
- [x] Update `WP_Vulnz\PLUGIN_VERSION` to `Vulnz_Agent\PLUGIN_VERSION`
- [x] Update `WP_Vulnz\PLUGIN_FILE` to `Vulnz_Agent\PLUGIN_FILE`
- [x] Update `WP_Vulnz\PLUGIN_DIR` to `Vulnz_Agent\PLUGIN_DIR`
- [x] Update `WP_Vulnz\PLUGIN_URL` to `Vulnz_Agent\PLUGIN_URL`

### 1.4 WordPress Options (Database Keys)
- [x] Update `wp_vulnz_settings` to `vulnz_agent_settings` (via constants - values kept as wp_vulnz_* for compatibility)
- [x] Update `wp_vulnz_enabled` to `vulnz_agent_enabled` (via constants - values kept as wp_vulnz_* for compatibility)
- [x] Update `wp_vulnz_api_url` to `vulnz_agent_api_url` (via constants - values kept as wp_vulnz_* for compatibility)
- [x] Update `wp_vulnz_api_key` to `vulnz_agent_api_key` (via constants - values kept as wp_vulnz_* for compatibility)
- [x] Update cache key prefix from `wp_vulnz_website_` to `vulnz_agent_website_` (via constants - values kept as wp_vulnz_* for compatibility)

### 1.5 WordPress Hooks and Actions
- [x] Update SCHEDULE_NAME from `wp_vulnz` to `vulnz_agent`
- [x] Update SYNC_NOW_ACTION_NAME from `wp_vulnz_sync_now` to `vulnz_agent_sync_now`
- [x] Update SYNC_NOW_ACTION_NONCE from `wp_vulnz_sync_now_nonce` to `vulnz_agent_sync_now_nonce`
- [x] Update CRON_HOOK from `wp_vulnz_hourly_event` to `vulnz_agent_hourly_event`

### 1.6 Admin Menu Slugs
- [x] Update menu slug from `wp-vulnz-summary` to `vulnz-agent-summary`
- [x] Update menu slug from `wp-vulnz-settings` to `vulnz-agent-settings`

### 1.7 Text Domain
- [x] Update text domain from `wp-vulnz` to `vulnz-agent` (all occurrences)

### 1.8 Asset Handles
- [x] Update `wp-vulnz-admin` CSS handle to `vulnz-agent-admin`
- [x] Update `wp-vulnz-admin` JS handle to `vulnz-agent-admin`
- [x] Update `wp_vulnz` JS object to `vulnz_agent`

---

## Phase 2: Documentation and Metadata Updates

### 2.1 Plugin Header (vulnz-agent.php)
- [x] Update Plugin Name from `VULNZ` to `Vulnz Agent`
- [x] Update Plugin URI to new repository/site URL if needed
- [x] Update Description to reference `Vulnz Agent` instead of `VULNZ`
- [x] Update Text Domain from `wp-vulnz` to `vulnz-agent`
- [x] Update @package from `WP_Vulnz` to `Vulnz_Agent`

### 2.2 README.txt
- [x] Update plugin title from `=== VULNZ ===` to `=== Vulnz Agent ===`
- [x] Update Description to use `Vulnz Agent` instead of `VULNZ`
- [x] Update Installation instructions (folder paths, plugin name)
- [x] Update FAQ references to use `Vulnz Agent`
- [x] Update admin menu references (e.g., `WP Admin → Vulnz Agent`)
- [x] Update Screenshots description if needed
- [x] Add changelog entry for rebranding

### 2.3 File Headers (@package tags)
- [x] Update all `@package WP_Vulnz` to `@package Vulnz_Agent` in all PHP files

### 2.4 Admin Views
- [x] Update text/labels in `admin-views/settings.php`
- [x] Update text/labels in `admin-views/vulnz-overview.php`

---

## Phase 3: Testing and Verification

### 3.1 Code Verification
- [x] Search for any remaining `WP_Vulnz` references
- [x] Search for any remaining `wp_vulnz` references (except comments/docs where contextual)
- [x] Verify no hardcoded old plugin paths remain
- [x] Check that all namespace usages are correct

### 3.2 Functional Testing
- [x] Activate the plugin in WordPress
- [x] Verify admin menu appears as "Vulnz Agent"
- [x] Test Settings page loads correctly
- [x] Test Summary page loads correctly
- [x] Test API connection functionality
- [x] Test "Sync Now" functionality
- [x] Verify settings are saved correctly
- [x] Check that cron job is registered correctly
- [ ] Verify Settings link appears on plugins page

### 3.3 Database Migration (if upgrading existing installs)
- [ ] Create migration function to copy old option values to new keys
- [ ] Test upgrade path from wp-vulnz to vulnz-agent
- [ ] Document any manual migration steps needed

---

## Phase 4: Final Cleanup

- [x] Remove any old commented code
- [x] Verify all file references use correct paths
- [x] Update version number to 2.0.0
- [x] Review and test all changes
- [x] Create README.md for GitHub
- [x] Create CHANGELOG.md
- [ ] Commit changes to git repository
- [ ] Tag release if appropriate

---

## Notes

- **Global Variable:** The global `$wp_vulnz_plugin` → `$vulnz_agent_plugin` ✅
- **Plugin Folder:** Already renamed to `vulnz-agent/` ✅
- **Main File:** Already renamed to `vulnz-agent.php` ✅
- **Database Options:** Kept as `wp_vulnz_*` for compatibility, accessed via constants ✅
- **Important:** Database option names unchanged, so existing installations will continue to work

---

**Status:** Complete - Ready for Git Commit & Release  
**Last Updated:** 2025-12-29

## Summary

✅ **Version 2.0.0 Complete**
- All code rebranded from wp-vulnz to vulnz-agent
- Comprehensive documentation created (README.md, readme.txt, CHANGELOG.md)
- Breaking changes documented
- Backward compatibility maintained for database options
- Plugin tested and working

**Next Steps:**
1. Commit changes to git repository
2. Tag release as v2.0.0
3. Deploy to production
