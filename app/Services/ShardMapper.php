<?php


namespace App\Services;


use App\Models\Bucket;
use App\Models\Shard;
use Illuminate\Support\Facades\DB;

class ShardMapper
{
    public function getShardForMessages($userId)  :Shard {
        $bucketId = $this->getBucketId('messages', $userId);
        $shardId = DB::table('table_buckets_to_shards')->select('shard_id')->where('bucket_id', $bucketId)->first()->shard_id;
        $shard = Shard::findOrFail($shardId);

        return $shard;
    }

    public function getBucketId($type, $userId)
    {
        switch ($type) {
            case 'messages' :
                $bucket = $this->getBucketForMessages($userId);
            break;
        }


        return $bucket;
    }


    private function getBucketForMessages($userId)
    {
        if(is_null($userId))
        {
            throw new \Exception('invalid user id');
        }

        // some logic for mapping messages and buckets
        $bucket = Bucket::query()->select('id')->get();
        $bucketCount = $bucket->count();

        $bucketId = $bucket->toArray()[$userId % $bucketCount];

        return $bucketId;
    }

}