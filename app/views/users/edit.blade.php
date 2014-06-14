@extends('layouts/page')
@section('content')
<div id="editprofile-page">
	@include('users.profile.editInfo')

	@include('users.profile.editPassword')

	@include('users.profile.editSettings')
	
	@include('users.profile.delete')
</div>
@stop