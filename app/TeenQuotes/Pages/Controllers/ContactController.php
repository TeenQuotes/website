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

use BaseController;
use Lang;
use LaraSetting;
use View;

class ContactController extends BaseController
{
    public function index()
    {
        $data = [
            'chooseYourWeapon'     => Lang::get('contact.chooseYourWeapon'),
            'contactTitle'         => Lang::get('contact.contactTitle'),
            'emailAddress'         => Lang::get('contact.emailAddress'),
            'pageDescription'      => Lang::get('contact.pageDescription'),
            'pageTitle'            => Lang::get('contact.pageTitle'),
            'stayInTouchContent'   => Lang::get('contact.stayInTouchContent'),
            'stayInTouchTitle'     => Lang::get('contact.stayInTouchTitle'),
            'teamMembers'          => LaraSetting::get('team'),
            'teamTitle'            => Lang::get('contact.teamTitle'),
            'twitterAccount'       => Lang::get('layout.twitterUsername'),
        ];

        // Add description for each team member
        foreach ($data['teamMembers'] as $teamName) {
            $data['teamDescription'.$teamName['firstName']] = Lang::get('contact.teamDescription'.$teamName['firstName']);
        }

        return View::make('contact.show', $data);
    }
}
