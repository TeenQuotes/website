{{ Form::model($user, array('route' => array('users.settings', $user->login), 'class' => 'form-horizontal animated fadeInUp', 'id' => 'edit-settings', 'method' => 'PUT')) }}
<h2><i class="fa fa-wrench"></i> {{ Lang::get('users.editSettingsTitle') }}</h2>

<div class="info-pre-form">
	{{ Lang::get('users.editSettingsCatchphrase') }}
</div>

<!-- Colors for published quotes -->
<div class="form-group">
	{{ Form::label('colors', Lang::get('users.colorsInput'), array('class' => 'col-sm-2 control-label')) }}

	<div class="col-sm-10">
		{{ Form::select('colors', $colorsAvailable, $selectedColor, array('class' => 'form-control')) }}
		@if (!empty($errors->first('colors')))
			{{ TextTools::warningTextForm($errors->first('colors')) }}
		@endif
	</div>
</div>

<!-- Notification comment quote -->
<div class="form-group">
	<div class="col-xs-10 col-xs-offset-2">
		{{ Form::checkbox('notification_comment_quote', "true", null, array('id' => 'notification_comment_quote')) }}
		{{ Form::label('notification_comment_quote', Lang::get('users.notificationCommentQuoteInput'), array('id' => 'notification_comment_quote')) }}
		@if (!empty($errors->first('notification_comment_quote')))
			{{ TextTools::warningTextForm($errors->first('notification_comment_quote')) }}
		@endif
	</div>
</div>

<!-- Hide profile -->
<div class="form-group">
	<div class="col-xs-10 col-xs-offset-2">
		{{ Form::checkbox('hide_profile', "true", null, array('id' => 'hide_profile')) }}
		{{ Form::label('hide_profile', Lang::get('users.hideProfileInput'), array('id' => 'hide_profile')) }}
		@if (!empty($errors->first('hide_profile')))
			{{ TextTools::warningTextForm($errors->first('hide_profile')) }}
		@endif
	</div>
</div>

<!-- Daily and weekly newsletter -->
@foreach (['daily', 'weekly'] as $newsletterType)
	<div class="form-group">
		<div class="col-xs-10 col-xs-offset-2">
			{{ Form::checkbox($newsletterType.'_newsletter', "true", ${$newsletterType."Newsletter"}, array('id' => $newsletterType.'_newsletter')) }}
			{{ Form::label($newsletterType.'_newsletter', Lang::get('users.'.$newsletterType.'NewsletterInput'), array('id' => $newsletterType.'_newsletter')) }}
			@if (!empty($errors->first($newsletterType.'_newsletter')))
				{{ TextTools::warningTextForm($errors->first($newsletterType.'_newsletter')) }}
			@endif
		</div>
	</div>
@endforeach

<!-- Submit -->
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
		{{ Form::submit(Lang::get('users.editSettingsSubmit'), array('class' => 'transition btn btn-primary btn-lg')) }}
	</div>
</div>
{{ Form::close() }}