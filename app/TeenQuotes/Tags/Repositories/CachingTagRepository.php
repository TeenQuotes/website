<?php namespace TeenQuotes\Tags\Repositories;

use Cache, Lang;
use TeenQuotes\Tags\Models\Tag;
use TeenQuotes\Quotes\Models\Quote;

class CachingTagRepository implements TagRepository {

	/**
	 * @var \TeenQuotes\Tags\Repositories\TagRepository
	 */
	private $tags;

	public function __construct(TagRepository $tags)
	{
		$this->tags = $tags;
	}

	/**
	 * Create a new tag
	 *
	 * @param string $name
	 * @return \TeenQuotes\Tags\Models\Tag
	 */
	public function create($name)
	{
		return $this->tags->create($name);
	}

	/**
	 * Get a tag thanks to its name
	 *
	 * @param string $name
	 * @return \TeenQuotes\Tags\Models\Tag|null
	 */
	public function getByName($name)
	{
		$callback = function() use ($name)
		{
			return $this->tags->getByName($name);
		};

		return Cache::rememberForever('tags.name-'.$name, $callback);
	}

	/**
	 * Add a tag to a quote
	 *
	 * @param \TeenQuotes\Quotes\Models\Quote $q
	 * @param \TeenQuotes\Tags\Models\Tag $t
	 */
	public function tagQuote(Quote $q, Tag $t)
	{
		Cache::forget($this->cacheNameForListTags($q));

		return $this->tags->tagQuote($q, $t);
	}

	/**
	 * Remove a tag from a quote
	 *
	 * @param \TeenQuotes\Quotes\Models\Quote $q
	 * @param \TeenQuotes\Tags\Models\Tag $t
	 */
	public function untagQuote(Quote $q, Tag $t)
	{
		Cache::forget($this->cacheNameForListTags($q));

		return $this->tags->untagQuote($q, $t);
	}

	/**
	 * Get a list of tags for a given quote
	 *
	 * @param  \TeenQuotes\Quotes\Models\Quote $q
	 * @return array
	 */
	public function tagsForQuote(Quote $q)
	{
		$key = $this->cacheNameForListTags($q);

		$callback = function() use($q)
		{
			return $this->tags->tagsForQuote($q);
		};

		return Cache::remember($key, 10, $callback);
	}

	/**
	 * Get the key name when we list tags for a quote
	 *
	 * @param  \TeenQuotes\Quotes\Models\Quote $q
	 * @return string
	 */
	private function cacheNameForListTags(Quote $q)
	{
		return 'tags.quote-'.$q->id.'.list-name.locale-'.Lang::locale();
	}
}