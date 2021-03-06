<?php

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Class FileUtility
 */
class FileUtility extends AbstractUtility
{

    /**
     * Get all Files from a folder
     *
     * @param string $path Relative Path
     * @return array
     */
    public static function getFilesFromRelativePath($path)
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
     *
     * @param string $string
     * @return string
     */
    public static function addTrailingSlash($string)
    {
        return rtrim($string, '/') . '/';
    }

    /**
     * Get path from path and filename
     *
     * @param string $pathAndFilename
     * @return string
     */
    public static function getPathFromPathAndFilename($pathAndFilename)
    {
        $pathInfo = pathinfo($pathAndFilename);
        return $pathInfo['dirname'];
    }

    /**
     * Create folder
     *
     * @param $path
     * @return void
     * @throws \Exception
     */
    public static function createFolderIfNotExists($path)
    {
        if (!is_dir($path) && !GeneralUtility::mkdir($path)) {
            throw new \Exception('Folder ' . self::getRelativeFolder($path) . ' could not be create!');
        }
    }

    /**
     * returns the filename from a given path
     * e.g. typo3temp/tx_in2studyfinder/export.csv -> export.csv
     *
     * @param string $fileWithPath
     *
     * @return string
     */
    public static function getFilenameFromFileWithPath($fileWithPath)
    {
        return basename($fileWithPath);
    }

    /**
     * Prepend content to the beginning of a file
     *
     * @param string $pathAndFile
     * @param string $content
     * @return void
     */
    public static function prependContentToFile($pathAndFile, $content)
    {
        $absolutePathAndFile = GeneralUtility::getFileAbsFileName($pathAndFile);
        $lines = [];
        if (is_file($absolutePathAndFile)) {
            $lines = file($absolutePathAndFile);
        }
        array_unshift($lines, $content);
        GeneralUtility::writeFile($absolutePathAndFile, implode('', $lines));
    }

    /**
     * Get relative path from absolute path, but don't touch if it's already a relative path
     *
     * @param string $path
     * @return string
     */
    public static function getRelativeFolder($path)
    {
        if (PathUtility::isAbsolutePath($path)) {
            $path = PathUtility::getRelativePathTo($path);
        }
        return $path;
    }
}
