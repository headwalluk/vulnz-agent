<?php
/**
 * Constants used by the plugin.
 *
 * @package Vulnz_Agent
 */

declare(strict_types=1);

namespace Vulnz_Agent;

// Block direct access.
defined( 'ABSPATH' ) || die();

define( 'Vulnz_Agent\PLUGIN_DIR', \plugin_dir_path( __FILE__ ) );
define( 'Vulnz_Agent\PLUGIN_URL', \plugin_dir_url( __FILE__ ) );

// WordPress option names.
const SETTINGS_GROUP           = 'wp_vulnz_settings';
const IS_VULNZ_ENABLED         = 'wp_vulnz_enabled';
const VULNZ_API_URL            = 'wp_vulnz_api_url';
const VULNZ_API_KEY            = 'wp_vulnz_api_key';
const LAST_CRON_RUN            = 'wp_vulnz_last_cron_run';
const WEBSITE_CACHE_KEY_PREFIX = 'wp_vulnz_website_';

// Cron schedule and action names.
const SCHEDULE_NAME = 'vulnz_agent';

// AJAX action names.
const SYNC_NOW_ACTION_NAME  = 'vulnz_agent_sync_now';
const SYNC_NOW_ACTION_NONCE = 'vulnz_agent_sync_now_nonce';

const WEBSITE_DATA_CACHE_TTL = \MINUTE_IN_SECONDS;

const API_REQUEST_TIMEOUT = 10;

// Default API endpoint URL (base URL without /api path).
const DEFAULT_VULNZ_API_URL = 'https://api.vulnz.net';

// Plugin list sort order: 'title' or 'slug'.
const PLUGIN_SORT_ORDER = 'title';

// Dummy value for API key field to prevent leaking actual key in HTML.
const DUMMY_API_KEY = 'API KEY HIDDEN FOR PRIVACY';

// Admin UI configuration.
const ADMIN_INPUT_FIELD_SIZE  = 50;
const ADMIN_MENU_POSITION     = 80;
const ADMIN_MENU_ICON         = 'dashicons-shield-alt';
const ADMIN_PAGE_HOOK_SUMMARY = 'toplevel_page_vulnz-agent-summary';
