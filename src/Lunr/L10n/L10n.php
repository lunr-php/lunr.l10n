<?php

/**
 * This file contains the Localization helper class. It includes functions to
 * handle language definition, get supported languages of a project, etc
 *
 * SPDX-FileCopyrightText: Copyright 2010 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\L10n;

use Psr\Log\LoggerInterface;

/**
 * Localization support class
 */
class L10n extends AbstractL10n
{

    /**
     * Static list of supported languages
     * @var array|null
     */
    private static $languages;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger          Shared instance of a Logger class.
     * @param string          $localesLocation Location of translation files
     */
    public function __construct($logger, $localesLocation)
    {
        parent::__construct($logger, $localesLocation);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Get list of supported languages from the filesystem.
     *
     * Storing the values in a static attribute ensures the
     * filesystem is never parsed more than once per request
     *
     * @return array Supported languages
     */
    public function get_supported_languages()
    {
        if (self::$languages !== NULL)
        {
            return self::$languages;
        }

        self::$languages = [ $this->defaultLanguage ];

        foreach ($this->localesIterator as $file)
        {
            if ($file->isDot() || !$file->isDir() || $this->localesIterator->getFilename() == $this->defaultLanguage)
            {
                continue;
            }

            self::$languages[] = $this->localesIterator->getFilename();
        }

        return self::$languages;
    }

    /**
     * Convert a language (if supported) from ISO 639-1 to POSIX locale format.
     *
     * @param string $language The language to convert on ISO 639-1 format
     *
     * @return string $locale The POSIX locale that has been deemed most
     *                          appropriate or the default if not found
     */
    public function iso_to_posix($language)
    {
        $supported = $this->get_supported_languages();

        foreach ($supported as $locale)
        {
            if (locale_filter_matches($locale, $language, FALSE))
            {
                return $locale;
            }
        }

        return $this->defaultLanguage;
    }

}

?>
