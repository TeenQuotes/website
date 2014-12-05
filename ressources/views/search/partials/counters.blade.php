@if ($nbQuotes > 0 AND ! is_null($users) AND $nbUsers > 0)
	<div id="result-info" class="row">
		@foreach (['quotes', 'users'] as $element)
			<?php $count = 'nb'.ucfirst($element); ?>
			<div class="col-md-6 counter">
				<div class="content" data-scroll="{{ $element }}">
					@if ($element == 'quotes')
						<i class="fa fa-comment"></i>
					@else
						<i class="fa fa-users"></i>
					@endif
					{{ Lang::get('search.'.$element.'Result') }}
					<span class="count">
						{{ $$count }} {{ Lang::choice('search.result', $$count)}}
					</span>
				</div>
			</div>
		@endforeach
	</div>
@endif