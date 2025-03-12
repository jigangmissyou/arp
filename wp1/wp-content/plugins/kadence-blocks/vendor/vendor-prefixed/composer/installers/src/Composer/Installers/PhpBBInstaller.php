<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocks\Composer\Installers;

class PhpBBInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'extension' => 'ext/{$vendor}/{$name}/',
        'language'  => 'language/{$name}/',
        'style'     => 'styles/{$name}/',
    );
}
