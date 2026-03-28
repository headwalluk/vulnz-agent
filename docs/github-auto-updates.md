# GitHub Auto-Updates

Vulnz Agent ships with a built-in updater that checks GitHub Releases for new versions. When an update is available, it appears in the standard WordPress **Dashboard > Updates** screen alongside any other plugin updates.

## How It Works

1. WordPress periodically checks for plugin updates (roughly every 12 hours).
2. The updater queries the GitHub Releases API for the latest release of the plugin's repository.
3. If the release tag version is newer than the installed version, WordPress shows an update notice.
4. Clicking "Update Now" downloads the release ZIP directly from GitHub and installs it through the standard WordPress upgrader.

The "View details" link shows the plugin description and the release notes (changelog) from the GitHub release body.

## Caching

To avoid excessive GitHub API calls, the updater caches the latest release data as a WordPress transient:

| Setting | Default |
|---------|---------|
| Cache TTL | 12 hours (43,200 seconds) |
| Cache key | `headwall_ghu_` + md5 of repo slug |
| Unauthenticated GitHub rate limit | 60 requests/hour per IP |

With the default TTL, a site makes ~2 GitHub API calls per day — well within rate limits, even with multiple Headwall plugins installed on the same server.

The cache is automatically cleared when the plugin is updated, so the next check fetches fresh data.

## Disabling Auto-Updates

Use the `headwall_github_updater_enabled` filter to conditionally disable GitHub update checks. This is useful for:

- **Staging/development environments** where you don't want automatic updates
- **Version pinning** when you need to stay on a specific release
- **Managed hosting** where updates are handled through a deployment pipeline

### Disable for all Headwall plugins

```php
add_filter( 'headwall_github_updater_enabled', '__return_false' );
```

### Disable for a specific plugin

```php
add_filter( 'headwall_github_updater_enabled', function ( $enabled, $plugin_slug ) {
    if ( 'vulnz-agent' === $plugin_slug ) {
        return false;
    }
    return $enabled;
}, 10, 2 );
```

### Disable on non-production environments

```php
add_filter( 'headwall_github_updater_enabled', function ( $enabled ) {
    if ( defined( 'WP_ENVIRONMENT_TYPE' ) && 'production' !== WP_ENVIRONMENT_TYPE ) {
        return false;
    }
    return $enabled;
} );
```

### Filter reference

| Parameter | Type | Description |
|-----------|------|-------------|
| `$enabled` | `bool` | Whether updates are enabled. Default `true`. |
| `$plugin_slug` | `string` | Plugin directory name (e.g. `vulnz-agent`). |
| `$github_repo` | `string` | GitHub repository in `owner/repo` format. |

## Adding to Another Plugin

The updater class (`Headwall_GitHub_Plugin_Updater`) is portable and designed to be shared across plugins. A `class_exists` guard ensures the first plugin to load it wins — no conflicts.

To add GitHub auto-updates to any WordPress plugin hosted on GitHub:

1. Copy `includes/class-headwall-github-plugin-updater.php` into your plugin.

2. Add two lines to your main plugin file:

   ```php
   require_once __DIR__ . '/includes/class-headwall-github-plugin-updater.php';
   new Headwall_GitHub_Plugin_Updater( __FILE__, 'your-org/your-repo' );
   ```

3. Ensure your GitHub Actions release workflow produces a ZIP asset named `{plugin-slug}.zip` (the updater also accepts `{plugin-slug}-{version}.zip` as a fallback).

The optional third constructor parameter overrides the cache TTL (in seconds):

```php
// Check for updates every 6 hours instead of the default 12.
new Headwall_GitHub_Plugin_Updater( __FILE__, 'your-org/your-repo', 21600 );
```
