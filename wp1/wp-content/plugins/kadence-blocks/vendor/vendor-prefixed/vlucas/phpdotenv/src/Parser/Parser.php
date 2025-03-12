<?php
/**
 * @license BSD-3-Clause
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace KadenceWP\KadenceBlocks\Dotenv\Parser;

use KadenceWP\KadenceBlocks\Dotenv\Exception\InvalidFileException;
use KadenceWP\KadenceBlocks\Dotenv\Util\Regex;
use KadenceWP\KadenceBlocks\GrahamCampbell\ResultType\Result;
use KadenceWP\KadenceBlocks\GrahamCampbell\ResultType\Success;

final class Parser implements ParserInterface
{
    /**
     * Parse content into an entry array.
     *
     * @param string $content
     *
     * @throws \KadenceWP\KadenceBlocks\Dotenv\Exception\InvalidFileException
     *
     * @return \KadenceWP\KadenceBlocks\Dotenv\Parser\Entry[]
     */
    public function parse(string $content)
    {
        return Regex::split("/(\r\n|\n|\r)/", $content)->mapError(static function () {
            return 'Could not split into separate lines.';
        })->flatMap(static function (array $lines) {
            return self::process(Lines::process($lines));
        })->mapError(static function (string $error) {
            throw new InvalidFileException(\sprintf('Failed to parse dotenv file. %s', $error));
        })->success()->get();
    }

    /**
     * Convert the raw entries into proper entries.
     *
     * @param string[] $entries
     *
     * @return \KadenceWP\KadenceBlocks\GrahamCampbell\ResultType\Result<\Dotenv\Parser\Entry[],string>
     */
    private static function process(array $entries)
    {
        /** @var \KadenceWP\KadenceBlocks\GrahamCampbell\ResultType\Result<\Dotenv\Parser\Entry[],string> */
        return \array_reduce($entries, static function (Result $result, string $raw) {
            return $result->flatMap(static function (array $entries) use ($raw) {
                return EntryParser::parse($raw)->map(static function (Entry $entry) use ($entries) {
                    /** @var \KadenceWP\KadenceBlocks\Dotenv\Parser\Entry[] */
                    return \array_merge($entries, [$entry]);
                });
            });
        }, Success::create([]));
    }
}
