<?php


namespace App\Repositories;


use App\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAll($perPage, $skip = null)
    {
        // if we need to show all users without current
        // DB::select(DB::raw('select id, name, gender, city, birthday from users where id != ? '), [Auth::id()]);
        return $this->model->hydrate(
            DB::select(
                DB::raw("select id, name, gender, city, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users limit $perPage, $skip")
            )
        );
    }
    public function getAllCount()
    {
        // if we need to show all users without current
        // DB::select(DB::raw('select id, name, gender, city, birthday from users where id != ? '), [Auth::id()]);
        return DB::select(
                DB::raw('select count(*) as count  from users')
            )[0]->count;
    }

    public function getById($id)
    {
        return $this->model->hydrate(
            DB::select(
                DB::raw('select id, name, last_name, gender, city, interests, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users where id = ? limit 1 '), [$id]
            )
        )->first();
    }

}
