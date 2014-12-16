<?php

namespace TailoredTunes\MongoUserSessionDao;
use TailoredTunes\MongoConnectionAdapter\MongoDbConnection;
use TailoredTunes\UserSession\Dao\SessionDao;

class MongoSessionDao implements SessionDao
{

    private $maxTime = 10080;

    /**
     * @var MongoDbConnection
     */
    private $connection;

    public function __construct(MongoDbConnection $connection)
    {

        $this->connection = $connection;
    }

    public function open()
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $doc = $this->connection->sessions->findOne(["_id" => $id], ["sessionData" => 1]);
        return $doc['sessionData'];
    }

    public function write($id, $data)
    {
        $this->connection->sessions->save(["_id" => $id, "sessionData" => $data, "timeStamp" => time()]);
        return true;
    }

    public function destroy($id)
    {
        $this->connection->sessions->remove(["_id" => $id]);
        return true;
    }

    public function gc()
    {
        $agedTime = time() - $this->maxTime;
        $this->connection->sessions->remove(["timeStamp" => ['$lt' => $agedTime]]);
    }
}
