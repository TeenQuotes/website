@extends('emails.default')

@section('content')
	<img src="{{ Lang::get('email.imageChristmas') }}" id="img-event" />
	{{ Lang::get('email.hiWithLogin', ['login' => $user->login]) }}
	<br/><br/>
	{{ Lang::get('email.christmasBody') }}
@stop