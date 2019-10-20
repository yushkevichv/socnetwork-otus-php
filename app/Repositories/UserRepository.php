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

    public function getAll($perPage, $skip = null, $query = null)
    {
        $whereQuery = $this->prepareLikeCondition($query);

        // if we need to show all users without current
        // DB::select(DB::raw('select id, name, gender, city, birthday from users where id != ? '), [Auth::id()]);
        return $this->model->hydrate(
            DB::select(
                DB::raw("select id, name, last_name, gender, city, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users $whereQuery order by id asc limit $perPage, $skip ")
            )
        );
    }

    public function getAllCount($query = null)
    {
        $whereQuery = $this->prepareLikeCondition($query);

        return DB::select(
                DB::raw("select count(*) as count from users $whereQuery")
            )[0]->count;
    }

    private function prepareLikeCondition($query = null)
    {
        if($query) {
            $query = "where name like '$query%' or last_name like '$query%'";
        }

        return $query;
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
