<?php namespace TeenQuotes\Stories\Presenters;

use Illuminate\Support\Str;
use Laracasts\Presenter\Presenter;
use Quote;

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