{{ Form::model($user, ['route' => ['users.update', $user->login], 'class' => 'form-horizontal animated fadeInLeft', 'id' => 'edit-info', 'method' => 'PUT', 'files' => true]) }}
	<h1><i class="fa fa-edit"></i> {{ Lang::get('users.editProfileTitle') }}</h1>

	<div class="row info-pre-form">
		<div class="col-xs-9">
			{{ Lang::get('users.inputsOptionalInfo') }}
		</div>
		<div class="column-avatar col-xs-3">
			<img class="avatar img-responsive" src="{{{ $user->present()->avatarLink.'?'.rand(1, 32000) }}}"/>
		</div>
	</div>

	<div class="alert alert-info no-hide alert-dismissable" id="alert-change-avatar">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		{{ Lang::get('users.browserCantChangeAvatar')}}
	</div>

	<!-- Gender -->
	<div class="form-group">
	{{ Form::label('gender', Lang::get('users.genderLabel'), ['class' => 'col-sm-2 control-label']) }}

		<div class="col-sm-10">
			<div class="register-switch">
				<input type="radio" name="gender" value="F" id="gender_f" class="register-switch-input" {{{ ($gender == 'M') ? '' : 'checked'}}}>
				<label for="gender_f" class="register-switch-label"><i class="fa fa-female "></i> {{ Lang::get('users.femaleLabel') }}</label>
				<input type="radio" name="gender" value="M" id="gender_m" class="register-switch-input" {{{ ($gender == 'M') ? 'checked' : ''}}}>
				<label for="gender_m" class="register-switch-label"><i class="fa fa-male "></i> {{ Lang::get('users.maleLabel') }}</label>
			</div>
			@if ( ! empty($errors->first('gender')))
				{{ TextTools::warningTextForm($errors->first('gender')) }}
			@endif
		</div>
	</div>

	<!-- Birthdate -->
	<div class="form-group">
		{{ Form::label('birthdate', Lang::get('users.birthdateInput'), ['class' => 'col-sm-2 control-label']) }}

		<div class="col-sm-10">
			{{ Form::text('birthdate', Input::old('birthdate'), ['class' => 'form-control', 'placeholder' => Lang::get('users.dateFormatInput')]) }}
			<div class="input-hint">
				{{ Lang::get('users.hintBirthdate') }}
			</div>
			@if ( ! empty($errors->first('birthdate')))
				{{ TextTools::warningTextForm($errors->first('birthdate')) }}
			@endif
		</div>
	</div>

	<!-- Country -->
	<div class="form-group">
		{{ Form::label('country', Lang::get('users.countryInput'), ['class' => 'col-sm-2 control-label']) }}

		<div class="col-sm-10">
			{{ Form::select('country', $listCountries, $selectedCountry, ['class' => 'form-control']) }}
			@if ( ! empty($errors->first('country')))
				{{ TextTools::warningTextForm($errors->first('country')) }}
			@endif
		</div>
	</div>

	<!-- City -->
	<div class="form-group">
		{{ Form::label('city', Lang::get('users.cityInput'), ['class' => 'col-sm-2 control-label']) }}

		<div class="col-sm-10">
			{{ Form::text('city', $selectedCity, ['class' => 'form-control', 'placeholder' => Lang::get('users.cityPlaceholder')]) }}
			@if ( ! empty($errors->first('city')))
				{{ TextTools::warningTextForm($errors->first('city')) }}
			@endif
		</div>
	</div>

	<!-- Avatar -->
	<div class="form-group" id="change-avatar">
		{{ Form::label('avatar', Lang::get('users.yourAvatarInput'), ['class' => 'col-sm-2 control-label']) }}

		<div class="col-sm-10">
			{{ Form::file('avatar', ['class' => 'form-control']) }}
			<div class="input-hint">
				{{ Lang::get('users.hintAvatar') }}
			</div>
			@if ( ! empty($errors->first('avatar')))
				{{ TextTools::warningTextForm($errors->first('avatar')) }}
			@endif
		</div>
	</div>

	<!-- About me -->
	<div class="form-group">
		{{ Form::label('about_me', Lang::get('users.aboutMeInput'), ['class' => 'col-sm-2 control-label']) }}

		<div class="col-sm-10">
			{{ Form::textarea('about_me', Input::old('about_me'), ['class' => 'form-control', 'rows' => '3', 'placeholder' => Lang::get('users.aboutMePlacehoolder')]) }}
			@if ( ! empty($errors->first('about_me')))
				{{ TextTools::warningTextForm($errors->first('about_me')) }}
			@endif
		</div>
	</div>

	<!-- Submit button -->
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			{{ Form::submit(Lang::get('users.editProfileSubmit'), ['class' => 'transition animated fadeInUp btn btn-primary btn-lg']) }}
		</div>
	</div>
{{ Form::close() }}