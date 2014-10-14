<?php namespace TeenQuotes\Stories\Presenters;

use Illuminate\Support\Str;
use Laracasts\Presenter\Presenter;
use TeenQuotes\Quotes\Models\Quote;

class StoryPresenter extends Presenter {

	public function storyAge()
	{
		return $this->created_at->diffForHumans();
	}

	public function totalQuotes()
	{
		// Round to nearest thousand
		$quoteRepo = App::make('TeenQuotes\Quotes\Repositories\QuoteRepository');

		return number_format(round($quoteRepo->totalPublished(), - 3), 0, '.', ',');
	}

	public function pageDescription()
	{
		return Str::limit($this->frequence_txt, 200);
	}
}