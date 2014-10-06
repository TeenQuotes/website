<?php namespace Codeception\Module;

use Codeception\Module;
use Illuminate\Support\Facades\Config;

class QuotesHelper extends Module {

	public function getNbComments()
	{
		return Config::get('app.comments.nbCommentsPerPage');
	}

	public function getTotalNumberOfQuotesToCreate()
	{
		return $this->getNbPagesToCreate() * $this->getNbQuotesPerPage();
	}

	public function getNbPagesToCreate()
	{
		return 3;
	}

	public function getNbQuotesPerPage()
	{
		return Config::get('app.quotes.nbQuotesPerPage');
	}
}