<meta name="twitter:card" content="summary">
<meta name="twitter:description" content="{{ $quote->present()->textTwitterCard }}">
<meta name="twitter:image:height" content="{{ Lang::get('layout.cardTwitterHeight') }}">
<meta name="twitter:image:src" content="{{ Lang::get('layout.cardTwitterURL') }}">
<meta name="twitter:image:width" content="{{ Lang::get('layout.cardTwitterWidth') }}">
<meta name="twitter:site" content="{{ Lang::get('layout.twitterUsername') }}">
<meta name="twitter:title" content="Quote #{{ $quote->id }}">
<meta name="twitter:url" content="{{ URL::current() }}">