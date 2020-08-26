<?php
class Animal {
    private $x;
    private $y;
    private $species;
    private $image;
    private $orientation;
    
    function __construct(int $posX, int $posY, string $rinoOrEleph, string $image, string $orientation){
        $this->x = $posX;
        $this->y = $posY;
        $this->species = $rinoOrEleph;
        $this->image = $image;
        $this->orientation = $orientation;
    }

    function getX(){
        return $this->x;
    }

    function getY(){
        return $this->y;
    }

    function setPos($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }
    
    function getOrientation(){
        return $this->orientation;
    }

    function getOppositeOrientation(){
        switch($this->orientation){
        case "north":
            return "south";
        case "south":
            return "north";
        case "west":
            return "east";
        case "east":
            return "west";
        }
    }

    function goOut(){
        $this->setPos(-1, -1);
    }
    
    function isOut(){
        return ($this->x == -1 && $this->y == -1);
    } 

    function getType(){
        return $this->species;
    }
    
    function leave(BoardGame $board){
        $board->leaveCase($this->x, $this->y);
        $this->goOut();     
    }


    function getSpecies(){
        return $this->species;
    }

    function getImage() {
        if ($this -> species === "elephant") {
            switch ($this -> orientation) {
                case "north" :
                    return "images/10.gif";
                break;
                case "east" :
                    return "images/11.gif";
                break;
                case "south" :
                    return "images/12.gif";
                break;
                case "west" :
                    return "images/13.gif";
                break;
            }
        } else if ($this -> species === "rhinoceros") {
            switch ($this -> orientation) {
                case "north" :
                    return "images/14.gif";
                break;
                case "east" :
                    return "images/15.gif";
                break;
                case "south" :
                    return "images/16.gif";
                break;
                case "west" :
                    return "images/17.gif";
                break;
            }
        }
    }

    function encode() {
        $object = array(
            'x' => $this->x,
            'y' => $this->y,
            'species' => $this->species,
            'image' => $this ->image,
            'orientation' => $this->orientation
        );

        return json_encode($object);
    }

    function decode($json) :Animal {
        $object = json_decode($json);

        return new Animal($object->{'x'}, $object->{'y'}, $object->{'species'}, $object->{'image'}, $object->{'orientation'});
    }
}

?>
