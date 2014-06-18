{{ Form::open(array('url' => URL::route('story.store'), 'class' => 'form-horizontal')) }}

	<h1><i class="fa fa-book"></i>{{ Lang::get('stories.addStory')}}</h1>

	<!-- represent_txt -->
	<div class="form-group">
		{{ Form::label('represent_txt', Lang::get('stories.inputRepresent'), array('class' => 'col-sm-2 control-label')) }}

		<div class="col-sm-10">
			{{ Form::textarea('represent_txt', Input::old('represent_txt'), array('class' => 'form-control', 'rows' => '3', 'placeholder' => Lang::get('stories.placeholderRepresent'))) }}
			@if (!empty($errors->first('represent_txt')))
				{{ TextTools::warningTextForm($errors->first('represent_txt')) }}
			@endif
		</div>
	</div>

	<!-- frequence_txt -->
	<div class="form-group">
		{{ Form::label('frequence_txt', Lang::get('stories.inputFrequence'), array('class' => 'col-sm-2 control-label')) }}

		<div class="col-sm-10">
			{{ Form::textarea('frequence_txt', Input::old('frequence_txt'), array('class' => 'form-control', 'rows' => '3', 'placeholder' => Lang::get('stories.placeholderFrequence'))) }}
			@if (!empty($errors->first('frequence_txt')))
				{{ TextTools::warningTextForm($errors->first('frequence_txt')) }}
			@endif
		</div>
	</div>

	<!-- Submit button -->
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			{{ Form::submit(Lang::get('stories.submitStory'), array('class' => 'transition btn btn-primary btn-lg')) }}
		</div>
	</div>
{{ Form::close() }}