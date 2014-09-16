<?php namespace TeenQuotes\Presenters;

use Laracasts\Presenter\Presenter;

class StoryPresenter extends Presenter {
	
	public function storyAge()
	{
		return $this->created_at->diffForHumans();
	}
}