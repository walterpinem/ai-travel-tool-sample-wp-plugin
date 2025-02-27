<?php

/**
 * AI Travel Tool Sample
 *
 * @package       AITRAVELTOOL
 * @author        Walter Pinem
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   AI Travel Tool Sample
 * Plugin URI:    https://walterpinem.com/creating-an-ai-travel-tool-wordpress-plugin/
 * Description:   A basic sample of the AI Travel Tool.
 * Version:       1.0.0
 * Author:        Walter Pinem
 * Author URI:    https://walterpinem.com/
 * Text Domain:   ai-travel-tool-sample
 * Domain Path:   /languages
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) exit;

// Plugin name
define('AITRAVELTOOL_NAME',            'AITRAVELTOOL');

// Plugin version
define('AITRAVELTOOL_VERSION',        '1.0.0');

// Plugin Root File
define('AITRAVELTOOL_PLUGIN_FILE',    __FILE__);

// Plugin base
define('AITRAVELTOOL_PLUGIN_BASE',    plugin_basename(AITRAVELTOOL_PLUGIN_FILE));

// Plugin Folder Path
define('AITRAVELTOOL_PLUGIN_DIR',    plugin_dir_path(AITRAVELTOOL_PLUGIN_FILE));

// Plugin Folder URL
define('AITRAVELTOOL_PLUGIN_URL',    plugin_dir_url(AITRAVELTOOL_PLUGIN_FILE));

// All required files
require AITRAVELTOOL_PLUGIN_DIR . 'inc/handler.php';
require AITRAVELTOOL_PLUGIN_DIR . 'inc/settings.php';
require AITRAVELTOOL_PLUGIN_DIR . 'inc/shortcode.php';
