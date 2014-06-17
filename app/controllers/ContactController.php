<?php
class ContactController extends BaseController {
	
	public function index()
	{
		$data = [
			'pageTitle'          => Lang::get('contact.pageTitle'),
			'pageDescription'    => Lang::get('contact.pageDescription'),
			'title'              => Lang::get('contact.title'),
			'stayInTouchTitle'   => Lang::get('contact.stayInTouchTitle'),
			'stayInTouchContent' => Lang::get('contact.stayInTouchContent'),
			'emailAddress'       => Lang::get('contact.emailAddress'),
			'chooseYourWeapon'   => Lang::get('contact.chooseYourWeapon'),
			'twitterAccount'     => Lang::get('layout.twitterUsername'),
		];

		return View::make('contact.index', $data);
	}
}