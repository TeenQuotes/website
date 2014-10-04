@extends('layouts.page')
<?php
$i = 0;
?>
@section('content')
	@foreach ($quotes as $quote)
		@include('quotes.singleQuote', compact("quote"))
	<?php $i++ ?>
	@endforeach
	
	<div id="ad-footer">
		@include('layouts.pub-footer')
	</div>
	
	<div id="paginator-quotes" class="text-center">
		{{ $paginator->links() }}
	</div>
@stop