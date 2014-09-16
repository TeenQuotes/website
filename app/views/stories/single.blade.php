<div class="story">
	<div class="row">
		<!-- Avatar -->
		<div class="column column-avatar col-xs-3 col-sm-3 col-md-2 col-lg-1">
			@if (!$story->user->isHiddenProfile())
				<a href="{{ URL::action('UsersController@show', ['id' => $story->user->login]) }}">
			@endif
				<img class="avatar img-responsive" src="{{{ $story->user->present()->avatarLink }}}"/>
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
			<span class="story-id">
				<a href="{{URL::route('story.show', $story->id)}}">
				#{{$story->id}}
				</a>
			</span>
		</h3>
			<h4>{{ Lang::get('stories.storiesTellTitle') }}</h4>
			{{{ $story->represent_txt }}}
			<h4>{{ Lang::get('stories.useTellTitle') }}</h4>
			{{{ $story->frequence_txt }}}
			
			<div class="story-date">
				{{ $story->present()->storyAge }}
			</div>
		</div>
	</div>
</div>