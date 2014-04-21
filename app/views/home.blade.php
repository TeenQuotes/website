<?php
$i = 0;
?>
@include('layouts/header')
<div class="container under-navbar">
	@foreach ($quotes as $quote)
		<div class="quote" style="background-color:{{$colors[$i]}};border-bottom-color:{{Quote::adjustBrightness($colors[$i], -20)}}">
			#{{ $quote->id}}
			{{ $quote->content}}
		</div>
		<?php $i++ ?>	
	@endforeach
</div>

<div class="text-center">
	{{ $quotes->links() }}
</div>

@include('layouts/footer')