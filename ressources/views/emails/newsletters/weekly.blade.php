@extends('emails.default')

@section('content')
	{{ Lang::get('email.hiWithLogin', ['login' => '*|LOGIN|*']) }}
	<br/><br/>
	{{ Lang::get('newsletters.beenWaitingForLong') }}

	@include('emails.quotes.multiple', compact('quotes'))

	{{ Lang::get('newsletters.callToVisitWebsite') }}
@stop

<!-- Link to edit email settings -->
@include('emails.newsletters.editSettings', ['login' => '*|LOGIN|*'])
