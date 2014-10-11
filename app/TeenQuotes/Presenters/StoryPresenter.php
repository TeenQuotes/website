<?php namespace TeenQuotes\Presenters;

use Laracasts\Presenter\Presenter;
use Quote;
use Str;

class StoryPresenter extends Presenter {

	public function storyAge()
	{
		return $this->created_at->diffForHumans();
	}

	public function totalQuotes()
	{
		// Round to nearest thousand
		return number_format(round(Quote::nbQuotesPublished(), - 3), 0, '.', ',');
	}

	public function pageDescription()
	{
		return Str::limit($this->frequence_txt, 200);
	}
}