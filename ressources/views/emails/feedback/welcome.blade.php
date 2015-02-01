@extends('emails.default')

@section('content')
	{{ Lang::get('email.hiWithLogin', compact('login')) }}
	<br/><br/>
	{{ Lang::get('feedback.welcomeContent') }}
@stop