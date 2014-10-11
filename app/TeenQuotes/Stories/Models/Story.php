<?php namespace TeenQuotes\Stories\Models;

use Laracasts\Presenter\PresentableTrait;
use TeenQuotes\Stories\Models\Relations\StoryTrait as StoryRelationsTrait;
use TeenQuotes\Stories\Models\Scopes\StoryTrait as StoryScopesTrait;
use Toloquent;

class Story extends Toloquent {

	use PresentableTrait, StoryRelationsTrait, StoryScopesTrait;
	protected $presenter = 'TeenQuotes\Stories\Presenters\StoryPresenter';
	
	protected $table = 'stories';
	protected $fillable = [];

	/**
	 * The validation rules
	 * @var array
	 */
	public static $rules = [
		'represent_txt' => 'required|min:100|max:1000',
		'frequence_txt' => 'required|min:100|max:1000',
	];
}