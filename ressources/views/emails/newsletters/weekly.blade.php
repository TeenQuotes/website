@extends('emails.default')

@section('content')
	{{ Lang::get('email.hiWithLogin', ['login' => $newsletter->user->login]) }}
	<br/><br/>
	{{ Lang::get('newsletters.beenWaitingForLong') }}
	
	@foreach ($quotes as $quote)
		@include('emails.quotes.single', compact('quote'))
	@endforeach

	{{ Lang::get('newsletters.callToVisitWebsite') }}
@stop

<!-- Link to edit email settings -->
@include('emails.newsletters.editSettings', ['login' => $newsletter->user->login])