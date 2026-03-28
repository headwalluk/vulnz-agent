# API Integration

Vulnz Agent communicates with the Vulnz API to sync your site's plugin inventory for vulnerability monitoring.

## Sync Behaviour

- **Automatic:** Runs hourly via WP-Cron when the connection is enabled.
- **Manual:** Click "Sync Now" on the **Vulnz Agent > Summary** page.

Each sync sends your site's metadata and plugin list to the API. The API returns vulnerability data which is displayed on the Summary page.

### Sync Flow

1. Plugin collects site metadata and installed plugin list.
2. Checks if the website already exists in the API (GET `/api/websites/{domain}`).
3. Creates the website record if new (POST `/api/websites`), or updates it (PUT `/api/websites/{domain}`).

## API Endpoint

| | |
|---|---|
| **Default URL** | `https://api.vulnz.net` |
| **Authentication** | API key via `X-Api-Key` header |
| **Protocol** | HTTPS required (HTTP is rejected) |
| **Timeout** | 10 seconds per request |

The API URL is configurable for self-hosted Vulnz instances (see [Configuration](configuration.md)).

## Data Transmitted

The plugin sends **only** the following data:

| Field | Example |
|-------|---------|
| Site title | `My WordPress Site` |
| Domain | `example.com` |
| WordPress version | `6.9` |
| PHP version | `8.3.12` |
| Database server type | `mariadb` or `mysql` |
| Database server version | `11.4.2` |
| SSL status | `true` |
| Admin login URL | `https://example.com/wp-login.php` |
| Installed plugins | `[{"slug": "akismet", "version": "5.3"}]` |

**No personal data, user information, or site content is transmitted.**

## Data Returned

The API returns your website record including a list of plugins with vulnerability status. The Summary page displays:

- Plugin name and version
- Links to known vulnerability reports (if any)

Website data is cached locally as a WordPress transient (1-minute TTL) to reduce API calls on repeat page loads.

## Security

- **HTTPS enforced** — the plugin will not communicate over plaintext HTTP.
- **SSRF protection** — requests to `localhost`, `127.0.0.1`, and private/reserved IP ranges are blocked.
- **Nonce verification** — the "Sync Now" AJAX endpoint requires a valid nonce.
- **Capability check** — only users with `manage_options` can trigger a sync.
