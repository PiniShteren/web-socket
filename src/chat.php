<?php

namespace MyApp;

require __DIR__ . "../../vendor/autoload.php";

use ArrayObject;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new ArrayObject();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // echo $conn->resourceId;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msgObg = json_decode($msg, TRUE);
        if ($msgObg['type'] === "id") {
            $this->clients->append(array("id" => $msgObg["payload"], "connect" => $from, "game_id" => $msgObg["game_id"]));
            // var_dump($this->clients->count());
        } else {
            foreach ($this->clients as $client) {
                if ($msgObg["to_id"] === "all") {
                    $client["connect"]->send($msgObg["payload"]);
                } else {
                    echo strpos($msgObg["to_id"], $client["id"]);
                    echo $client["game_id"] === $msgObg["game_id"] ? "true" : "false";
                    if (strpos($msgObg["to_id"], $client["id"]) >= 0 && $client["game_id"] === $msgObg["game_id"]) {
                        $client["connect"]->send($msgObg["payload"]);
                    }
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
            // var_dump($this->clients->count());
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
