@extends('emails/default')

@section('content')
	{{ Lang::get('auth.heyEmail') }}<br/>
	<br/>
	{{ Lang::get('auth.askedResetPasswordEmail') }} {{ URL::action('RemindersController@getReset', compact('token')) }}.
@stop