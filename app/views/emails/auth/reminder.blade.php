@extends('emails/default')

@section('content')
	{{ Lang::get('auth.heyEmail') }}<br/>
	<br/>
	{{ Lang::get('auth.askedResetPasswordEmail') }} {{ URL::to('password/reset', array($token)) }}.
@stop