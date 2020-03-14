<?php

use Illuminate\Database\Seeder;

class ShardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('buckets')->truncate();
        \Illuminate\Support\Facades\DB::table('shards')->truncate();
        \Illuminate\Support\Facades\DB::table('table_buckets_to_shards')->truncate();

        $bucket1 = \App\Models\Bucket::create(
            [
                'meta' => 'first'
            ]
        );
        $bucket2 = \App\Models\Bucket::create(
            [
                'meta' => 'second'
            ]
        );

        $shard1 = \App\Models\Shard::create(
            [
                'name' => 'shard_1',
                'host' => 'mysql1',
                'username' => 'default',
                'db_name' => 'shard1',
                'password' => 'secret',
            ]
        );

        $shard2 = \App\Models\Shard::create(
            [
                'name' => 'shard_2',
                'host' => 'mysql2',
                'username' => 'default',
                'db_name' => 'shard',
                'password' => 'secret',
            ]
        );

        \Illuminate\Support\Facades\DB::table('table_buckets_to_shards')->insert(
            [
                [
                    'bucket_id' => $bucket1->id,
                    'shard_id' => $shard1->id
                ],
                [
                    'bucket_id' => $bucket2->id,
                    'shard_id' => $shard2->id
                ]
            ]
        );
    }
}
