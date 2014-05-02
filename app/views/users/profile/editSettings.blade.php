{{ Form::model($user, array('route' => array('users.settings', $user->id), 'class' => 'form-horizontal animated fadeInUp', 'id' => 'edit-settings', 'method' => 'PUT')) }}
<h2><i class="fa fa-wrench"></i> {{ Lang::get('users.editSettingsTitle') }}</h2>

<div class="info-pre-form">
	{{ Lang::get('users.editSettingsCatchphrase') }}
</div>

<!-- Notification comment quote -->
<div class="form-group">
	<div class="col-xs-10 col-xs-offset-2">
		{{ Form::checkbox('notification_comment_quote', "on", null, array('id' => 'notification_comment_quote')) }}
		{{ Form::label('notification_comment_quote', Lang::get('users.notificationCommentQuoteInput'), array('id' => 'notification_comment_quote')) }}
		@if (!empty($errors->first('notification_comment_quote')))
			{{ TextTools::warningTextForm($errors->first('notification_comment_quote')) }}
		@endif
	</div>
</div>

<!-- Hide profile -->
<div class="form-group">
	<div class="col-xs-10 col-xs-offset-2">
		{{ Form::checkbox('hide_profile', "on", null, array('id' => 'hide_profile')) }}
		{{ Form::label('hide_profile', Lang::get('users.hideProfileInput'), array('id' => 'hide_profile')) }}
		@if (!empty($errors->first('hide_profile')))
			{{ TextTools::warningTextForm($errors->first('hide_profile')) }}
		@endif
	</div>
</div>

<!-- Weekly newsletter -->
<div class="form-group">
	<div class="col-xs-10 col-xs-offset-2">
		{{ Form::checkbox('weekly_newsletter', "on", $weeklyNewsletter, array('id' => 'weekly_newsletter')) }}
		{{ Form::label('weekly_newsletter', Lang::get('users.weeklyNewsletterInput'), array('id' => 'weekly_newsletter')) }}
		@if (!empty($errors->first('weekly_newsletter')))
			{{ TextTools::warningTextForm($errors->first('weekly_newsletter')) }}
		@endif
	</div>
</div>

<!-- Submit -->
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
		{{ Form::submit(Lang::get('users.editSettingsSubmit'), array('class' => 'transition btn btn-primary btn-lg')) }}
	</div>
</div>
{{ Form::close() }}