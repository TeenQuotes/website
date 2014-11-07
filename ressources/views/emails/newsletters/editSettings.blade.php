@section('add-footer')
	{{ Lang::get('email.manageEmailSettings', ['url' => URL::route('users.edit', $newsletter->user->login)]) }}
@stop