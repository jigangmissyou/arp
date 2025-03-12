<?php
/**
 * @license BSD-3-Clause
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace KadenceWP\KadenceBlocks\Dotenv\Store;

interface StoreInterface
{
    /**
     * Read the content of the environment file(s).
     *
     * @throws \KadenceWP\KadenceBlocks\Dotenv\Exception\InvalidEncodingException|\KadenceWP\KadenceBlocks\Dotenv\Exception\InvalidPathException
     *
     * @return string
     */
    public function read();
}
