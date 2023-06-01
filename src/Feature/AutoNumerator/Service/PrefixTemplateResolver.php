<?php

declare(strict_types=1);

namespace App\Feature\AutoNumerator\Service;

/**
 * template: "[<prefix>-<number>] <card name>"
 */
final class PrefixTemplateResolver
{
    public static function getRegExpPrefix(string $prefix): string
    {
        return "/(\[{$prefix}-\d*\])/m";
    }

    public static function retrieveNumber(string $value, string $prefix): int
    {
        $patterns = [
            "/{$prefix}-/",
            "/\[/",
            "/\]/",
        ];

        return (int)preg_replace($patterns, '', $value);
    }

    public static function getPrefixWithNumber(string $prefix, int $number): string
    {
        return "[{$prefix}-{$number}]";
    }
}