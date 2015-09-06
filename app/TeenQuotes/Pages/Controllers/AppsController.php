<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Pages\Controllers;

use Agent;
use BaseController;
use Config;
use Lang;
use LaraSetting;
use Redirect;
use URL;
use View;

class AppsController extends BaseController
{
    public function redirectPlural()
    {
        return Redirect::route('apps', null, 301);
    }

    public function index()
    {
        $hasAndroidApp = Config::get('mobile.androidApp');
        $hasIOSApp     = Config::get('mobile.iOSApp');

        // Tablet
        if (Agent::isTablet()) {
            return Redirect::route('apps.device', 'tablet');
        }
        // Mobile
        elseif (Agent::isMobile()) {
            // Android
            if (Agent::isAndroidOS()) {
                if ($hasAndroidApp) {
                    return Redirect::away(Config::get('mobile.downloadLinkAndroid'));
                }

                return Redirect::route('apps.device', 'android');
            // iOS
            } elseif (Agent::isiOS()) {
                if ($hasIOSApp) {
                    return Redirect::away(Config::get('mobile.downloadLinkiOS'));
                }

                return Redirect::route('apps.device', 'ios');
            }

            return Redirect::route('apps.device', 'mobile');
        }

        // Desktop
        return Redirect::route('apps.device', 'desktop');
    }

    public function getDevice($device)
    {
        // Add data for Google Analytics in a view composer

        // Retrieve devices info from settings.json
        $devicesInfo = LaraSetting::get('devicesInfo')[0];

        $data = [
            'title'           => Lang::get('apps.'.$device.'Title'),
            'titleIcon'       => $this->getIconTitle($device),
            'deviceType'      => $device,
            'content'         => Lang::get('apps.'.$devicesInfo[$device]['text-key'], ['url' => URL::route('contact')]),
            'devicesInfo'     => $devicesInfo,
            'pageTitle'       => Lang::get('apps.'.$device.'Title').' | '.Lang::get('layout.nameWebsite'),
            'pageDescription' => Lang::get('apps.pageDescription'),
        ];

        return View::make('apps.download', $data);
    }

    private function getIconTitle($device)
    {
        switch ($device) {
            case 'android':
            case 'ios':
            case 'mobile':
                $result = 'fa-mobile';
                break;

            case 'tablet':
                $result = 'fa-tablet';
                break;
            case 'desktop':
                $result = 'fa-desktop';
                break;
        }

        return $result;
    }
}
