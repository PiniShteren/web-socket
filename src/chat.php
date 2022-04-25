<?php
/*


{
    "type": "id",
    "id": "39fmdp%dfjkg"
    "name": "pini"
}

{
    "type": "msg",
    "to_id": "39fmdp%dfjkg",
    "from_id": "39fmdp%dfjkg",
    "msg": "ekogihepwo kgujg"
}

*/ 
namespace MyApp;

require __DIR__ . "../../vendor/autoload.php";

use ArrayObject;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{

    protected $clients;
    protected $admins;

    public function __construct()
    {
        $this->clients = new ArrayObject();
        $this->admins = new ArrayObject();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // $this->clients->attach(($conn));
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msgObg = json_decode($msg, TRUE);
        // for admins
        $count = var_dump($this->clients->count());
        if ($msgObg["type"] === "id") {
            if ($count > 0) {
                $this->clients->append(array("id" => $msgObg["id"], "connect" => $from, "name"=> $msgObg["name"]));
                $newMsg = array("name"=>$msgObg["name"], "id"=> $msgObg["id"]);
                $newMsg = json_encode($newMsg);
                foreach ($this->clients as $client) {
                    $client["connect"]->send($newMsg);
                }
            } else {
                $this->clients->append(array("id" => $msgObg["id"], "connect" => $from));
            }
        } else {
            foreach ($this->clients as $client) {
                if ($client["id"] === $msgObg["to_id"]) {
                    $client["connect"]->send($msgObg);
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        foreach ($this->clients as $index => $client) {
            if ($client["connect"] === $conn) {
                $this->clients->offsetUnset($index);
            }
            var_dump($this->clients->count());
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
