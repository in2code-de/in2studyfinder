<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Embedding;

use In2code\In2studyfinder\Domain\Model\StudyCourse;

/**
 * One day i want to fetch configuration from typoscript
 */
class EmbeddingsConfiguration
{
    public static function get(): array
    {
        return [
            StudyCourse::TABLE => ['title', 'teaser', 'description'],
        ];
    }

    public static function getAllowedTables(): array
    {
        return array_keys(self::get());
    }

    public static function getAllowedFields(string $tableName): array
    {
        return self::get()[$tableName] ?? [];
    }
}
