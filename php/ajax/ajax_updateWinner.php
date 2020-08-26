<?php
include_once("../objects/BoardGame.php");
require_once("../../config.php");
include_once('../../base/Bdd.php');
if (isset($_POST["id_game"]) && isset($_POST["type"])){
    $bdd = new Bdd();
    $bdd->setWinner($_POST["id_game"], $_POST["type"]);
}
?>
