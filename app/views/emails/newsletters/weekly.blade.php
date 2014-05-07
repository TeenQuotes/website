@extends('emails/default')

@section('content')
	{{ Lang::get('email.hiWithLogin', array('login' => $newsletter['user']['login'])) }}
	<br/><br/>
	{{ Lang::get('newsletters.beenWaitingForLong') }}
	@foreach ($quotes as $quote)
		@include('emails.quotes.single', ['quote' => $quote])
	@endforeach

	{{ Lang::get('newsletters.callToVisitWebsite') }}
@stop

<!-- Link to edit email settings -->
@section('add-footer')
	{{ Lang::get('email.manageEmailSettings', array('url' => URL::route('users.edit', array($newsletter['user']['login']))))}}
@stop