@if ($comment->isPostedBySelf())
	<!-- Edit my comment -->
	<a class="edit-comment" href="{{ URL::route('comments.edit', $comment->id) }}"><i class="fa fa-edit"></i></a>

	<!-- Delete my comment -->
	<i class="delete-comment fa fa-times" data-id="{{{ $comment->id }}}" data-url="{{ URL::route('comments.destroy', $comment->id) }}"></i>
@endif