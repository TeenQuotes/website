@foreach ($quotes as $quote)
	@include('emails.quotes.single', compact('quote'))
@endforeach
