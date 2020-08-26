<?php

class Rock {
    private $x;
    private $y;
    
    function __construct($posX, $posY){
        $this->x = $posX;
        $this->y = $posY;
    }

    function encode():string {
        $object = array(
            'x' => $this->x,
            'y' => $this->y
        );

        return json_encode($object);
    }

    function decode(string $json) :Rock {
        $object = json_decode($json);

        return new Rock($object->{'x'}, $object->{'y'});
    }

    function getX(){
        return $this->x;
    }

    function getY(){
        return $this->y;
    }

    function setX($posX){
        $this->x = $posX;
    }

    function setY($posY){
        $this->y = $posY;
    }

    function draw(){
        echo "<td><img src=\"./images/rocher.gif\"/></td>";
    }

    function getType() {
        return "rocher";
    }

    function getSpecies() {
        return $this->getType();
    }

    function getOrientation() {
        return 0;
    }
}

?>