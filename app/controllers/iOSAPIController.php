<?php

class iOSAPIController extends BaseController {

	public function getInfoMembreRecherche()
	{
		$json = array();
		$pseudo = Input::get('pseudo');

		if (!empty($pseudo)) {

			$user = User::whereLogin($pseudo)->with('favoriteQuotes', 'comments')->first();

			if (!is_null($user)) {

				$data = [
					'pseudo'            => $user->login,
					'avatar'            => $user->getURLAvatar(),
					'nbQuotesApproved'  => Quote::forUser($user)->published()->count(),
					'nbQuotesSubmited'  => Quote::forUser($user)->count(),
					'nbQuotesFavorites' => $user->favorite_quotes->count(),
					'nbComments'        => $user->comments->count(),
				];

				$json = array($data);
			}
		}

		return Response::json($json);
	}

	public function getReinitAvatar()
	{
		$user = User::whereLogin(Input::get('pseudo'))->first();

		if (!is_null($user)) {
			$user->avatar = 'icon50.png';
			$user->save();
		}
	}

	public function getPageMax()
	{
		$nbQuotesApproved = Quote::published()->count();
		$nbPages = ceil($nbQuotesApproved / Config::get('app.quotes.nbQuotesPerPage'));

		return Response::json(['nombrePage' => $nbPages]);
	}
}