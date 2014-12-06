@extends('emails.default')

@section('content')
	{{ Lang::get('email.hiWithLogin', ['login' => $user->login]) }}
	<br/><br/>
	{{ Lang::get('newsletters.unsubscribeBecauseInactive') }}
@stop

<!-- Link to edit email settings -->
@include('emails.newsletters.editSettings', ['login' => $user->login])