@extends('emails.default')

@section('content')
	{{ Lang::get('email.hiWithLogin', ['login' => $quote->user->login]) }}
	<br/><br/>

	{{ Lang::get('quotes.quoteHasBeenRefused', ['id' => $quote->id]) }}

	@include('emails.quotes.single')

	{{ Lang::get('quotes.quoteHasBeenRefusedAlertSad', ['urlHelp' => Config::get('app.quotes.moderationURLHelp')]) }}
@stop