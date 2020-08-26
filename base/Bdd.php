<?php
include(SITE_ROOT . "/php/objects/Player.php");

class Bdd {
    private $bdd;
    private $dir = __DIR__;

    function __construct() {
        try {
            $this -> bdd = new PDO("sqlite:$this->dir/siam.db");
        } catch (Exception $e) {
            die('Erreur : ' . $e -> getMessage());
        }
    }

    function getAnimals(int $id_player, int $id_game) :array{
        $request = "SELECT * FROM pawns WHERE id_game = :id_game AND type = (SELECT type_animal FROM playerGame WHERE id_player = :id_p AND id_game = :id_g)";
        $r = $this->bdd->prepare($request);
        $res = $r->execute(array(
            ':id_game' => $id_game,
            ':id_p' => $id_player,
            ':id_g' => $id_game
        ));
        
        $animals = array();

        $row = $r->fetch(PDO::FETCH_ASSOC);
        $animals = $row;

        $r->closeCursor();
        return $animals;
    }

    function createGame(string $name) {
        if (isset($_SESSION['id'])) {
            $cpt = 0;
            $name = ucfirst($name);

            $request = 'INSERT INTO game (name, round, nbPlayers) VALUES (:name, 0,1)';
            $r = $this->bdd->prepare($request);
            $r->execute(array(
                ':name' => $name
            ));

            $r->closeCursor();

            $request = "SELECT id_game FROM game";
            $r = $this->bdd->query($request);
            while($data = $r->fetch(PDO::FETCH_ASSOC)) {
                $cpt = $data['id_game'];
            }

            // Ajout du joueur dans la partie ! Il est forcement éléphant
            $request = "INSERT INTO player (id_game, id_user, type_animal, reserve) VALUES (:id_game, :id_user, :type_animal, :reserve)";
            $r = $this->bdd->prepare($request);
            $r->execute(array(
                ':id_game' => $cpt,
                'id_user' => $_SESSION['id'],
                ':type_animal' => "elephant",
                ':reserve' => 3
            ));
            $r->closeCursor();

            // Ajout des rochers dans la partie !
            $request = 'INSERT INTO pawns (id_game, type, posX, posY, orientation) VALUES (:id_game, :type, :posX, :posY, :orientation)';
            $r = $this->bdd->prepare($request);
            $r->execute(array(
                ':id_game' => $cpt,
                ':type' => "rocher",
                ':posX' => 2,
                ':posY' => 1,
                ':orientation' => 0
            ));
            $r->closeCursor();
            $r = $this->bdd->prepare($request);
            $r->execute(array(
                ':id_game' => $cpt,
                ':type' => "rocher",
                ':posX' => 2,
                ':posY' => 2,
                ':orientation' => 0
            ));
            $r->closeCursor();
            $r = $this->bdd->prepare($request);
            $r->execute(array(
                ':id_game' => $cpt,
                ':type' => "rocher",
                ':posX' => 2,
                ':posY' => 3,
                ':orientation' => 0
            ));

            $r -> closeCursor();
        }
    }

    function getGames() :array {
        $request = "SELECT * from game ORDER BY nbPlayers";
        $r = $this->bdd->query($request);

        $games = array();

        while ($data = $r->fetch(PDO::FETCH_ASSOC)) {
            $games[] = $data;
        }

        return $games;
    }

    function getGame(int $id) :array {
        $request = "SELECT * from game WHERE id_game = :id";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id' => $id
        ));

        $game = $r->fetch(PDO::FETCH_ASSOC);

        return $game;
    }

    function getPlayerGames(int $id_user) :array {
        $request = "SELECT game.*, player.type_animal FROM game, player WHERE game.id_game = player.id_game AND player.id_user = :id_user";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_user' => $id_user
        ));

        $games = array();

        while ($data = $r->fetch(PDO::FETCH_ASSOC)) {
            $games[] = $data;
        }

        return $games;
    }


    function getPlayersOfGame(int $id) :array {
        $request = 'SELECT * from player WHERE id_game = :id';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id' => $id
        ));

        $players = array();
        while ($data = $r->fetch(PDO::FETCH_ASSOC)) {
            $players[] = $data;
        }

        return $players;
    }

    function printPlayerGames(array $games) {
        foreach($games as $value) {

            $name = $value['name'];
            $id = $value['id_game'];
            $nbPlayers= $value['nbPlayers'];
            $winner = $value['winner'];

            $tour = (($value['round'] % 2 == 0 && $value['type_animal'] == "elephant") || ($value['round'] % 2 != 0 && $value['type_animal'] == "rhinoceros")) ? "C'est à votre tour" : "C'est au tour de l'adversaire";
            $class = (($value['round'] % 2 == 0 && $value['type_animal'] == "elephant") || ($value['round'] % 2 != 0 && $value['type_animal'] == "rhinoceros")) ? "yourturn" : "party";

            $class = ($nbPlayers > 1 ) ? $class : "party";
            $tour = ($nbPlayers > 1 ) ? $tour : "En attente d'un deuxième joueur";

            if ($winner != NULL) {
                if (($winner == "2" && $value['type_animal'] == "elephant") || ($winner == "1" && $value['type_animal'] == "rhinoceros")) {
                    $tour = "Vous avez gagné cette partie";
                    $class = "win";
                }
                else {
                    $tour = "Vous avez perdu cette partie";
                    $class = "loose";
                }
            }

            echo "
                <a href='partie.php?id_game=$id'>
                    <div class='col-md-3 $class'>
                        <h3>$name</h3>
                        <p>$tour</p>
                        <p>id : $id</p>
                        <p>Nombre de joueurs : $nbPlayers</p>
                    </div>
                </a>
            ";
        }
    }

    function printGameList(array $games) {
        foreach($games as $value) {

            $name = $value['name'];
            $id = $value['id_game'];
            $nbPlayers= $value['nbPlayers'];
            
            if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
                $btnSup = "
                <form method='post'>
                    <input type='hidden' value='$id' name='idsup'>
                    <input type='submit' value='Supprimer'>
                </form>
                ";
            } else {
                $btnSup = "";
            }

            echo "
                <a href='partie.php?id_game=$id'>
                    <div class='col-md-3 party'>
                        <h3>$name</h3>
                        <p>id : $id</p>
                        <p>Nombre de joueurs : $nbPlayers</p>
                        "
                        .
                        $btnSup
                        .
                        "
                    </div>
                </a>
            ";
        }
    }

    function deleteGame(int $id_game) {
        $request = "DELETE FROM player WHERE id_game = :id_game";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_game' => $id_game
        ));

        $r->execute(array(
            ':id_game' => $id_game
        ));


        $r->closeCursor();

        $request = "DELETE FROM pawns WHERE id_game = :id_game";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_game' => $id_game
        ));

        $r->execute(array(
            ':id_game' => $id_game
        ));

        $r->closeCursor();

        $request = "DELETE FROM game WHERE id_game = :id_game";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_game' => $id_game
        ));

        $r->closeCursor();
    }

    function getBoard(int $idgame) {
        $request = 'SELECT * FROM pawns WHERE id_game = :id';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id' => $idgame
        ));

        $board = array();
        while($data = $r->fetch(PDO::FETCH_ASSOC)) {
            $board[] = $data;
        }

        return $board;
    }

    function getPlayer(int $id_user, int $id_game) :Player {
        $request = "SELECT * FROM player WHERE id_user = :id_user AND id_game = :id_game";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_user' => $id_user,
            ':id_game' => $id_game
        ));
        $row = $r -> fetch(PDO::FETCH_ASSOC);
        $animal = $row["type_animal"];
        $reserve = $row["reserve"];

        $r->closeCursor();

        $name = $this->getUser($id_user)['pseudo'];

        return new Player($name, $animal, $reserve);
    }

    function getPlayerWithId(int $id_user) :array {
        $request = 'SELECT * from player WHERE id_user = :id_user';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_user' => $id_user
        ));

        $user = array();

        while ($data = $r->fetch(PDO::FETCH_ASSOC)) {
            $user[] = $data;
        }

        return $user;
    }

    function addPlayerToGame(int $id_user, $id_game) {
        $request = 'INSERT INTO player (id_game, id_user, type_animal, reserve) VALUES (:id_game, :id_user, :type_animal, :reserve);';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_game' => $id_game,
            ':id_user' => $id_user,
            ':type_animal' => 'rhinoceros',
            ':reserve' => 3
        ));

        $r->closeCursor();

        $request = 'UPDATE game SET nbPlayers = 2 WHERE id_game = :id_game';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_game' => $id_game
        ));
    }

    function registerUser(string $pseudo, string $password) {
        $pseudo = ucfirst($pseudo);
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
        $request = 'INSERT INTO user (pseudo, password) VALUES (:pseudo, :password)';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            'pseudo' => $pseudo,
            'password' => $passwordHashed
        ));

        $r->closeCursor();
    }

    function getUser(int $id) {
        $request = 'SELECT id_user, pseudo FROM user WHERE id_user = :id_user';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_user' => $id
        ));

        $user = $r->fetch(PDO::FETCH_ASSOC);

        $r->closeCursor();
        return $user;
    }

    function connectUser(string $pseudo, string $password) {
        $pseudo = ucfirst($pseudo);
        $request = 'SELECT * FROM user WHERE pseudo = :pseudo';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':pseudo' => $pseudo
        ));
        $user = $r->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {
            $_SESSION['pseudo'] = $pseudo;
            $_SESSION['id'] = $user['id_user'];
            if (!isset($_SESSION['isAdmin'])) $_SESSION['isAdmin'] = false;
        }

        $r->closeCursor();
    }
    
    function getBdd() {
        return $this->bdd;
    }

    function updatePawns($id_game, $type_animal, $oldX, $oldY, $newX, $newY){
        $request = "UPDATE pawns SET posX = :newX, posY= :newY WHERE id_game = :id_game AND posX = :oldX AND posY = :oldY AND type = :type_animal";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ":newX" => $newX,
            ":newY" => $newY,
            ":id_game" => $id_game,
            ":oldX" => $oldX,
            ":oldY" => $oldY,
            ":type_animal" => $type_animal
        ));
        $r->closeCursor();
    }

    function setWinner($id_game, $type_animal){
        if ($type_animal == "rhinoceros"){
            $winner = 1;
        }else $winner = 2;
        $request = "UPDATE game SET winner = :winner WHERE id_game = :id_game";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ":winner" => $winner,
            ":id_game" => $id_game
        ));
        $r->closeCursor();
    }

    function deletePawns($id_game, $oldX, $oldY, $type_animal){
        $request = "DELETE FROM pawns WHERE id_game = :id_game AND type = :type_animal AND posX = :oldX AND posY = :oldY";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ":id_game" => $id_game,
            ":type_animal" => $type_animal,
            ":oldX" => $oldX,
            ":oldY" => $oldY
        ));

        $request = "UPDATE player SET reserve = reserve +1 WHERE id_game = :id_game AND type_animal = :type_animal";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ":id_game" => $id_game,
            ":type_animal" => $type_animal
        ));
    }

    function saveBoard(int $id_game, array $plateau) {
        // On supprime tous les pions
        $request = "DELETE from pawns WHERE id_game = $id_game";
        $this->bdd->query($request);

        // Et on le remet tous à leur nouvelle position
        foreach ($plateau as $key => $value) {
            foreach ($value as $k => $v) {
                $request = "INSERT INTO pawns (id_game, type, posX, posY, orientation)
                VALUES (:id_game, :type, :posX, :posY, :orientation)";
                $r = $this->bdd->prepare($request);
                $r->execute(array(
                    ':id_game' => $id_game,
                    ':type' => $v->getSpecies(),
                    ':posX' => $v->getX(),
                    ':posY' => $v->getY(),
                    ':orientation' => $v->getOrientation()
                ));
            }
        }
    }

    function savePlayer(int $id_game, int $id_player, Player $player) {
        $reserve = $player->getReserve();
        $request = "UPDATE player SET reserve = $reserve WHERE id_user = $id_player AND id_game = $id_game";
        $this->bdd->query($request);
        echo "fait";
    }

    function incrementRound(int $id_game) {
        $game = $this->getGame($id_game);

        $round = $game['round'] + 1;

        $request = "UPDATE game SET round = $round WHERE id_game = :id_game";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_game' => $id_game
        ));
    }

    function registerAdmin(string $pseudo, string $password) {
        $pseudo = ucfirst($pseudo);
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
        $request = 'INSERT INTO admin (pseudo, password) VALUES (:pseudo, :password)';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            'pseudo' => $pseudo,
            'password' => $passwordHashed
        ));

        $r->closeCursor();
    }

    function getAdmin(int $id) {
        $request = 'SELECT id_user, pseudo FROM user WHERE id_user = :id_user';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_user' => $id
        ));

        $user = $r->fetch(PDO::FETCH_ASSOC);

        $r->closeCursor();
        return $user;
    }

    function connectAdmin(string $pseudo, string $password) {
        $pseudo = ucfirst($pseudo);
        $request = 'SELECT * FROM admin WHERE pseudo = :pseudo';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':pseudo' => $pseudo
        ));
        $admin = $r->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $admin['password'])) {
            $_SESSION['pseudoAdmin'] = $pseudo;
            $_SESSION['idAdmin'] = $admin['id_admin'];
            $_SESSION['isAdmin'] = true;
        }

        $r->closeCursor();
    }

    function updateDernier(int $id_game, string $dernier) {
        $request = "UPDATE game SET dernier = :dernier WHERE id_game = :id_game";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':dernier' => $dernier,
            ':id_game' => $id_game
        ));
    }

    function getDernier(int $id_game) {
        $request = "SELECT * FROM game WHERE id_game = :id_game";
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_game' => $id_game
        ));;

        $data = $r->fetch(PDO::FETCH_ASSOC);

        return $data['dernier'];
    }

    function changeUserPassword(int $id_user, string $old, string $new) :bool {
        $request = 'SELECT * FROM user WHERE id_user = :id_user';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_user' => $id_user
        ));
        $user = $r->fetch(PDO::FETCH_ASSOC);

        $r->closeCursor();

        if (password_verify($old, $user['password'])) {
            $passwordHashed = password_hash($new, PASSWORD_DEFAULT);
            $request = "UPDATE user SET password = :pass WHERE id_user = :id_user";
            $r = $this->bdd->prepare($request);
            $r->execute(array(
                ':pass' => $passwordHashed,
                ':id_user' => $id_user
            ));

            $r->closeCursor();
            return true;
        }
        return false;
    }

    function changeAdminPassword(int $id_user, string $old, string $new) :bool {
        $request = 'SELECT * FROM admin WHERE id_admin = :id_admin';
        $r = $this->bdd->prepare($request);
        $r->execute(array(
            ':id_admin' => $id_user
        ));
        $user = $r->fetch(PDO::FETCH_ASSOC);

        $r->closeCursor();

        if (password_verify($old, $user['password'])) {
            $passwordHashed = password_hash($new, PASSWORD_DEFAULT);
            $request = "UPDATE admin SET password = :pass WHERE id_admin = :id_user";
            $r = $this->bdd->prepare($request);
            $r->execute(array(
                ':pass' => $passwordHashed,
                ':id_user' => $id_user
            ));

            $r->closeCursor();
            return true;
        }
        return false;
    }
}

$bdd = new Bdd();

?>
