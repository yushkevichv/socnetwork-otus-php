@extends('layouts.inner')

@section('content')

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
             Ваши посты
        </h3>
    </div>

    @if(count($posts) > 0)
        <table class="w-2/3">
            <tbody class="bg-white">
            @foreach($posts as $post)
                <tr>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 w-2/3" >
                        <div class="flex justify-between ">
                            <div class="text-sm right-auto leading-5 font-medium text-gray-900">{{ $post->created_at }} </div>
                        </div>
                        <div class="text-sm pt-4 leading-5 font-medium text-gray-900">{{ $post->content }} </div>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>

    @endif

    <div class="bg-white px-4 py-3 flex items-right justify-between border-t border-gray-200 ">

        <form class="flex-auto" action="{{ route('post.store') }}" method="POST">
            @csrf

            <div class="flex w-2/3 h-48">
                <textarea id="message" name="message"  class="form-input block w-full pl-7 pr-12 border-2 border-gray-300" ></textarea>
            </div>

            <button  type="submit" class="relative mt-4 inline-flex items-center px-2 py-2 rounded-md border border-gray-300 bg-gray-800 text-sm leading-5 font-medium text-white hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150">
                Написать
            </button>

        </form>

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
