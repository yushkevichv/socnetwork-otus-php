@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Список пользователей</div>

                <div class="card-body">
                    <p>Всего пользователей: {{ count($users) }} </p>

                    @if(count($users) > 0)
                        <table class="table">
                            <tr>
                                <th>Имя</th>
                                <th>Пол</th>
                                <th>Город</th>
                                <th>Возраст</th>
                                <th></th>
                            </tr>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->gender }}</td>
                                <td>{{ $user->city }}</td>
                                <td>{{ $user->age }}</td>
                                <td><a href="{{route('user.show', ['id' => $user->id])}}">Посмотреть</a> </td>
                            </tr>
                        @endforeach
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection