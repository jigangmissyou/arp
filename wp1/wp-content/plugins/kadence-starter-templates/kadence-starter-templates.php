<?php
/**
 * Plugin Name: Starter Templates by Kadence WP
 * Description: Launch a beautiful website with the power of AI or using our classic pre built style.
 * Version: 2.1.9
 * Author: Kadence WP
 * Author URI: https://kadencewp.com/
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * Text Domain: kadence-starter-templates
 *
 * @package Kadence Starter Templates
 */

// Block direct access to the main plugin file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KADENCE_STARTER_TEMPLATES_PATH', plugin_dir_path( __FILE__ ) );
define( 'KADENCE_STARTER_TEMPLATES_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'KADENCE_STARTER_TEMPLATES_VERSION', '2.1.9' );

require_once plugin_dir_path( __FILE__ ) . 'vendor/vendor-prefixed/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'class-kadence-starter-templates.php';

use KadenceWP\KadenceStarterTemplates\App;
use KadenceWP\KadenceStarterTemplates\StellarWP\ContainerContract\ContainerInterface;
use KadenceWP\KadenceStarterTemplates\Container;
use KadenceWP\KadenceStarterTemplates\StellarWP\ProphecyMonorepo\Container\ContainerAdapter;
use KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\Config;
use KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\Uplink;
use KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\Register;

/**
 * Load the plugin app.
 */
function kadence_starter_templates_init() {
	$container = new Container();

	// The Kadence Starter Templates Application.
	App::instance( new ContainerAdapter( $container->container() ) );
	/**
	 * Uplink.
	 */
	Config::set_container( $container );
	Config::set_hook_prefix( 'kadence-starter-templates' );
	if ( ! class_exists( '\KadenceWP\KadenceBlocks\App' ) ) {
		Config::set_auth_cache_expiration( WEEK_IN_SECONDS );
		Config::set_token_auth_prefix( 'kadence' );
	}
	Uplink::init();

	Register::plugin(
		'kadence-starter-templates',
		'Kadence Starter Templates',
		KADENCE_STARTER_TEMPLATES_VERSION,
		'kadence-starter-templates/kadence-starter-templates.php',
		App::class
	);
	add_filter( 'stellarwp/uplink/kadence-starter-templates/prevent_update_check', '__return_true' );
	add_filter(
		'stellarwp/uplink/kadence-starter-templates/api_get_base_url',
		static function() {
			return 'https://licensing.kadencewp.com';
		},
		10,
		0
	);
}
add_action( 'plugins_loaded', 'kadence_starter_templates_init', 2 );

/**
 * Load the plugin textdomain
 */
function kadence_starter_templates_lang() {
	load_plugin_textdomain( 'kadence-starter-templates', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'kadence_starter_templates_lang' );

/**
 * The Kadence Starter Templates Application Container.
 *
 * @see kadence_starter_templates_init()
 *
 * @note kadence_starter_templates_init() must be called before this one.
 *
 * @return ContainerInterface
 * @throws InvalidArgumentException
 */
function kadence_starter_templates(): ContainerInterface {
	return App::instance()->container();
}
