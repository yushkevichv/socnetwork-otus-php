@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row ">
        <div class="col-md-8 offset-2">
            <form method="get" class="form-inline" action="{{ route('user.index') }}" >
                <div class="form-group mb-2 mx-sm-3">
                    <input type="text" name="q" class="form-control" value="{{ $query }}" placeholder="Поиск">
                </div>

                <input type="submit" class="btn btn-primary mb-2" value="Искать">
            </form>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Список пользователей</div>

                <div class="card-body">
{{--                    <p>Всего пользователей: {{ $usersCount }} </p>--}}

                    @if(count($users) > 0)
                        <table class="table">
                            <tr>
                                <th>Имя</th>
                                <th>Фамилия</th>
                                <th>Пол</th>
                                <th>Город</th>
                                <th>Возраст</th>
                                <th></th>
                            </tr>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->gender }}</td>
                                <td>{{ $user->city }}</td>
                                <td>{{ $user->age }}</td>
                                <td><a href="{{route('user.show', ['id' => $user->id])}}">Посмотреть</a> </td>
                            </tr>
                        @endforeach
                        </table>
                    @endif

                    <p>
                        Текущая страница: {{ $page }} <br>
                        @if($page != 1)
                             <a href="{{route('user.index')}}">В начало</a> |
                        @endif
                        @if($isMoreExist && (count($users) > 0))
                             <a href="{{route('user.index', ['page' => $page+1])}}">Следующая страница</a>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
