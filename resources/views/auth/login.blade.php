@extends('layouts.guest')
@section('content')<h2 class="h5 mb-4 text-center">Admin login</h2><form method="post" action="{{ route('login.store') }}">@csrf<div class="mb-3"><label class="form-label" for="email">Email</label><input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus></div><div class="mb-3"><label class="form-label" for="password">Password</label><input id="password" class="form-control" type="password" name="password" required></div><button class="btn btn-primary w-100">Sign in</button></form>@endsection

