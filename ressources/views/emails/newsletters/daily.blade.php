@extends('emails.default')

@section('content')
	{{ Lang::get('email.hiWithLogin', ['login' => '*|LOGIN|*']) }}
	<br/><br/>
	{{ Lang::get('newsletters.someQuotesPublishedToday') }}
	
	@foreach ($quotes as $quote)
		@include('emails.quotes.single', compact('quote'))
	@endforeach

	{{ Lang::get('newsletters.otherQuotesToRead', ['login' => '*|LOGIN|*']) }}
@stop

<!-- Link to edit email settings -->
@include('emails.newsletters.editSettings', ['login' => '*|LOGIN|*'])