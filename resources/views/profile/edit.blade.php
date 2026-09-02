@extends('layouts.app')

@section('content')
    <div class="card">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="card">
        @include('profile.partials.update-password-form')
    </div>

    <div class="card">
        @include('profile.partials.addresses')
    </div>

    <div class="card">
        @include('profile.partials.delete-user-form')
    </div>
@endsection
