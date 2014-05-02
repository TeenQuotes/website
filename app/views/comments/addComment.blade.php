<div class="animated fadeInUpBig">
	{{ Form::open(array('url' => URL::route('comments.store'), 'class' => 'form-horizontal')) }}

		<!-- Comment's content -->
		<div class="form-group {{{ $errors->has('content') ? 'error' : '' }}}">
			{{ Form::label('content', Lang::get('comments.yourComment'), array('class' => 'col-sm-2 control-label')) }}

			<div class="col-sm-10">
				{{ Form::textarea('content', Input::old('content'), array('class' => 'form-control', 'id' => 'content-comment', 'rows' => '3')) }}
				<span id="countLetters" class="orange">0 characters</span>
				@if (!empty($errors->first('content')))
					{{ TextTools::warningTextForm($errors->first('content')) }}
				@endif
			</div>
		</div>

		<!-- ID quote -->
		{{ Form::hidden('quote_id', $quote->id) }}

		<!-- Submit button -->
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				{{ Form::submit(Lang::get('comments.addMyComment'), array('class' => 'transition btn btn-primary btn-lg', 'id' => 'submit-comment')) }}
			</div>
		</div>
	{{ Form::close() }}
</div>