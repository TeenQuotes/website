<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | View to Bind JavaScript Vars To
    |--------------------------------------------------------------------------
    |
    | Set this value to the name of the view (or partial) that
    | you want to prepend the JavaScript variables to.
    |
    */
    'bind_js_vars_to_this_view' => 'layouts.footer',

    /*
    |--------------------------------------------------------------------------
    | JavaScript Namespace
    |--------------------------------------------------------------------------
    |
    | By default, we'll add variables to the global window object.
    | It's recommended that you change this to some namespace - anything.
    | That way, from your JS, you may do something like `Laracasts.myVar`.
    |
    */
    'js_namespace' => 'laravel',

];
