@extends('layouts/page')
<?php
$i = 0;
?>
@section('content')
	@foreach ($quotes as $quote)
		@include('layouts.singleQuote', array(compact($quote)))
	<?php $i++ ?>	
	@endforeach

	<div class="text-center">
		{{ $quotes->links() }}
	</div>
@stop