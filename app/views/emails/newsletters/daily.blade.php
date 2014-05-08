@extends('emails/default')

@section('content')
	{{ Lang::get('email.hiWithLogin', array('login' => $newsletter['user']['login'])) }}
	<br/><br/>
	{{ Lang::get('newsletters.someQuotesPublishedToday') }}
	@foreach ($quotes as $quote)
		@include('emails.quotes.single', ['quote' => $quote])
	@endforeach

	{{ Lang::get('newsletters.otherQuotesToRead', array('login' => $newsletter['user']['login'])) }}
@stop

<!-- Link to edit email settings -->
@section('add-footer')
	{{ Lang::get('email.manageEmailSettings', array('url' => URL::route('users.edit', array($newsletter['user']['login']))))}}
@stop