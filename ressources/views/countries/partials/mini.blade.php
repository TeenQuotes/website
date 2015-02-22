<div class="countries__country-link">
	<a href="{{ $country->present()->searchUsers}}">
		{{{ $country->name }}}
		@include('countries.partials.flag', ['country' => $country])
	</a>
</div>