@if (Session::has('redirectedToMostCommonCountry'))
	<div class="alert alert-info no-hide">
		{{ Lang::get('search.redirectedToMostCommonCountry') }}
	</div>
@endif