<?php
/**
 * @license GPL-2.0-only
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */ declare(strict_types=1);

namespace KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\ImageDownloader\Sanitization\Sanitizers;

use KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\ImageDownloader\Sanitization\Contracts\Sanitizer;

/**
 * The WordPress filename sanitizer.
 *
 * @codeCoverageIgnore
 */
final class WPFileNameSanitizer implements Sanitizer
{
	/**
	 * {@inheritDoc}
	 */
	public function __invoke(string $filename): string {
		// @phpstan-ignore-next-line
		return strtolower(sanitize_file_name($filename));
	}
}
