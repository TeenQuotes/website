@section('add-footer')
	{{ Lang::get('email.manageEmailSettings', ['url' => URL::route('users.edit', $login)]) }}
@stop