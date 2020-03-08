@extends('layouts.inner')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Анкета пользователя {{ $user->name }} {{ $user->last_name }}</div>

                <div class="card-body">
                    <p>Пол: {{ $user->gender }}</p>
                    <p>Город: {{ $user->city }}</p>
                    <p>Возраст: {{ $user->age }}</p>
                    <p>Интересы: {{ $user->interests }}</p>
                </div>
                <p>Вернуться <a href="{{ route('user.index') }}">к списку пользователей</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
