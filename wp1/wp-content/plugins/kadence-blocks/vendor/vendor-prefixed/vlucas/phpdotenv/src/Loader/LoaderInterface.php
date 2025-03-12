<?php
/**
 * @license BSD-3-Clause
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace KadenceWP\KadenceBlocks\Dotenv\Loader;

use KadenceWP\KadenceBlocks\Dotenv\Repository\RepositoryInterface;

interface LoaderInterface
{
    /**
     * Load the given entries into the repository.
     *
     * @param \KadenceWP\KadenceBlocks\Dotenv\Repository\RepositoryInterface $repository
     * @param \KadenceWP\KadenceBlocks\Dotenv\Parser\Entry[]                 $entries
     *
     * @return array<string,string|null>
     */
    public function load(RepositoryInterface $repository, array $entries);
}
