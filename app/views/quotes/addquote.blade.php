@extends('layouts/page')
@section('content')
	<div id="addquote-page" class="row">
		<!-- ADD A QUOTE -->
		<div class="animated fadeInLeft col-md-6">
			{{ Form::open(array('url' => URL::route('quotes.store'), 'class' => 'form-horizontal')) }}

				<h1><i class="fa fa-comment"></i><span class="red">{{ Lang::get('quotes.addYourQuote')}}</span></h1>
				<div class="addquote-text">
					{{ Lang::get('quotes.speakYourMind') }}
				</div>

				<!-- Quote's content -->
				<div class="form-group {{{ $errors->has('quote') ? 'error' : '' }}}">
					{{ Form::label('quote', Lang::get('quotes.yourQuote'), array('class' => 'col-sm-2 control-label')) }}

					<div class="col-sm-10">
						{{ Form::textarea('quote', Input::old('quote'), array('class' => 'form-control', 'id' => 'content-quote')) }}
						<span id="countLetters" class="orange">0 characters</span>
						@if (!empty($errors->first('quote')))
							{{ TextTools::warningTextForm($errors->first('quote')) }}
						@endif
					</div>
				</div>

				<!-- Submit button -->
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						{{ Form::submit(Lang::get('quotes.submitMyQuote'), array('class' => 'transition btn btn-primary btn-lg', 'id' => 'submit-form')) }}
					</div>
				</div>
			{{ Form::close() }}
		</div>

		<!-- RULES -->
		<div class="animated fadeInRight col-md-6">
			<h1><i class="fa fa-gavel"></i><span class="red">{{ Lang::get('quotes.fewRules') }}</span></h1>
			<ul>
				{{Lang::get('quotes.rulesAddQuote')}}
			</ul>
			<span class="notice-email"><i class="fa fa-info"></i>{{ Lang::get('quotes.noticeByEmail') }}</span>
		</div>
	</div>
@stop