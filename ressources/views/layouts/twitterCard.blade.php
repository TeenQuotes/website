<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="{{ Lang::get('layout.twitterUsername') }}">
<meta name="twitter:title" content="Quote #{{ $quote->id }}">
<meta name="twitter:description" content="{{ $quote->present()->textTwitterCard }}">
<meta name="twitter:image:src" content="{{ Lang::get('layout.imageTwitterCard') }}">
<meta name="twitter:url" content="{{ URL::current() }}">