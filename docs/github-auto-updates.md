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
| Cache key | `vulnz_agent_updater_release` |
| Unauthenticated GitHub rate limit | 60 requests/hour per IP |

With the default TTL, a site makes ~2 GitHub API calls per day — well within rate limits.

The cache is automatically cleared when the plugin is updated, so the next check fetches fresh data.

## Disabling Auto-Updates

Use the `vulnz_agent_updater_enabled` filter to conditionally disable GitHub update checks. This is useful for:

- **Staging/development environments** where you don't want automatic updates
- **Version pinning** when you need to stay on a specific release
- **Managed hosting** where updates are handled through a deployment pipeline

### Disable entirely

```php
add_filter( 'vulnz_agent_updater_enabled', '__return_false' );
```

### Disable on non-production environments

```php
add_filter( 'vulnz_agent_updater_enabled', function ( $enabled ) {
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

## Configuration

The updater is wired up in `vulnz-agent.php` and configured via constants in `constants.php`:

| Constant | Purpose |
|----------|---------|
| `UPDATER_GITHUB_REPO` | GitHub repository in `owner/repo` format. |
| `UPDATER_CACHE_KEY` | Transient key for cached release data. |
| `UPDATER_CACHE_TTL` | How long release data is cached (default 12 hours). |

The release workflow (`.github/workflows/release.yml`) produces a ZIP asset named `vulnz-agent.zip`; the updater also accepts `vulnz-agent-{version}.zip` as a fallback.
