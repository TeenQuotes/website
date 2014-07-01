@extends('layouts/page')
@section('content')
	<div id="addquote-page" class="row">
		<!-- ADD A QUOTE -->
		<div class="animated fadeInLeft col-md-6">
			{{ Form::open(array('url' => URL::route('quotes.store'), 'class' => 'form-horizontal')) }}

				<h1><i class="fa fa-comment"></i>{{ Lang::get('quotes.addYourQuote') }}</h1>
				<div class="addquote-text">
					{{ Lang::get('quotes.speakYourMind') }}
				</div>

				<!-- Quote's content -->
				<div class="form-group {{{ $errors->has('content') ? 'error' : '' }}}">
					{{ Form::label('content', Lang::get('quotes.yourQuote'), array('class' => 'col-sm-2 control-label')) }}

					<div class="col-sm-10">
						{{ Form::textarea('content', Input::old('content'), array('class' => 'form-control', 'id' => 'content-quote', 'rows' => '3')) }}
						<span id="countLetters" class="orange">0 characters</span>
						@if (!empty($errors->first('content')))
							{{ TextTools::warningTextForm($errors->first('content')) }}
						@endif
						@if (!empty($errors->first('quotesSubmittedToday')))
							{{ TextTools::warningTextForm($errors->first('quotesSubmittedToday')) }}
						@endif
					</div>
				</div>

				<!-- Submit button -->
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						{{ Form::submit(Lang::get('quotes.submitMyQuote'), array('class' => 'transition btn btn-primary btn-lg', 'id' => 'submit-quote')) }}
					</div>
				</div>
			{{ Form::close() }}
		</div>

		<!-- RULES -->
		<div class="animated fadeInRight col-md-6">
			<h1><i class="fa fa-gavel"></i>{{ Lang::get('quotes.fewRules') }}</h1>
			<ul>
				{{Lang::get('quotes.rulesAddQuote')}}
			</ul>
			<div class="notice-email">
				<i class="fa fa-info"></i> {{ Lang::get('quotes.noticeByEmail') }}
			</div>
		</div>
	</div>
@stop