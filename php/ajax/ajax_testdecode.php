<?php
    include('../objects/Animal.php');
    function decoder($json) :Animal {
        $animal = new Animal(0,0, "elephant", "/images/10.gif",  "north");
        $animal = $animal -> decode($json);

        return $animal;
    }

    echo decoder($_POST['object'])->getY();

?>