@extends('emails/default')

@section('content')
	{{ Lang::get('email.hiWithLogin', array('login' => $quote['user']['login'])) }}
	<br/><br/>

	{{ Lang::get('quotes.quoteHasBeenRefused', array('id' => $quote['id'])) }}

	@include('emails.quotes.single')

	{{ Lang::get('quotes.quoteHasBeenRefusedEnd', array('login' => $quote['user']['login'])) }}
@stop