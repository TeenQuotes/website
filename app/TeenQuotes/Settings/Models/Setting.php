<?php

namespace TeenQuotes\Settings\Models;

use Eloquent;
use TeenQuotes\Settings\Models\Relations\SettingTrait as SettingRelationsTrait;

class Setting extends Eloquent
{
    use SettingRelationsTrait;

    protected $fillable = ['user_id', 'key', 'value'];
    public $timestamps = false;
}
