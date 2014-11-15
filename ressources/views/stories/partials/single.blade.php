<div class="story" data-id="{{{ $story->id }}}">
	<div class="row">
		<!-- Avatar -->
		@include('stories.partials.avatar')

		<!-- Content -->
		<div class="column col-xs-9 col-sm-9 col-md-10 col-lg-11">
			@include('stories.partials.author')
			
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