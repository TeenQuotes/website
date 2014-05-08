@section('add-footer')
	{{ Lang::get('email.manageEmailSettings', array('url' => URL::route('users.edit', array($newsletter['user']['login']))))}}
@stop