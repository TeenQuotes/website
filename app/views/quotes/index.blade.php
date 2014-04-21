<?php
$i = 0;
?>
@include('layouts/header')
<div class="container under-navbar">
	@foreach ($quotes as $quote)
		@include('layouts.singleQuote', array('quote'=> $quote))
	<?php $i++ ?>	
	@endforeach
</div>

<div class="text-center">
	{{ $quotes->links() }}
</div>

@include('layouts/footer')