@extends('emails.default')

@section('content')
	{{ Lang::get('email.hiWithLogin', ['login' => $quote->user->login]) }}
	<br/><br/>

	{{ Lang::get('quotes.quoteHasBeenApprovedStart', ['id' => $quote->id]) }}
	{{ Lang::choice('quotes.nbDays', $nbDays, ['nb' => $nbDays]).Lang::get('quotes.quoteHasBeenApprovedEnd') }}

	@include('emails.quotes.single')

	{{ Lang::get('quotes.quoteHasBeenApprovedFinal') }}
@stop