@extends('layouts/page')
<?php
$i = 0;
?>
@section('content')
	<div id="user-profile">
		@if ($type == 'favorites')
			<h2><i class="fa fa-heart"></i> {{ Lang::get('users.favoriteQuotes') }}</h2>
		@else
			<h2><i class="fa fa-pencil"></i> {{ Lang::get('users.publishedQuotes') }}</h2>
		@endif

		@foreach ($quotes as $quote)
			@include('quotes.singleQuote', compact($quote))
		<?php $i++ ?>	
		@endforeach

		<div class="text-center">
			{{ $paginator->links() }}
		</div>
	</div>
@stop