@extends('layouts.inner')

@section('content')

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Переписка с пользователем {{ $chatUser->name }} {{ $chatUser->last_name }}
        </h3>
    </div>

    @if(count($messages) > 0)
        <table class="min-w-full">
            <thead>
            <tr>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Имя
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Фамилия
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Пол
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Город
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Возраст
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
            </tr>
            </thead>
            <tbody class="bg-white">
{{--            @foreach($users as $user)--}}
{{--                <tr>--}}
{{--                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">--}}
{{--                        <div class="flex items-center">--}}
{{--                            <div class="ml-4">--}}
{{--                                <div class="text-sm leading-5 font-medium text-gray-900">{{ $user->name }}</div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </td>--}}
{{--                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">--}}
{{--                        <div class="text-sm leading-5 text-gray-900">{{ $user->last_name }}</div>--}}
{{--                    </td>--}}

{{--                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">--}}
{{--                        {{ $user->gender }}--}}
{{--                    </td>--}}
{{--                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">--}}
{{--                        {{ $user->city }}--}}
{{--                    </td>--}}
{{--                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">--}}
{{--                        {{ $user->age }}--}}
{{--                    </td>--}}
{{--                    <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">--}}
{{--                        <a href="{{route('user.show', ['id' => $user->id])}}" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Посмотреть</a>--}}
{{--                    </td>--}}
{{--                    <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">--}}
{{--                        <a href="{{route('chat.store', ['to' => $user->id])}}" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Написать</a>--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--            @endforeach--}}

            </tbody>
        </table>

    @endif

    <div class="bg-white px-4 py-3 flex items-right justify-between border-t border-gray-200 ">

        <div class="flex w-2/3 h-48">
            <textarea id="message" class="form-input block w-full pl-7 pr-12 border-2 border-gray-300" >
            </textarea>
        </div>

    </div>
    <div class="flex px-3 mb-8 w-2/3 items-right">
        <a  href="{{route('user.index')}}" class="relative mt-4 inline-flex items-center px-2 py-2 rounded-md border border-gray-300 bg-gray-800 text-sm leading-5 font-medium text-white hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150">
            Написать
        </a>
    </div>
</div>

<div class="bg-white px-4 py-3 mt-3 flex items-center justify-between border-t border-gray-200">
    <div class="flex-1 flex justify-between">
        <a href="{{ route('user.index') }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
            Вернуться к списку пользователей
        </a>
    </div>
</div>

@endsection
