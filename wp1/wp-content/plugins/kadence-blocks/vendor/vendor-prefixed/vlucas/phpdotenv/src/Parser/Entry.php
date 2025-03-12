<?php
/**
 * @license BSD-3-Clause
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace KadenceWP\KadenceBlocks\Dotenv\Parser;

use KadenceWP\KadenceBlocks\PhpOption\Option;

final class Entry
{
    /**
     * The entry name.
     *
     * @var string
     */
    private $name;

    /**
     * The entry value.
     *
     * @var \KadenceWP\KadenceBlocks\Dotenv\Parser\Value|null
     */
    private $value;

    /**
     * Create a new entry instance.
     *
     * @param string                    $name
     * @param \KadenceWP\KadenceBlocks\Dotenv\Parser\Value|null $value
     *
     * @return void
     */
    public function __construct(string $name, ?Value $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get the entry name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the entry value.
     *
     * @return \KadenceWP\KadenceBlocks\PhpOption\Option<\Dotenv\Parser\Value>
     */
    public function getValue()
    {
        /** @var \KadenceWP\KadenceBlocks\PhpOption\Option<\Dotenv\Parser\Value> */
        return Option::fromValue($this->value);
    }
}
