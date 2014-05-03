@extends('layouts/page')
<?php
$i = 0;
?>
@section('content')
	<div id="admin-page">
		<h2><span id="nb-quotes-waiting">{{ count($quotes) }}</span> Waiting quotes</h2>
		@foreach ($quotes as $quote)
			@include('quotes.singleQuoteAdmin', compact($quote))
		<?php
		if ($i == (count($colors) - 1))
			$i = 0;
		else
			$i++
		?>
		@endforeach
	</div>
@stop