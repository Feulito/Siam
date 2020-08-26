<?php
    if (!isset($_GET['id_game'])) {
        header('Location: listeparties.php');
    }
    session_start();
    require_once("config.php");
    include_once('php/inc/verifSignedIn.inc');

    $id = $_GET['id_game'];
    include_once('php/inc/verifPlayer.inc');
    echo "
        <input type='hidden' id='id_game' value='$id'>
    ";
    $bdd = new Bdd();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Partie</title>
    <?php include("php/inc/head.inc"); ?>
    <link href="css/plateau.css" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/21fab4a8ae.js" crossorigin="anonymous"></script>
    <?php
    $win = $bdd->getGame($_GET['id_game']);
    $round = $bdd->getGame($_GET['id_game'])['round'];
    if (!isset($win['winner']) && ($win['nbPlayers'] >= 2 || (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == true))) { ?>
        <script src="./js/game.js"></script>
    <?php } ?>
</head>
<body>
    <?php include('php/inc/header.inc'); ?>

    <?php
        if (isset($win['winner'])) {
            if ($win['winner'] == "2") $win = "éléphant";
            else $win = "rhinoceros"
            ?>  <div class="row text-center">
                    <h2 class="col-md-12">Le joueur <?php echo $win; ?> a gagné cette partie</h2>
                </div>
            <?php
        } else if ($win['nbPlayers'] < 2 && (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == false)) {
            ?>
                <div class="row text-center">
                    <h2 class="col-md-12">En attente d'un deuxième joueur...</h2>
                </div>
            <?php
        } else {
            include('php/inc/plateau.inc');
        }
    ?>

    <?php include('php/inc/footer.inc'); ?>
</body>
</html>
