<?php

use TeenQuotes\Models\Relations\ProfileVisitorTrait as ProfileVisitorRelationsTrait;

class ProfileVisitor extends Eloquent {
	
	use ProfileVisitorRelationsTrait;

	protected $table = 'profile_visitors';
	protected $fillable = [];
}