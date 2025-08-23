<?php

/**
 * This file contains the abstract definition for the
 * PHP array Localization Provider.
 *
 * SPDX-FileCopyrightText: Copyright 2011 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\L10n;

use Psr\Log\LoggerInterface;

/**
 * PHP (array) Localization Provider class
 */
class PHPL10nProvider extends L10nProvider
{

    /**
     * Attribute that stores the language array
     * @var array
     */
    private $langArray;

    /**
     * Whether the langArray was initialized already or not.
     * @var bool
     */
    private $initialized;

    /**
     * Constructor.
     *
     * @param string          $language        POSIX locale definition
     * @param string          $domain          Localization domain
     * @param LoggerInterface $logger          Shared instance of a logger class
     * @param string          $localesLocation Location of translation files
     */
    public function __construct($language, $domain, $logger, $localesLocation)
    {
        parent::__construct($language, $domain, $logger, $localesLocation);

        $this->initialized = FALSE;
        $this->langArray   = [];
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->langArray);

        parent::__destruct();
    }

    /**
     * Initialization method for setting up the provider.
     *
     * @param string $language POSIX locale definition
     *
     * @return void
     */
    protected function init($language)
    {
        if ($this->initialized === TRUE)
        {
            return;
        }

        if ($language != $this->defaultLanguage)
        {
            $lang     = [];
            $langpath = $this->localesLocation . '/' . $language . '/';
            include $langpath . $this->domain . '.php';
            $this->langArray =& $lang;
        }

        $this->initialized = TRUE;
    }

    /**
     * Return a translated string.
     *
     * @param string $identifier Identifier for the requested string
     * @param string $context    Context information for the requested string
     *
     * @return string $string Translated string, identifier by default
     */
    public function lang($identifier, $context = '')
    {
        //Check if it's necessary to translate the identifier
        if ($this->language == $this->defaultLanguage)
        {
            return $identifier;
        }

        $this->init($this->language);

        //Check if the identifier is not contained in the language array
        if (!array_key_exists($identifier, $this->langArray))
        {
            return $identifier;
        }

        if ($context == '')
        {
            //Check if the key have context associated in the array
            if (is_array($this->langArray[$identifier]))
            {
                foreach ($this->langArray[$identifier] as $value)
                {
                    if (is_array($value) && isset($value[0]) && !is_array($value[0]))
                    {
                        return $value[0];
                    }
                }

                return $identifier;
            }

            return $this->langArray[$identifier];
        }

        if (!is_array($this->langArray[$identifier]) || !array_key_exists($context, $this->langArray[$identifier]))
        {
            return $identifier;
        }

        if (is_array($this->langArray[$identifier][$context]))
        {
            return $identifier;
        }

        return $this->langArray[$identifier][$context];
    }

    /**
     * Return a translated string, with proper singular/plural form.
     *
     * @param string $singular Identifier for the singular version of
     *                         the string
     * @param string $plural   Identifier for the plural version of the string
     * @param int    $amount   The amount the translation should be based on
     * @param string $context  Context information for the requested string
     *
     * @return string $string Translated string, identifier by default
     */
    public function nlang($singular, $plural, $amount, $context = '')
    {
        //Check if it's necessary to translate
        if ($this->language == $this->defaultLanguage)
        {
            return ($amount == 1 ? $singular : $plural);
        }

        $this->init($this->language);

        //Check if there is a translation available
        if (!array_key_exists($singular, $this->langArray))
        {
            return ($amount == 1 ? $singular : $plural);
        }

        // Check if the base string actually has plural forms available
        if (!is_array($this->langArray[$singular]))
        {
            return $this->langArray[$singular];
        }

        // Check if we have a simple translation with the given context
        if (($context != '')
            && !array_key_exists($plural, $this->langArray[$singular])
            && array_key_exists($context, $this->langArray[$singular])
            && !is_array($this->langArray[$singular][$context])
        )
        {
            return $this->langArray[$singular][$context];
        }

        // Check if we have plural forms available
        if (!array_key_exists($plural, $this->langArray[$singular]))
        {
            return ($amount == 1 ? $singular : $plural);
        }

        if ($context == '')
        {
            if (!is_array($this->langArray[$singular][$plural])
                || !isset($this->langArray[$singular][$plural][0])
                || !isset($this->langArray[$singular][$plural][1])
            )
            {
                return ($amount == 1 ? $singular : $plural);
            }

            if ($amount == 1)
            {
                if (!is_array($this->langArray[$singular][$plural][0]))
                {
                    return $this->langArray[$singular][$plural][0];
                }

                return $singular;
            }

            if (!is_array($this->langArray[$singular][$plural][1]))
            {
                return $this->langArray[$singular][$plural][1];
            }

            return $plural;
        }

        // Check whether we have the given context available
        if (!is_array($this->langArray[$singular][$plural])
            || !isset($this->langArray[$singular][$plural][$context])
            || !is_array($this->langArray[$singular][$plural][$context])
        )
        {
            return ($amount == 1 ? $singular : $plural);
        }

        if ($amount == 1)
        {
            return $this->langArray[$singular][$plural][$context][0];
        }

        return $this->langArray[$singular][$plural][$context][1];
    }

}

?>
