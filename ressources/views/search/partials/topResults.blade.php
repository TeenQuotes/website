@if ($$count > $maxNbResultPerCategory)
	<div class="alert alert-info no-hide">
		{{ Lang::get('search.showingTopResults', ['nb' => $maxNbResultPerCategory]) }}
	</div>
@endif