@foreach ($deepLinksArray as $key => $value)
	<meta property="{{ $key }}" content="{{ $value }}" />
@endforeach