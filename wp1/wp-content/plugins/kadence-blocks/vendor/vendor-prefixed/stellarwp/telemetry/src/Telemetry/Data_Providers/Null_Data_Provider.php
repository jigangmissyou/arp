<?php
/**
 * A data provider that provides no data, used for testing.
 *
 * @since   TBD
 *
 * @package KadenceWP\KadenceBlocks\StellarWP\Telemetry\Data_Providers;
 *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocks\StellarWP\Telemetry\Data_Providers;

use KadenceWP\KadenceBlocks\StellarWP\Telemetry\Contracts\Data_Provider;

/**
 * Class Null_Data_Provider.
 *
 * @since   TBD
 *
 * @package KadenceWP\KadenceBlocks\StellarWP\Telemetry\Data_Providers;
 */
class Null_Data_Provider implements Data_Provider {

	/**
	 * {@inheritDoc}
	 *
	 * @since   TBD
	 */
	public function get_data(): array {
		return [];
	}
}
