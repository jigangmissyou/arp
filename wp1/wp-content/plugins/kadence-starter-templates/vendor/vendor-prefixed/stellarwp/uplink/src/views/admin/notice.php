<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */ declare( strict_types=1 );
/**
 * Render a WordPress dashboard notice.
 *
 * @see \KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\Notice\Notice_Controller
 *
 * @var string $message The message to display.
 * @var string $classes The CSS classes for the notice.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="<?php echo esc_attr( $classes ) ?>">
	<p><?php echo esc_html( $message ) ?></p>
</div>
