<?php

class LegalController extends BaseController {
	
	public function show($page = null)
	{
		// Redirect to the default page if no argument has been given
		if (is_null($page))
			return Redirect::route('legal.show', 'tos', 301);

		if ($page == 'tos')
			$displayName = 'termsOfUse';
		else
			$displayName = 'privacyPolicy';

		$arianeLineLinks = [
			'tos'     => Lang::get('legal.termsOfUseTitle'),
			'privacy' => Lang::get('legal.privacyPolicyTitle'),
		]; 

		$data = [
			'title'           => Lang::get('legal.'.$displayName.'Title'),
			'content'         => Lang::get('legal.'.$displayName.'Content'),
			'arianeLineLinks' => $arianeLineLinks,
		];

		return View::make('legal.show', $data);
	}
}