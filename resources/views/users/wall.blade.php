@extends('layouts.inner')

@section('content')

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Стена
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
    @else
        Пока ниикто не написал
    @endif


</div>

@endsection
