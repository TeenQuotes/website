@extends('emails.default')

@section('content')
	{{ Lang::get('email.hiWithLogin', ['login' => $quote->user->login]) }}
	<br/><br/>
	{{ Lang::get('comments.commentAddedOnQuote') }}

	@include('emails.quotes.single')

	{{ Lang::get('comments.ifWantsToSeeComment', ['url' => URL::route('quotes.show', $quote->id)]) }}
@stop

<!-- Link to edit email settings -->
@section('add-footer')
	{{ Lang::get('email.manageEmailSettings', ['url' => URL::route('users.edit', $quote->user->login)]) }}
@stop