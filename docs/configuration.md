# Configuration

Vulnz Agent can be configured in two ways: through the WordPress admin interface or via constants in `wp-config.php`. Constants take precedence over database settings.

## Admin Interface

Navigate to **Vulnz Agent > Settings** in the WordPress admin:

| Setting | Description |
|---------|-------------|
| **Enable Connection** | Toggle the hourly sync on or off |
| **API URL** | Base URL for the Vulnz API (default: `https://api.vulnz.net`). Do not include the `/api` path. |
| **API Key** | Your Vulnz API key. Masked in the UI after saving. |

## wp-config.php Constants

For mu-plugin deployments or environments where database configuration is impractical, define constants in `wp-config.php`:

```php
define( 'VULNZ_AGENT_ENABLED', true );
define( 'VULNZ_AGENT_API_URL', 'https://api.vulnz.net' );
define( 'VULNZ_AGENT_API_KEY', 'your-api-key-here' );
```

When a constant is defined, the corresponding setting field in the admin UI is disabled and shows a notice indicating it is set via `wp-config.php`.

### Precedence

The plugin resolves each setting in this order:

1. `wp-config.php` constant (if defined)
2. Database option (WordPress Settings API)
3. Built-in default

## API Key

You can obtain an API key by logging in to your [Vulnz account](https://vulnz.net) and generating a new key in the dashboard.

The API key is:

- Stored in the database as option `wp_vulnz_api_key` (unless set via constant)
- Sanitised to alphanumeric characters only
- Masked in the settings UI to prevent leaking in the page source
