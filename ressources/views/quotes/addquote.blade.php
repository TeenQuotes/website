@extends('layouts.page')

@section('content')
	<div id="addquote-page" class="row">

		<!-- RULES -->
		<div class="col-sm-12 col-md-offset-2 col-md-8">
			<h1><i class="fa fa-gavel"></i>{{ trans('quotes.fewRules') }}</h1>
			<ul>
				{{trans('quotes.rulesAddQuote')}}
			</ul>
		</div>

		<!-- ADD A QUOTE -->
		<div class="animated fadeInUp col-sm-12 col-md-offset-2 col-md-8 addquote__form">
			{{ Form::open(['url' => URL::route('quotes.store'), 'class' => 'form-horizontal']) }}

				<h1><i class="fa fa-comment"></i>{{ trans('quotes.addYourQuote') }}</h1>
				<div class="addquote-text">
					{{ trans('quotes.speakYourMind') }}
				</div>

				<!-- Quote's content -->
				<div class="form-group {{{ $errors->has('content') ? 'error' : '' }}}">
					{{ Form::label('content', trans('quotes.yourQuote'), ['class' => 'col-sm-2 control-label']) }}

					<div class="col-sm-10">
						{{ Form::textarea('content', Input::old('content'), ['class' => 'form-control', 'id' => 'content-quote', 'rows' => '3', 'placeholder' => trans('quotes.readyToInspire')]) }}
						<span id="countLetters" class="orange">0 characters</span>
						@if ( ! empty($errors->first('content')))
							{{ TextTools::warningTextForm($errors->first('content')) }}
						@endif
						@if ( ! empty($errors->first('quotesSubmittedToday')))
							{{ TextTools::warningTextForm($errors->first('quotesSubmittedToday')) }}
						@endif
					</div>
				</div>

				<!-- Submit button -->
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						{{ Form::submit(trans('quotes.submitMyQuote'), ['class' => 'transition btn btn-primary btn-lg', 'id' => 'submit-quote']) }}
					</div>
				</div>
			{{ Form::close() }}
			<div class="notice-email">
				<i class="fa fa-info"></i> {{ trans('quotes.noticeByEmail') }}
			</div>
		</div>
	</div>
@stop

<!-- Send event to Google Analytics -->
@section('add-js')
	@include('js.sendEvent')
@stop
