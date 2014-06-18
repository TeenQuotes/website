<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="{{ Lang::get('layout.twitterUsername') }}">
<meta name="twitter:title" content="Quote #{{ $quote->id }}">
<meta name="twitter:description" content="{{ $quote->textTwitterCard() }}">
<meta name="twitter:image:src" content="{{ Lang::get('layout.imageTwitterCard') }}">
<meta name="twitter:domain" content="{{ Config::get('app.domain') }}">
<meta name="twitter:app:name:iphone" content="{{ Lang::get('layout.nameWebsite') }}">
<meta name="twitter:app:id:iphone" content="{{ Lang::get('layout.IDAppiOS') }}">
<meta name="twitter:app:id:ipad" content="{{ Lang::get('layout.IDAppiOS') }}">