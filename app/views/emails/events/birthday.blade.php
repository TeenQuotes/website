@extends('emails.default')

@section('content')
	<img src="{{ Lang::get('email.imageBirthday') }}" id="img-event" />
	{{ Lang::get('email.hiWithLogin', ['login' => $user->login]) }}
	<br/><br/>
	{{ Lang::get('email.birthdayContent', ['login' => $user->login, 'age' => $user->present()->age]) }}
@stop