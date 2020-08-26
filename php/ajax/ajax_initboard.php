<?php

    if (!isset($_POST['id_game'])) {
        header('Location: listeparties.php');
    }
    require_once("../../config.php");
    include_once('../../base/Bdd.php');
    include_once('../objects/Game.php');
    include_once('../objects/BoardGame.php');
    include_once('../objects/Animal.php');
    include_once('../objects/Player.php');
    include_once('../objects/Rock.php');

    $bd = new Bdd();

    $b = $bd->getBoard($_POST['id_game']);
    $board = new BoardGame();
    foreach ($b as $key => $v) {
        switch ($v['type']) {
            case "rocher" :
                $n = new Rock($v['posX'], $v['posY']);
                $board->setRock($n, $v['posX'], $v['posY']);
            break;
            case "elephant" :
                $n = new Animal($v['posX'], $v['posY'], $v['type'], "", $v['orientation']);
                $board->setAnimal($n, $v['posX'], $v['posY']);
            break;
            case "rhinoceros" :
                $n = new Animal($v['posX'], $v['posY'], $v['type'], "", $v['orientation']);
                $board->setAnimal($n, $v['posX'], $v['posY']);
            break;
        }
    }
    echo $board->encode();
?>