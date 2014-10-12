@extends('emails.default')

@section('content')
	{{ Lang::get('auth.heyEmail') }}<br/>
	<br/>
	{{ Lang::get('auth.askedResetPasswordEmail') }} {{ URL::route('password.reset', compact('token')) }}.
@stop