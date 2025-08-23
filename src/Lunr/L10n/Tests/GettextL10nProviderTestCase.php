<?php

/**
 * This file contains the GettextL10nProviderTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\L10n\Tests;

use Lunr\Halo\LunrBaseTestCase;
use Lunr\L10n\GettextL10nProvider;
use Psr\Log\LoggerInterface;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the GettextL10nProvider class.
 *
 * @covers Lunr\L10n\GettextL10nProvider
 */
abstract class GettextL10nProviderTestCase extends LunrBaseTestCase
{

    /**
     * Mock Object for a Logger class.
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Instance of the tested class.
     * @var GettextL10nProvider
     */
    protected GettextL10nProvider $class;

    /**
     * The language used for testing.
     * @var string
     */
    protected const LANGUAGE = 'de_DE';

    /**
     * The domain used for testing.
     * @var string
     */
    protected const DOMAIN = 'Lunr';

    /**
     * Base locale value.
     * @var string
     */
    private $baseLocale;

    /**
     * Base domain value.
     * @var string
     */
    private $baseDomain;

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $this->baseLocale = setlocale(LC_MESSAGES, 0);
        $this->baseDomain = textdomain(NULL);

        $this->class = new GettextL10nProvider(self::LANGUAGE, self::DOMAIN, $this->logger, TEST_STATICS . '/l10n/');
        $this->class->set_default_language('nl_NL');
        $this->class->set_locales_location(TEST_STATICS . '/l10n');

        parent::baseSetUp($this->class);
    }

    /**
     * TestCase Destructor.
     */
    public function tearDown(): void
    {
        setlocale(LC_MESSAGES, $this->baseLocale);
        textdomain($this->baseDomain);

        unset($this->class);
        unset($this->logger);
        unset($this->baseLocale);
        unset($this->baseDomain);

        parent::tearDown();
    }

}

?>
