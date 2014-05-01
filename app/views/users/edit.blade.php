@extends('layouts/page')
@section('content')
<div id="editprofile-page">
	@include('users.profile.editInfo')

	@include('users.profile.editPassword')
</div>
@stop