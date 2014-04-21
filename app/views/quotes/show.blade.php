<?php
$i = rand(0, count($colors) - 1);
?>
@include('layouts/header')

<div class="container under-navbar">
		@include('layouts.singleQuote', array('quote' => $quote))
</div>

@include('layouts/footer')