<?php
    include('../objects/Animal.php');
    function reserve() :Animal {
        return new Animal(0,0, "elephant", "/images/10.gif",  "north");
    }

    echo reserve() -> encode();

?>