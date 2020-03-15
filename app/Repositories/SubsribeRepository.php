<?php


namespace App\Repositories;


use Illuminate\Support\Facades\DB;

class SubsribeRepository
{

    public function subscribe($userId, $author): void
    {
        DB::statement("INSERT IGNORE into subscribers (`author_id`, `user_id`) VALUES (?, ?)", [$author, $userId]);
    }

    public function unsubscribe($userId, $author) :void
    {
        DB::table('subscribers')
            ->where('author_id', $author)
            ->where('user_id', $userId)
            ->delete();
    }

}