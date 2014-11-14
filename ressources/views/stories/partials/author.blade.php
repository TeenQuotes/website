<h3>
	@if ( ! $story->user->isHiddenProfile())
		<a href="{{ URL::route('users.show', ['id' => $story->user->login]) }}">
	@endif
		{{{ $story->user->login }}}
	@if ( ! $story->user->isHiddenProfile())
		</a>
	@endif
	<span class="story-id">
		<a href="{{URL::route('story.show', $story->id)}}">
		#{{$story->id}}
		</a>
	</span>
</h3>