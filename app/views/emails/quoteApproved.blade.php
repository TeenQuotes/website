@extends('emails/default')

@section('content')
	{{ Lang::get('email.hiWithLogin', array('login' => $quote['user']['login'])) }}
	<br/><br/>
	{{ Lang::get('quotes.quoteHasBeenApproved', array('id' => $quote['id'], 'url' => URL::route('quotes.show', array($quote['id'])))) }}
@stop