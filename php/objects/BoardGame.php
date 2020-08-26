<?php
include("Rock.php");
include ("Animal.php");

class BoardGame{
    private $board;
    private $victory;
    private $playerRhino;
    private $playerEleph;

    function __construct(){
        if (func_num_args() == 0) $this->init();
        else if (func_num_args() == 1) {
            $this->board = func_get_arg(0);
        } else if (func_num_args() == 4) {
            $this -> board = func_get_arg(0);
            $this -> victory = func_get_arg(1);
            $this->playerRhino = func_get_arg(2);
            $this->playerEleph = func_num_args(3);
        } else {
            throw new InvalidArgumentException("Nombre d'arguments incorrect");
        }
    }

    function init(){
        $this->board = array();
        for ($i=0; $i<5; $i++){
            $this->board[$i] = array();
            for ($j=0; $j<5; $j++){
                $this->board[$i][$j] = null;
            }
        }
    }
    
    function encode() :string {
        $object = array(
            'board' => $this->encodeBoard(),
            'victory' => $this->victory
        );

        return json_encode($object);
    }

    function encodeBoard() {
        $board = array();

        for ($i = 0; $i < 5; $i++) {
            $board[$i] = array();
            for ($j = 0; $j < 5; $j++) {
                if ($this->board[$i][$j] != null) {
                    $board[$i][$j] = array(
                        "x" => $this->board[$i][$j]->getX(),
                        "y" => $this->board[$i][$j]->getY(),
                        'type' => $this->board[$i][$j]->getType(),
                        'orientation' => $this->board[$i][$j]->getOrientation()
                    );
                } else {
                    $board[$i][$j] = null;
                }
            }
        }

        return $board;
    }

    function decode(string $json) :BoardGame {
        $object = json_decode($json);
        //var_dump($object);
        $tab = array();
        for($i = 0; $i < sizeof($object); $i++) {
            for($j = 0; $j < sizeof($object[$i]); $j++) {
                $o = $object[$i][$j];
                if ($o == null) $tab[] = array();
                else {
                    if (($o->type == "elephant")||($o->type == "rhinoceros")) $tab[$i][$j] = new Animal($o->x, $o->y, $o->type, "null", $o->orientation);
                    else $tab[$i][$j] = new Rock($o->x, $o->y);
                }
            }
        }
        return new BoardGame($tab);
    }
    
    function getCase($i, $j){
        if ($this->checkCoords($i, $j))
            return $this->board[$i][$j];
    }   

    function checkCoords($i, $j){
        return (0<=$i && $i<5 && 0<=$j && $j<5);
    }

    function isEmpty($i, $j){
        return $this->board[$i][$j] == null;
    }

    function getBoard(){
        return $this->board;
    }

    function setBoard($b){
        $this->board = $b;
    }

    function setAnimal(Animal $a, $i, $j){
        $this->board[$i][$j] = $a; 
    }

    function setRock(Rock $r, $i, $j) {
        $this->board[$i][$j] = $r;
    }

    function leaveCase($i, $j){
        $this->board[$i][$j] = null;
    }


    function getVictory(){
        return $this->victory;
    }
    
    function isEnterCoords($i, $j){
        return $this->checkCoords($i, $j) && ($i==0 || $i== 4 || $j == 0|| $j == 4);
    }

    function drawBoard(){

    }

    function drawEmpty(){
        echo "<td><img src=\"./images/22.gif\"/></td>"; //un test
    }
} 

//$b = new BoardGame();
//$b->drawBoard();
?>
