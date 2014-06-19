<?php

class Story extends Toloquent {
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

	public function user()
	{
		return $this->belongsTo('User', 'user_id', 'id');
	}

	public function scopeOrderDescending($query)
	{
		return $query->orderBy('created_at', 'DESC');
	}
}