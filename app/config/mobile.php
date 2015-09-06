<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    // Do we have an iOS app?
    'iOSApp' => true,

    // Do we have an Android app?
    'androidApp' => true,

    // The ID of the iOS application
    'iOSAppID' => 'id1008642341',

    // The name of package for the Android app
    'androidPackage' => 'com.mytriber.ohteenquotes',

    // URLs to download applications
    'downloadLinkiOS'     => 'https://itunes.apple.com/us/app/id1008642341',
    'downloadLinkAndroid' => 'https://play.google.com/store/apps/details?id=com.mytriber.ohteenquotes',

    // Scheme for deep links
    'deepLinksProtocol' => 'TeenQuotes://',
];
