<?php

use TeenQuotes\Models\Relations\StoryTrait as StoryRelationsTrait;
use TeenQuotes\Models\Scopes\StoryTrait as StoryScopesTrait;

class Story extends Toloquent {

	use StoryRelationsTrait, StoryScopesTrait;
	
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