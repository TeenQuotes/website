<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Tags\Repositories;

use Illuminate\Database\Eloquent\Collection;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tags\Models\Tag;

class DbTagRepository implements TagRepository
{
    /**
     * Create a new tag.
     *
     * @param string $name
     *
     * @return \TeenQuotes\Tags\Models\Tag
     */
    public function create($name)
    {
        return Tag::create(compact('name'));
    }

    /**
     * Get a tag thanks to its name.
     *
     * @param string $name
     *
     * @return \TeenQuotes\Tags\Models\Tag|null
     */
    public function getByName($name)
    {
        return Tag::whereName($name)->first();
    }

    /**
     * Add a tag to a quote.
     *
     * @param \TeenQuotes\Quotes\Models\Quote $q
     * @param \TeenQuotes\Tags\Models\Tag     $t
     */
    public function tagQuote(Quote $q, Tag $t)
    {
        $q->tags()->attach($t);
    }

    /**
     * Remove a tag from a quote.
     *
     * @param \TeenQuotes\Quotes\Models\Quote $q
     * @param \TeenQuotes\Tags\Models\Tag     $t
     */
    public function untagQuote(Quote $q, Tag $t)
    {
        $q->tags()->detach($t);
    }

    /**
     * Get a list of tags for a given quote.
     *
     * @param \TeenQuotes\Quotes\Models\Quote $q
     *
     * @return array
     */
    public function tagsForQuote(Quote $q)
    {
        return $q->tags()->lists('name');
    }

    /**
     * Get the total number of quotes having a tag.
     *
     * @param \TeenQuotes\Tags\Models\Tag $t
     *
     * @return int
     */
    public function totalQuotesForTag(Tag $t)
    {
        return $t->quotes()->count();
    }

    /**
     * Get the quotes that are not tagged yet but should be tagged.
     *
     * @param \TeenQuotes\Tags\Models\Tag $t
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function quotesToTag(Tag $t)
    {
        $words = $this->wordsForTag($t);

        return Quote::published()
        ->where(function ($query) use ($words) {
            $method = 'where';
            foreach ($words as $word) {
                $query = $query->$method('content', 'like', '%'.$word.'%');
                $method = 'orWhere';
            }
        })
        ->whereNotIn('id', $t->quotes()->lists('id'))
        ->get();
    }

    /**
     * Find related quotes.
     *
     * @param \TeenQuotes\Quotes\Models\Quote $q
     * @param int $nb
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function relatedQuotes(Quote $q, $nb=3)
    {
        $tag = $q->tags()->first();
        if (is_null($tag)) {
            return new Collection([]);
        }

        return $tag->quotes()->orderBy('id', 'DESC')
            ->where('id', '<', $q->id)
            ->take($nb)
            ->get();
    }

    /**
     * Get all tags.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allTags()
    {
        return Tag::all();
    }

    protected function wordsForTag(Tag $t)
    {
        $words = [
            'school'  => ['school', 'college'],
            'family'  => ['family', 'mother', 'father', 'brother', 'sister'],
            'love'    => ['relationship', 'love', 'heart'],
            'friends' => ['friend', 'bestfriend'],
            'holiday' => ['holiday', 'vacation'],
            'music'   => ['music', 'song', 'sing'],
            'awkward' => ['awkward', 'strange', 'embarrass'],
            'book'    => ['book', 'reading'],
        ];

        return $words[$t->name];
    }
}
