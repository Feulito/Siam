<?php
include_once("../objects/BoardGame.php");
require_once("../../config.php");
include_once('../../base/Bdd.php');
    if (isset($_POST['id_game'])) {
        $bdd = new Bdd();

        $winner = $bdd->getGame($_POST['id_game'])['winner'];

        if ($winner == 2) echo "elephant";
        else echo "rhinoceros";
    }
?>