<?php
include_once("../objects/BoardGame.php");
require_once("../../config.php");
include_once('../../base/Bdd.php');

if (isset($_POST["id_game"])){
    $bdd = new Bdd();
    if (isset($_POST["oldX"]) && isset($_POST["oldY"]) && isset($_POST["type"])) {
        if (isset($_POST["newX"]) && isset($_POST["newY"])){
            $bdd->updatePawns($_POST["id_game"], $_POST["type"], $_POST["oldX"], $_POST["oldY"], $_POST["newX"], $_POST["newY"]);                   
        }
        else if (isset($_POST["delete"])){
            $bdd->deletePawns($_POST["id_game"], $_POST["oldX"], $_POST["oldY"], $_POST["type"]);
        }
        
    }
    else if (isset($_POST["type"]) && isset($_POST["winner"])){
        $bdd->setWinner($_POST["id_game"], $_POST["type"]);
    } 
}

?>
