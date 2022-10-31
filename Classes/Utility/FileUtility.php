<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use RuntimeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class FileUtility extends AbstractUtility
{
    public static function getFilesFromRelativePath(string $path): array
    {
        $array = [];
        $files = GeneralUtility::getFilesInDir(GeneralUtility::getFileAbsFileName($path));
        foreach ($files as $file) {
            $array[] = $file;
        }
        return $array;
    }

    /**
     * Add a trailing slash to a string (e.g. path)
     *        folder1/folder2 => folder1/folder2/
     *        folder1/folder2/ => folder1/folder2/
     */
    public static function addTrailingSlash(string $string): string
    {
        return rtrim($string, '/') . '/';
    }

    public static function getPathFromPathAndFilename(string $pathAndFilename): string
    {
        $pathInfo = pathinfo($pathAndFilename);
        return $pathInfo['dirname'];
    }

    /**
     * @throws \Exception
     */
    public static function createFolderIfNotExists(string $path): void
    {
        if (!is_dir($path) && !GeneralUtility::mkdir($path)) {
            throw new RuntimeException('Folder ' . self::getRelativeFolder($path) . ' could not be create!');
        }
    }

    /**
     * returns the filename from a given path
     * e.g. typo3temp/tx_in2studyfinder/export.csv -> export.csv
     */
    public static function getFilenameFromFileWithPath(string $fileWithPath): string
    {
        return basename($fileWithPath);
    }

    public static function prependContentToFile(string $pathAndFile, string $content): void
    {
        $absolutePathAndFile = GeneralUtility::getFileAbsFileName($pathAndFile);
        $lines = [];
        if (is_file($absolutePathAndFile)) {
            $lines = file($absolutePathAndFile);
        }
        array_unshift($lines, $content);
        GeneralUtility::writeFile($absolutePathAndFile, implode('', $lines));
    }

    public static function getRelativeFolder(string $path): string
    {
        if (PathUtility::isAbsolutePath($path)) {
            $path = PathUtility::getRelativePathTo($path);
        }
        return $path;
    }
}
