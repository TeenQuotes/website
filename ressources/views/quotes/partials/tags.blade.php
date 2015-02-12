@if (! empty($tagsName))

	<div class="quotes__tags-container">
		@foreach($tagsName as $tagKey => $tagName)
			<a class="quotes__tags-tag" href="{{ route('quotes.tags.index', $tagKey) }}">
				<span class="quotes__tag-hashtag">#</span>{{ $tagName }}
			</a>
		@endforeach
	</div>

@endif