<!-- Approve -->
<div class="col-xs-1">
	<span class="badge quote-moderation" data-id="{{{ $quote->id }}}" data-url="{{{ URL::route('admin.quotes.moderate', [$quote->id, 'approve']) }}}" data-decision="approve">
		<i class="fa fa-thumbs-up"></i>
	</span>
</div>

<!-- Unapprove -->
<div class="col-xs-1">
	<span class="badge quote-moderation" data-id="{{{ $quote->id }}}" data-url="{{{ URL::route('admin.quotes.moderate', [$quote->id, 'unapprove']) }}}" data-decision="unapprove">
		<i class="fa fa-thumbs-down"></i>
	</span>
</div>

<!-- Edit -->
<div class="col-xs-1">
	<a href="{{ URL::route('admin.quotes.edit', array($quote->id)); }}" class="badge admin__quote__edit-button">
		<i class="fa fa-pencil-square-o"></i>
	</a>
</div>

<!-- Alert sad content -->
<div class="col-xs-1">
	<span class="badge quote-moderation" data-id="{{{ $quote->id }}}" data-url="{{{ URL::route('admin.quotes.moderate', [$quote->id, 'alert']) }}}" data-decision="alert">
		<i class="fa fa-warning"></i>
	</span>
</div>