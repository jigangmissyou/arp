<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */ declare( strict_types=1 );

namespace KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\Auth;

use KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\Auth\Admin\Connect_Controller;
use KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\Auth\Admin\Disconnect_Controller;
use KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\Config;
use KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\Resources\Collection;
use KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\Resources\Resource;

/**
 * Manages Token Authorization WordPress actions to connect/disconnect
 * tokens.
 */
final class Action_Manager {

	public const ACTION = 'admin_action';

	/**
	 * @var Disconnect_Controller
	 */
	private $disconnect_controller;

	/**
	 * @var Connect_Controller
	 */
	private $connect_controller;

	/**
	 * @var Collection
	 */
	private $resources;

	/**
	 * @param  Disconnect_Controller  $disconnect_controller
	 * @param  Connect_Controller     $connect_controller
	 * @param  Collection             $resources
	 */
	public function __construct(
		Disconnect_Controller $disconnect_controller,
		Connect_Controller $connect_controller,
		Collection $resources
	) {
		$this->disconnect_controller = $disconnect_controller;
		$this->connect_controller    = $connect_controller;
		$this->resources             = $resources;
	}

	/**
	 * Get the resource's unique hook name.
	 *
	 * @param  Resource  $resource  The resource.
	 *
	 * @throws \RuntimeException
	 *
	 * @return string
	 */
	public function get_hook_name( Resource $resource ): string {
		return sprintf( 'stellarwp/uplink/%s/%s_%s',
			Config::get_hook_prefix(),
			self::ACTION,
			$resource->get_slug()
		);
	}

	/**
	 * Register a unique action for each resource in order to fire off connect/disconnect logic
	 * uniquely so as one plugin would not interfere with another.
	 *
	 * @action admin_init
	 *
	 * @throws \RuntimeException
	 *
	 * @return void
	 */
	public function add_actions(): void {
		foreach ( $this->resources as $resource ) {
			$hook_name = $this->get_hook_name( $resource );

			add_action(
				$hook_name,
				[ $this->disconnect_controller, 'maybe_disconnect' ]
			);

			add_action(
				$hook_name,
				[ $this->connect_controller, 'maybe_store_token_data' ]
			);
		}
	}

	/**
	 * When an `uplink_slug` query parameter is available, fire off the appropriate
	 * resource action.
	 *
	 * @action current_screen
	 *
	 * @throws \RuntimeException
	 *
	 * @return void
	 */
	public function do_action(): void {
		if ( empty( $_REQUEST[ Disconnect_Controller::SLUG ] ) ) {
			return;
		}

		$action = $_REQUEST[ Disconnect_Controller::SLUG ];

		/**
		 * Fires when an 'uplink_slug' request variable is sent.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to
		 * the action derived from the `GET` or `POST` request.
		 */
		do_action( sprintf( 'stellarwp/uplink/%s/%s_%s',
			Config::get_hook_prefix(),
			self::ACTION,
			$action
		) );
	}

}
