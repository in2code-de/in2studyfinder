<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use RuntimeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class FileUtility extends AbstractUtility
{
    /**
     * @throws \Exception
     */
    public static function createFolderIfNotExists(string $path): void
    {
        if (!is_dir($path) && !GeneralUtility::mkdir($path)) {
            throw new RuntimeException('Folder ' . self::getRelativeFolder($path) . ' could not be create!', 8451014118);
        }
    }

    public static function getRelativeFolder(string $path): string
    {
        if (PathUtility::isAbsolutePath($path)) {
            return PathUtility::getRelativePathTo($path);
        }

        return $path;
    }
}
