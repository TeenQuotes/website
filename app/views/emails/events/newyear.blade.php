@extends('emails.default')

@section('content')
	<img src="{{ Lang::get('email.imageNewyear') }}" id="img-event" />
	{{ Lang::get('email.hiWithLogin', array('login' => $user['login'])) }}
	<br/><br/>
	{{ Lang::get('email.newyearBody') }}
@stop