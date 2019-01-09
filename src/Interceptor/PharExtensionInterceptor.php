<?php
declare(strict_types=1);
namespace TYPO3\PharStreamWrapper\Interceptor;

/*
 * This file is part of the TYPO3 project.
 *
 * It is free software; you can redistribute it and/or modify it under the terms
 * of the MIT License (MIT). For the full copyright and license information,
 * please read the LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\PharStreamWrapper\Assertable;
use TYPO3\PharStreamWrapper\Helper;
use TYPO3\PharStreamWrapper\Exception;

class PharExtensionInterceptor implements Assertable
{
    /**
     * Determines whether the base file name has a ".phar" suffix.
     *
     * @param string $path
     * @param string $command
     * @return bool
     * @throws Exception
     */
    public function assert(string $path, string $command): bool
    {
        if ($this->baseFileContainsPharExtension($path)) {
            return true;
        }
        throw new Exception(
            sprintf(
                'Unexpected file extension in "%s"',
                $path
            ),
            1535198703
        );
    }

    /**
     * @param string $path
     * @return bool
     */
    private function baseFileContainsPharExtension(string $path): bool
    {
        $baseFile = Helper::determineBaseFile($path);
        if ($baseFile === null) {
            return false;
        }
        // If the stream wrapper is registered by invoking a phar file that does
        // not not have .phar extension then this should be allowed. For
        // example, some CLI tools recommend removing the extension.
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $caller = array_pop($backtrace);
        if (isset($caller['file']) && $baseFile === $caller['file']) {
            return true;
        }
        $fileExtension = pathinfo($baseFile, PATHINFO_EXTENSION);
        return strtolower($fileExtension) === 'phar';
    }
}
