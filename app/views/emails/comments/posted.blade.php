@extends('emails/default')

@section('content')
	{{ Lang::get('email.hiWithLogin', array('login' => $quote['user']['login'])) }}
	<br/><br/>
	{{ Lang::get('comments.commentAddedOnQuote') }}

	@include('emails.quotes.single')

	{{ Lang::get('comments.ifWantsToSeeComment', array('url' => URL::route('quotes.show', array($quote['id'])))) }}
@stop

<!-- Link to edit email settings -->
@section('add-footer')
	{{ Lang::get('email.manageEmailSettings', array('url' => URL::route('users.edit', array($quote['user']['login']))))}}
@stop