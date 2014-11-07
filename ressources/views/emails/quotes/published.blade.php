@extends('emails.default')

@section('content')
	{{ Lang::get('email.hiWithLogin', ['login' => $quote->user->login]) }}
	<br/><br/>
	{{ Lang::get('quotes.quoteHasBeenPublished', ['id' => $quote->id, 'url' => URL::route('quotes.show', $quote->id)]) }}
@stop