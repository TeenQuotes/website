@extends('layouts.stories')

@section('content')
	<div id="stories">
		@foreach ($stories as $story)
			<div class="story">
				<div class="row">
					<!-- Avatar -->
					<div class="column column-avatar col-xs-3 col-sm-3 col-md-2 col-lg-1">
						@if (!$story->user->isHiddenProfile())
							<a href="{{ URL::action('UsersController@show', ['id' => $story->user->login]) }}">
						@endif
							<img class="avatar img-responsive" src="{{{ $story->user->getURLAvatar() }}}"/>
						@if (!$story->user->isHiddenProfile())
							</a>
						@endif
					</div>

					<!-- Content -->
					<div class="column col-xs-9 col-sm-9 col-md-10 col-lg-11">
					<h3>
						@if (!$story->user->isHiddenProfile())
							<a href="{{ URL::action('UsersController@show', ['id' => $story->user->login]) }}">
						@endif
							{{{ $story->user->login }}}
						@if (!$story->user->isHiddenProfile())
							</a>
						@endif
					</h3>
						<h4>{{ Lang::get('stories.storiesTellTitle') }}</h4>
						{{{ $story->represent_txt }}}
						<h4>{{ Lang::get('stories.useTellTitle') }}</h4>
						{{{ $story->frequence_txt }}}
					</div>
				</div>
			</div>
		@endforeach

		{{ $stories->links() }}
	</div>
@stop