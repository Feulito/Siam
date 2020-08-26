<?php

class Game {
    private $id;
    private $name;
    private $round;
    private $nbPlayers;

    function __construct(int $id, string $name, int $round, $nbPlayers)
    {
        $this->id = $id;
        $this->name = $name;
        $this->round = $round;
        $this->nbPlayers = $nbPlayers;
    }

    function encode() {
        $object = array(
        'id_game' => $this->id,
        'name' => $this->name,
        'round' => $this->round,
        'nbPlayers' => $this->nbPlayers
        );

        return json_encode($object);
    }
}

?>
