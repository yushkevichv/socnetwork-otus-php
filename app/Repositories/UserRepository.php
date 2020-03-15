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
        if($query) {
            $dbQuery = "
            select * from 
            (
                select * from 
                (
                    select id, name, last_name, gender, city, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users ".$this->prepareLikeNameCondition($query)." order by id asc limit  $skip, $perPage
                ) t1
                union 
                select * from 
                (
                    select id, name, last_name, gender, city, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users ".$this->prepareLikeLastNameCondition($query)." order by id asc limit $skip, $perPage
                ) t2
               
            ) tbl order by id asc limit  $skip, $perPage";
        }
        else {
            $dbQuery = "select id, name, last_name, gender, city, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users order by id asc limit  $skip, $perPage ";
        }


        // if we need to show all users without current
        // DB::select(DB::raw('select id, name, gender, city, birthday from users where id != ? '), [Auth::id()]);
        return
            $this->model->hydrate(
                DB::select(
                    DB::raw($dbQuery)
                )
            );
    }

    public function getFollowing($id) :array
    {
        return DB::table('subscribers')
            ->select('author_id as following_id')
            ->where('user_id', $id)
            ->get()
            ->pluck('following_id')
            ->toArray();
    }

    public function getAllCount($query = null)
    {
        $whereQuery = $this->prepareLikeCondition($query);

        return DB::select(
                DB::raw("select count(id) as count from users $whereQuery")
            )[0]->count;
    }

    private function prepareLikeNameCondition($query = null)
    {
        if($query) {
            $query = "where name like '$query%'";
        }

        return $query;
    }

    private function prepareLikeLastNameCondition($query = null)
    {
        if($query) {
            $query = "where last_name like '$query%'";
        }

        return $query;
    }

    public function getById($id)
    {
        return $this->model->hydrate(
            DB::select(
                DB::raw('select id, name, last_name, gender, city, interests, email, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users where id = ? limit 1 '), [$id]
            )
        )->first();
    }

}
