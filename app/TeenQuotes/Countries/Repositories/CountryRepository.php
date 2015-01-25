<?php namespace TeenQuotes\Countries\Repositories;

interface CountryRepository {

	/**
	 * Retrieve a country by its id
	 *
	 * @param  int $id
	 * @return TeenQuotes\Countries\Models\Country
	 */
	public function findById($id);

	/**
	 * List all name and IDs for countries
	 *
	 * @return array
	 */
	public function listNameAndId();

	/**
	 * Retrieve all countries
	 *
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getAll();
}