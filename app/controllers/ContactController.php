<?php
class ContactController extends BaseController {
	
	public function index()
	{
		$data = [
			'pageTitle'          => Lang::get('contact.pageTitle'),
			'pageDescription'    => Lang::get('contact.pageDescription'),
			'contactTitle'       => Lang::get('contact.contactTitle'),
			'teamTitle'       	 => Lang::get('contact.teamTitle'),
			'stayInTouchTitle'   => Lang::get('contact.stayInTouchTitle'),
			'stayInTouchContent' => Lang::get('contact.stayInTouchContent'),
			'emailAddress'       => Lang::get('contact.emailAddress'),
			'chooseYourWeapon'   => Lang::get('contact.chooseYourWeapon'),
			'twitterAccount'     => Lang::get('layout.twitterUsername'),
			'teamMembers'        => LaraSetting::get('team'),
		];

		// Add description for each team member
		foreach ($data['teamMembers'] as $teamName)
			$data['teamDescription'.$teamName['firstName']] = Lang::get('contact.teamDescription'.$teamName['firstName']);

		return View::make('contact.show', $data);
	}
}