<?php
/**
 * The API Kadence_Blocks_provided by each builder.
 *
 * @package lucatume\DI52
 *
 * @license GPL-3.0
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocks\lucatume\DI52\Builders;

/**
 * Interface BuilderInterface
 *
 * @package KadenceWP\KadenceBlocks\lucatume\DI52\Builders
 */
interface BuilderInterface
{
    /**
     * Builds and returns the implementation handled by the builder.
     *
     * @return mixed The implementation provided by the builder.
     */
    public function build();
}
