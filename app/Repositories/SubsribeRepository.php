<?php


namespace App\Repositories;


use Illuminate\Support\Facades\DB;

class SubsribeRepository
{

    public function subscribe($author, $userId): void
    {
        DB::raw("INSERT IGNORE into subscribers ('author_id', 'user_id') VALUES (?, ?)", [$author, $userId]);
    }

    public function unsubscribe($author, $userId) :void
    {
        DB::table('subscribers')
            ->where('author_id', $author)
            ->where('user_id', $userId)
            ->delete();
    }

}