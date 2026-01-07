# WordPress Plugin Development Guidelines

**Version:** 1.1.0  
**Purpose:** Base coding standards and patterns for WordPress plugin development  
**Portability:** Copy this file to any WordPress plugin project  
**Last Updated:** 7 January 2026

---

## Current Project: Venue Admissions for WooCommerce

**Active Feature:** Milestone 13 - Attendance Export & Reporting  
**Requirements:** See `dev-notes/00-project-tracker.md` Milestone 13 for detailed task list

---

## Core Principles

1. **Follow WordPress Coding Standards** - Verify with phpcs/phpcbf
2. **Use Modern PHP** - PHP 8.0+ features (type hints, union types, nullable types)
3. **Write Self-Documenting Code** - Clear naming reduces need for comments
4. **Security First** - Always sanitize input, escape output, verify nonces
5. **Keep It Simple** - Favor clarity over cleverness

---

## File Structure & Naming

### Directory Organization

```
plugin-name/
├── plugin-name.php           # Main plugin file
├── constants.php             # Plugin constants
├── functions.php             # Public helper functions
├── functions-private.php     # Private/internal functions
├── phpcs.xml                 # Code standards configuration
├── includes/                 # Core classes
│   ├── class-plugin.php
│   ├── class-settings.php
│   ├── class-admin-hooks.php
│   └── class-public-hooks.php
├── admin-views/              # Admin template files
├── templates/                # Public template files
├── assets/
│   ├── admin/               # Admin CSS/JS
│   └── public/              # Public CSS/JS
└── languages/               # Translation files
```

### File Naming Conventions

- **Class files:** `class-{classname}.php` (lowercase with hyphens)
  - Example: `class-event-controller.php`
- **Template files:** `{template-name}.php` (lowercase with hyphens)
  - Example: `settings-page.php`
- **Asset files:** `{descriptive-name}.{ext}` (lowercase with hyphens)
  - Example: `admin-styles.css`

---

## PHP Standards

### Namespaces & Classes

Use namespaces for all classes to avoid conflicts:

```php
namespace Plugin_Name;

class Settings {
    // No prefix needed - namespace handles it
}
```

**File header template:**
```php
<?php
/**
 * Class description.
 *
 * @package Plugin_Name
 */

declare(strict_types=1);

namespace Plugin_Name;

// Class definition
```

### Class Organization

**Property Order:**
1. Public properties
2. Protected properties
3. Private properties

**Method Order:**
1. `__construct()`
2. Public methods
3. Protected methods
4. Private methods

**Example:**
```php
<?php
namespace Plugin_Name;

defined( 'ABSPATH' ) || die();

/**
 * Example class demonstrating organization.
 *
 * @since 1.0.0
 */
class Example {

    /**
     * Public property.
     *
     * @var string
     */
    public string $name;

    /**
     * Private property.
     *
     * @var int
     */
    private int $id;

    /**
     * Constructor.
     *
     * @since 1.0.0
     *
     * @param string $name Item name.
     * @param int    $id   Item ID.
     */
    public function __construct( string $name, int $id ) {
        $this->name = $name;
        $this->id   = $id;
    }

    /**
     * Public method example.
     *
     * @since 1.0.0
     *
     * @return string Formatted name.
     */
    public function get_formatted_name(): string {
        return $this->format_name( $this->name );
    }

    /**
     * Private method example.
     *
     * @since 1.0.0
     *
     * @param string $name Name to format.
     * @return string Formatted name.
     */
    private function format_name( string $name ): string {
        return ucfirst( $name );
    }
}
```

### Type Hints & Return Types

Always use type declarations for modern PHP:

```php
public function get_items( int $user_id ): array {
    // Implementation
}

public function validate( string $input ): bool|\WP_Error {
    // Can return bool or WP_Error
}
```

### Function Documentation

Every function needs a docblock:

```php
/**
 * Brief description of what the function does.
 *
 * Longer description if needed. Explain the "why" not the "what"
 * when the code itself isn't clear.
 *
 * @since 1.0.0
 *
 * @param string $param1 Description of parameter.
 * @param int    $param2 Description of parameter.
 *
 * @throws \Exception If something goes wrong.
 *
 * @return array<string, mixed> Description of return value.
 */
public function example_function( string $param1, int $param2 ): array {
    // Implementation
}
```

### Constants & Magic Values

**Rule:** All magic strings and magic numbers must be defined as constants in `constants.php`.

**Exception:** Translatable text strings use `__()` or `_e()` directly.

```php
// constants.php
namespace Plugin_Name;

// Default values - prefix with DEF_
const DEF_CHECKOUT_ERROR_MESSAGE = 'Item no longer available.';
const DEF_VARIATION_HANDLING = 'css';
const DEF_BYPASS_HIDE_CART = false;
const DEF_DONATION_PERCENTAGE = 10;

// wp_options keys - prefix with OPT_
const OPT_TIME_FORMAT = 'plugin_name_time_format';
const OPT_AUTOCOMPLETE_VIRTUAL = 'plugin_name_autocomplete_virtual_orders';
const OPT_EMAIL_DATETIME_NOTE = 'plugin_name_email_datetime_note';

// CSS selectors and other magic strings
const WC_CART_ELEMENTS_SELECTOR = 'div.product form.cart div.quantity';
const WC_VARIATIONS_SELECTOR = 'div.product form.cart .variations';

// Magic numbers
const MAX_SLOTS_PER_DAY = 20;
const MIN_CAPACITY = 1;
```

**Why:** Prevents inconsistency, centralizes configuration, makes updates easier.

**Usage:**
```php
// ✅ Good - reference constant
$handling = get_option( OPT_VARIATION_HANDLING, DEF_VARIATION_HANDLING );
if ( 'css' === $handling ) {
    wp_add_inline_style( 'plugin-public', WC_VARIATIONS_SELECTOR . ' { display:none; }' );
}

// ❌ Bad - magic string/number
$handling = get_option( 'plugin_variation_handling', 'css' );
if ( 'css' === $handling ) {
    wp_add_inline_style( 'plugin-public', 'div.product .variations { display:none; }' );
}
```

**Boolean Options - Handle Multiple Formats:**

WordPress Settings API with `rest_sanitize_boolean` stores checkboxes as `1`/`0`, but older code or plugins may use `'yes'`/`'no'`, `'on'`/`'off'`, or `true`/`false`. Use `filter_var()` with `FILTER_VALIDATE_BOOLEAN` to handle all formats reliably:

```php
// ✅ Good - handles all boolean formats (1, '1', 'yes', 'on', true, etc.)
$autocomplete = (bool) filter_var( get_option( OPT_AUTOCOMPLETE_VIRTUAL, false ), FILTER_VALIDATE_BOOLEAN );
if ( $autocomplete ) {
    // Auto-complete order
}

// ✅ Also good - for post meta
$enabled = (bool) filter_var( get_post_meta( $product_id, META_ENABLED, true ), FILTER_VALIDATE_BOOLEAN );
if ( $enabled ) {
    // Product has feature enabled
}

// ❌ Bad - only checks for specific string value
$autocomplete = get_option( OPT_AUTOCOMPLETE_VIRTUAL, 'no' );
if ( 'yes' === $autocomplete ) {
    // This fails when value is 1, true, '1', 'on', etc.
}

// ❌ Bad - verbose manual checking
$autocomplete = get_option( OPT_AUTOCOMPLETE_VIRTUAL, false );
if ( true === $autocomplete || '1' === $autocomplete || 'yes' === $autocomplete ) {
    // Works but unnecessarily verbose - use filter_var() instead
}
```

**Why:** 
- `filter_var()` with `FILTER_VALIDATE_BOOLEAN` handles: `'1'`, `'true'`, `'on'`, `'yes'` (case-insensitive) as `true`
- Handles: `'0'`, `'false'`, `'off'`, `'no'`, `''`, `null` (case-insensitive) as `false`
- Casting to `(bool)` ensures return type is always boolean
- Works identically for `get_option()` and `get_post_meta()`
- Future-proof against changes in how WordPress stores boolean values

---

## WordPress Integration Patterns

### Hook Registration

**Centralized approach:** Register all hooks in main Plugin class, delegate logic to separate classes:

```php
class Plugin {
    private Admin_Hooks $admin_hooks;
    
    public function run(): void {
        // Admin hooks
        add_action( 'admin_menu', array( $this, 'add_menu_items' ) );
        add_action( 'admin_enqueue_scripts', array( $this->admin_hooks, 'enqueue_assets' ) );
        
        // Public hooks
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
    }
}
```

**Benefits:**
- One place to see all hooks
- Easy to audit integrations
- Clear separation of concerns

### AJAX Endpoints

**Standard pattern with security:**

```php
/**
 * Handle AJAX request.
 *
 * @since 1.0.0
 */
public function ajax_handle_request(): void {
    // Verify nonce
    check_ajax_referer( 'plugin_name_nonce', 'nonce' );
    
    // Check capabilities
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'plugin-name' ) ) );
    }
    
    // Validate input
    $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    
    if ( ! $id ) {
        wp_send_json_error( array( 'message' => __( 'Invalid ID.', 'plugin-name' ) ) );
    }
    
    // Process request
    $result = $this->process_data( $id );
    
    // Return response
    wp_send_json_success( array( 'data' => $result ) );
}
```

**Register AJAX hooks:**

```php
add_action( 'wp_ajax_plugin_action', array( $this, 'ajax_handle_request' ) );
add_action( 'wp_ajax_nopriv_plugin_action', array( $this, 'ajax_handle_request' ) ); // If needed for logged-out users
```

### Rate Limiting for Public Endpoints

```php
public function ajax_public_endpoint(): void {
    $ip_address   = $_SERVER['REMOTE_ADDR'] ?? '';
    $throttle_key = 'plugin_throttle_' . md5( $ip_address );
    $count        = get_transient( $throttle_key );
    
    if ( false !== $count && $count > 20 ) {
        wp_send_json_error( array( 'message' => __( 'Rate limit exceeded.', 'plugin-name' ) ) );
    }
    
    set_transient( $throttle_key, ( $count ? $count + 1 : 1 ), MINUTE_IN_SECONDS );
    
    // Process request...
}
```

### Caching Patterns

Use WordPress Transients API for caching to improve performance:

```php
/**
 * Get user's websites with caching.
 *
 * @param int $user_id User ID.
 * @return array Website list.
 */
function get_user_websites( int $user_id ): array {
    $cache_key = 'plugin_websites_' . $user_id;
    $websites  = get_transient( $cache_key );
    
    if ( false !== $websites ) {
        return $websites;
    }
    
    // Fetch from API or database
    $websites = fetch_websites_from_api( $user_id );
    
    // Cache for 5 minutes
    set_transient( $cache_key, $websites, 5 * MINUTE_IN_SECONDS );
    
    return $websites;
}

/**
 * Invalidate cache when data changes.
 *
 * @param int $user_id User ID.
 */
function invalidate_websites_cache( int $user_id ): void {
    delete_transient( 'plugin_websites_' . $user_id );
}
```

**Best Practices:**
- Use descriptive cache keys with unique identifiers (user ID, post ID, etc.)
- Set appropriate expiration times (use WordPress time constants)
- Invalidate cache when underlying data changes
- Consider using object caching for high-traffic sites (but don't rely on it)
- Avoid caching user-specific data without user ID in key

**WordPress Time Constants:**
```php
MINUTE_IN_SECONDS  // 60
HOUR_IN_SECONDS    // 3600
DAY_IN_SECONDS     // 86400
WEEK_IN_SECONDS    // 604800
MONTH_IN_SECONDS   // 2592000
YEAR_IN_SECONDS    // 31536000
```

---

## Security Best Practices

### Always Follow These Rules

1. **Sanitize Input**
   ```php
   $id    = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
   $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
   $text  = isset( $_POST['text'] ) ? sanitize_text_field( wp_unslash( $_POST['text'] ) ) : '';
   ```

2. **Escape Output**
   ```php
   echo esc_html( $user_text );
   echo esc_url( $link );
   echo esc_attr( $attribute_value );
   ```

3. **Verify Nonces**
   ```php
   // In forms
   wp_nonce_field( 'plugin_action', 'plugin_nonce' );
   
   // In handlers
   if ( ! wp_verify_nonce( $_POST['plugin_nonce'], 'plugin_action' ) ) {
       wp_die( 'Security check failed' );
   }
   
   // After verifying nonce, disable phpcs warning for $_POST access
   // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified above
   $data = isset( $_POST['data'] ) ? sanitize_text_field( wp_unslash( $_POST['data'] ) ) : '';
   // phpcs:enable
   ```

4. **Check Capabilities**
   ```php
   if ( ! current_user_can( 'manage_options' ) ) {
       wp_die( 'Insufficient permissions' );
   }
   ```

5. **Prepare Database Queries**
   ```php
   $wpdb->get_results(
       $wpdb->prepare(
           "SELECT * FROM {$wpdb->prefix}custom_table WHERE id = %d",
           $id
       )
   );
   ```

### Error Handling with WP_Error

Use WordPress's `WP_Error` class for robust error handling:

```php
/**
 * Create subscription.
 *
 * @param array $data Subscription data.
 * @return int|\WP_Error Subscription ID or error object.
 */
function create_subscription( array $data ) {
    // Validate input
    if ( empty( $data['user_id'] ) ) {
        return new \WP_Error(
            'missing_user_id',
            __( 'User ID is required.', 'plugin-name' )
        );
    }
    
    // Create post
    $post_id = wp_insert_post( $post_data );
    
    if ( is_wp_error( $post_id ) ) {
        return $post_id;
    }
    
    return $post_id;
}

// Usage
$result = create_subscription( $data );
if ( is_wp_error( $result ) ) {
    error_log( 'Subscription creation failed: ' . $result->get_error_message() );
    return false;
}

// Success - use $result as post ID
```

**Benefits:**
- Consistent error handling across WordPress
- Support for multiple error codes and messages
- Easy error checking with `is_wp_error()`
- Can pass error data: `new \WP_Error( 'code', 'message', array( 'data' => $value ) )`

---

## Database Patterns

### Custom Tables

**When to use:**
- Complex data structures
- High-performance requirements
- Data not fitting WordPress core tables

**Migration pattern:**

```php
class Database {
    private const DB_VERSION = 1;
    
    public function create_tables(): bool {
        global $wpdb;
        
        $table_name      = $wpdb->prefix . 'plugin_data';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            data_value varchar(255) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
        
        update_option( 'plugin_name_db_version', self::DB_VERSION );
        
        return true;
    }
}
```

**Version tracking:**
```php
function check_database_version(): void {
    $current_version = get_option( 'plugin_name_db_version', 0 );
    
    if ( $current_version < self::DB_VERSION ) {
        $this->run_migrations( $current_version );
    }
}
```

---

## Template System

### Template Loading with Overrides

Allow theme customization:

```php
/**
 * Load a template file with theme override support.
 *
 * @param string $template_name Template filename.
 * @param array  $args          Variables to pass to template.
 */
function load_template( string $template_name, array $args = array() ): void {
    // Extract args to variables
    extract( $args );
    
    // Check theme override
    $theme_template = locate_template( "plugin-name/{$template_name}" );
    
    if ( $theme_template ) {
        include $theme_template;
    } else {
        include PLUGIN_DIR . "templates/{$template_name}";
    }
}
```

### Template Files

```php
<?php
/**
 * Template description.
 *
 * @package Plugin_Name
 */

defined( 'ABSPATH' ) || die();

// Template variables available:
// $variable1, $variable2, etc.
?>

<div class="plugin-wrapper">
    <h2><?php echo esc_html( $title ); ?></h2>
    <!-- Template content -->
</div>
```

---

## JavaScript Patterns

### Class-Based Selectors

Never use IDs for JavaScript selectors (except for unique admin elements):

```javascript
// Good - reusable, multiple instances safe
document.querySelector('.plugin-calendar');
document.querySelectorAll('.plugin-item');

// Avoid - unless truly unique admin element
document.getElementById('unique-admin-element');
```

### Container-Scoped Initialization

```javascript
class PluginWidget {
    constructor(container) {
        this.container = container;
        this.init();
    }
    
    init() {
        // Use delegated events for dynamic content
        this.container.addEventListener('click', (e) => {
            if (e.target.matches('.plugin-button')) {
                this.handleClick(e);
            }
        });
    }
}

// Initialize all instances
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.plugin-widget').forEach(container => {
        new PluginWidget(container);
    });
});
```

### AJAX in JavaScript

```javascript
async function sendRequest(action, data) {
    const formData = new FormData();
    formData.append('action', action);
    formData.append('nonce', pluginData.nonce);
    
    for (const [key, value] of Object.entries(data)) {
        formData.append(key, value);
    }
    
    try {
        const response = await fetch(pluginData.ajaxUrl, {
            method: 'POST',
            body: formData
        });
        
        return await response.json();
    } catch (error) {
        console.error('Request failed:', error);
        throw error;
    }
}
```

### No Inline JavaScript

**Rule:** Never include inline JavaScript in template files.

```php
<!-- ❌ Bad - inline JavaScript -->
<div id="my-element"></div>
<script>
jQuery(document).ready(function($) {
    $('#my-element').on('click', function() { /* handler */ });
});
</script>

<!-- ✅ Good - all JS in separate files -->
<div class="my-widget"></div>
<!-- JavaScript loaded via wp_enqueue_script() from assets/ directory -->
```

**Why:**
- Cleaner templates, better caching
- Easier debugging and testing
- Follows WordPress best practices
- Separates concerns (HTML vs JS)

### Button Elements Must Include `button` Class

**Rule:** All `<button>` elements in templates and JavaScript must include the `button` CSS class.

```php
<!-- ❌ Bad - missing button class -->
<button type="button" class="plugin-action">Click Me</button>

<!-- ✅ Good - includes button class -->
<button type="button" class="plugin-action button">Click Me</button>
```

```javascript
// ❌ Bad - missing button class
html += `<button type="button" class="plugin-slot" data-id="${id}">${label}</button>`;

// ✅ Good - includes button class
html += `<button type="button" class="plugin-slot button" data-id="${id}">${label}</button>`;
```

**Why:**
- Ensures consistent WordPress button styling across the plugin
- Leverages WordPress core button styles automatically
- Maintains visual consistency with WordPress admin and themes

---

## Code Quality

### PHPCS Configuration

Create `phpcs.xml` in plugin root:

```xml
<?xml version="1.0"?>
<ruleset name="WordPress Plugin Standards">
    <description>WordPress Coding Standards</description>
    
    <file>.</file>
    
    <!-- Exclude patterns -->
    <exclude-pattern>*/vendor/*</exclude-pattern>
    <exclude-pattern>*/node_modules/*</exclude-pattern>
    <exclude-pattern>*/assets/*</exclude-pattern>
    
    <!-- Arguments -->
    <arg value="sp"/>
    <arg name="basepath" value="."/>
    <arg name="colors"/>
    <arg name="extensions" value="php"/>
    <arg name="parallel" value="8"/>
    
    <!-- Rules -->
    <rule ref="WordPress">
        <exclude name="Generic.Arrays.DisallowShortArraySyntax"/>
    </rule>
    
    <!-- Prefixes -->
    <rule ref="WordPress.NamingConventions.PrefixAllGlobals">
        <properties>
            <property name="prefixes" type="array">
                <element value="plugin_name"/>
            </property>
        </properties>
    </rule>
</ruleset>
```

### Running Code Standards

```bash
# Check all files
phpcs

# Check specific files
phpcs includes/

# Auto-fix issues
phpcbf

# Generate report
phpcs --report=summary
```

---

## Git Workflow

### Commit Message Format

```
type: brief description

- Detailed point 1
- Detailed point 2
- Detailed point 3
```

**Types:**
- `feat:` New feature
- `fix:` Bug fix
- `chore:` Maintenance (dependencies, configs)
- `refactor:` Code restructuring without behavior change
- `docs:` Documentation changes
- `style:` Code style/formatting changes
- `test:` Adding or updating tests

**Example:**
```
feat: add datetime slot management

- Added Datetime_Slot_Controller class
- Implemented CRUD operations for slots
- Added capacity tracking and validation
- Created admin interface for slot management
```

---

## Common Patterns Reference

### Singleton vs Global Variable

**Use global variable pattern** for simplicity:

```php
// In main plugin file
global $plugin_name_instance;
$plugin_name_instance = new Plugin_Name\Plugin();
$plugin_name_instance->run();

// Access anywhere
function get_plugin_instance(): Plugin {
    global $plugin_name_instance;
    return $plugin_name_instance;
}
```

### Lazy Loading Pattern

Use getter methods with null checks for on-demand instantiation:

```php
class Plugin {
    private ?Admin_Hooks $admin_hooks = null;
    private ?Public_Hooks $public_hooks = null;
    
    public function get_admin_hooks(): Admin_Hooks {
        if ( is_null( $this->admin_hooks ) ) {
            $this->admin_hooks = new Admin_Hooks();
        }
        return $this->admin_hooks;
    }
}
```

**Exception:** Settings class must be instantiated early (before `admin_init`) to register WordPress settings at the correct hook timing.

### No Inline HTML in Functions

**Rule:** Avoid mixing PHP and HTML with `?>...<?php` blocks inside functions.

```php
// ❌ Bad - inline HTML in function
public function show_notice(): void {
    ?>
    <div class="notice">
        <p><?php esc_html_e( 'Message', 'plugin-name' ); ?></p>
    </div>
    <?php
}

// ✅ Good - use printf/sprintf
public function show_notice(): void {
    printf(
        '<div class="notice"><p>%s</p></div>',
        esc_html__( 'Message', 'plugin-name' )
    );
}
```

**Exception:** Template files in `admin-views/` and `templates/` directories are presentation-layer files designed for HTML output.

**Why:** Improves readability, better IDE support, consistent code flow, may improve OPcache performance.

### Settings API Pattern

```php
class Settings {
    public function register_settings(): void {
        register_setting(
            'plugin_name_options',
            'plugin_name_setting',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_setting' ),
                'default'           => '',
            )
        );
        
        add_settings_section(
            'plugin_name_section',
            __( 'General Settings', 'plugin-name' ),
            array( $this, 'render_section' ),
            'plugin_name_settings'
        );
        
        add_settings_field(
            'plugin_name_field',
            __( 'Setting Label', 'plugin-name' ),
            array( $this, 'render_field' ),
            'plugin_name_settings',
            'plugin_name_section'
        );
    }
}
```

### Meta Box Pattern

```php
public function add_meta_box(): void {
    add_meta_box(
        'plugin_name_meta',
        __( 'Plugin Settings', 'plugin-name' ),
        array( $this, 'render_meta_box' ),
        'post',
        'normal',
        'default'
    );
}

public function save_meta_box( int $post_id ): void {
    // Verify nonce
    if ( ! isset( $_POST['plugin_name_nonce'] ) 
         || ! wp_verify_nonce( $_POST['plugin_name_nonce'], 'plugin_name_save' ) ) {
        return;
    }
    
    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Save data
    $value = isset( $_POST['plugin_field'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_field'] ) ) : '';
    update_post_meta( $post_id, '_plugin_name_field', $value );
}
```

---

## WooCommerce Integration

### Declare HPOS Compatibility

```php
add_action(
    'before_woocommerce_init',
    function () {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                'custom_order_tables',
                __FILE__,
                true
            );
        }
    }
);
```

### HPOS Data Access Rule

**Never use `get_post_meta()`, `update_post_meta()`, or direct SQL queries for Order data.**  
**Always use the CRUD getters and setters on the `WC_Order` object.**

```php
// ❌ Bad - Breaks in HPOS
$email = get_post_meta( $order_id, '_billing_email', true );

// ✅ Good - HPOS Compatible
$order = wc_get_order( $order_id );
$email = $order->get_billing_email();
$order->update_meta_data( 'custom_key', $value );
$order->save();
```

**Why:**
- WooCommerce High-Performance Order Storage (HPOS) moves order data from `wp_posts` to custom tables
- Direct post meta access fails silently when HPOS is enabled
- WooCommerce CRUD methods abstract the storage layer
- Always declare compatibility with `custom_order_tables`

### Product Data Tabs

```php
add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_tab' ) );

public function add_product_tab( array $tabs ): array {
    $tabs['plugin_name'] = array(
        'label'    => __( 'Plugin Data', 'plugin-name' ),
        'target'   => 'plugin_name_data',
        'class'    => array( 'show_if_simple', 'show_if_variable' ),
        'priority' => 60,
    );
    return $tabs;
}
```

---

## Tabbed Admin Interfaces

### When to Use Tabs

Use tabbed interfaces for admin pages with distinct functional areas:
- Different operational contexts (e.g., quick tasks vs. advanced features)
- Grouping related settings
- Separating frequently-accessed features from configuration

### Implementation Pattern

**URL Hash-Based Navigation:**
```
/wp-admin/admin.php?page=plugin-name#tab-name
```

**Benefits:**
- Tab state persists on page reload
- Browser back/forward button support
- Deep linking to specific tabs
- Shareable URLs to specific sections

### HTML Structure

```php
<!-- admin-views/settings-page.php -->
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <!-- Tab Navigation -->
    <nav class="nav-tab-wrapper wp-clearfix">
        <a href="#quick-export" class="nav-tab nav-tab-active" data-tab="quick-export">
            <?php esc_html_e( 'Quick Export', 'plugin-name' ); ?>
        </a>
        <a href="#advanced" class="nav-tab" data-tab="advanced">
            <?php esc_html_e( 'Advanced Reporting', 'plugin-name' ); ?>
        </a>
        <a href="#settings" class="nav-tab" data-tab="settings">
            <?php esc_html_e( 'Settings', 'plugin-name' ); ?>
        </a>
        <a href="#status" class="nav-tab" data-tab="status">
            <?php esc_html_e( 'Status & Help', 'plugin-name' ); ?>
        </a>
    </nav>
    
    <!-- Tab Content Panels -->
    <div class="tab-content">
        <div id="quick-export-panel" class="tab-panel active">
            <!-- Quick export content -->
        </div>
        
        <div id="advanced-panel" class="tab-panel" style="display:none;">
            <!-- Advanced reporting content -->
        </div>
        
        <div id="settings-panel" class="tab-panel" style="display:none;">
            <!-- Settings form -->
        </div>
        
        <div id="status-panel" class="tab-panel" style="display:none;">
            <!-- Status & help content -->
        </div>
    </div>
</div>
```

### JavaScript Implementation

```javascript
// assets/admin/va-admin.js
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.nav-tab');
    const panels = document.querySelectorAll('.tab-panel');
    
    // Activate tab from URL hash on page load
    const activeTab = window.location.hash.substring(1) || 'quick-export';
    activateTab(activeTab);
    
    // Tab click handlers
    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            const tabName = tab.dataset.tab;
            window.location.hash = tabName;
            activateTab(tabName);
        });
    });
    
    // Handle browser back/forward
    window.addEventListener('hashchange', () => {
        const tabName = window.location.hash.substring(1) || 'quick-export';
        activateTab(tabName);
    });
    
    function activateTab(tabName) {
        // Update nav tabs
        tabs.forEach(t => t.classList.remove('nav-tab-active'));
        document.querySelector(`[data-tab="${tabName}"]`)?.classList.add('nav-tab-active');
        
        // Show/hide panels
        panels.forEach(panel => {
            panel.style.display = 'none';
            panel.classList.remove('active');
        });
        const activePanel = document.getElementById(`${tabName}-panel`);
        if (activePanel) {
            activePanel.style.display = 'block';
            activePanel.classList.add('active');
        }
    }
});
```

### CSS Styling

```css
/* assets/admin/va-admin.css */
/* WordPress provides .nav-tab styling, we just need panel styles */

.tab-panel {
    margin-top: 20px;
    padding: 20px;
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.tab-panel.active {
    /* Additional active styling if needed */
}
```

### Conditional Asset Loading

**Only load admin assets on plugin pages:**

```php
// includes/class-admin-hooks.php
public function enqueue_admin_assets( string $hook_suffix ): void {
    // Only load on our settings page
    if ( 'toplevel_page_plugin-name' !== $hook_suffix ) {
        return;
    }
    
    wp_enqueue_style(
        'plugin-name-admin',
        plugins_url( 'assets/admin/va-admin.css', dirname( __FILE__ ) ),
        array(),
        $this->version
    );
    
    wp_enqueue_script(
        'plugin-name-admin',
        plugins_url( 'assets/admin/va-admin.js', dirname( __FILE__ ) ),
        array(),
        $this->version,
        true
    );
    
    // Localize script if needed
    wp_localize_script(
        'plugin-name-admin',
        'pluginNameAdmin',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'plugin_name_admin' ),
        )
    );
}
```

### Best Practices

1. **Default tab:** Use hash or default to most frequently accessed tab
2. **Native styling:** Use WordPress `.nav-tab` classes for consistency
3. **Accessibility:** Ensure keyboard navigation works properly
4. **Performance:** Only load JavaScript/CSS on relevant admin pages
5. **State preservation:** URL hash preserves state across page reloads
6. **Progressive enhancement:** Basic functionality works without JavaScript

---

## Testing & Debugging

### Error Logging

```php
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( sprintf(
        '[Plugin Name] %s: %s',
        __FUNCTION__,
        print_r( $debug_data, true )
    ) );
}
```

### Conditional Loading

```php
// Only load admin features in admin
if ( is_admin() ) {
    require_once __DIR__ . '/includes/class-admin-hooks.php';
}

// Only load public features on frontend
if ( ! is_admin() ) {
    require_once __DIR__ . '/includes/class-public-hooks.php';
}
```

---

## Performance Considerations

1. **Avoid loading everything always** - Use conditional requires
2. **Minimize database queries** - Use caching when appropriate
3. **Lazy load assets** - Only enqueue where needed
4. **Use transients for expensive operations**
5. **Consider object caching** - But don't rely on it

---

## Translation Ready

### Text Domain

Always use consistent text domain:

```php
__( 'Text to translate', 'plugin-name' );
_e( 'Text to echo', 'plugin-name' );
esc_html__( 'Text to translate and escape', 'plugin-name' );
esc_html_e( 'Text to echo and escape', 'plugin-name' );
```

### Loading Translations

```php
public function load_textdomain(): void {
    load_plugin_textdomain(
        'plugin-name',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}
```

---

## Key Takeaways

✅ **DO:**
- Use type hints and return types
- Follow WordPress Coding Standards
- Sanitize input, escape output
- Document all functions
- Use namespaces for classes
- Keep security in mind always
- Test with phpcs regularly

❌ **DON'T:**
- Use global variables for everything
- Skip nonce verification
- Trust user input
- Use IDs for CSS/JS selectors (usually)
- Ignore WordPress core functionality
- Reinvent the wheel
- Sacrifice security for convenience

---

**This is your portable base.** Copy to any WordPress plugin project and adapt as needed.
