<?php

class Player {
    private $name;
    private $type;
    private $reserve;

    function __construct()
    {
        if (func_num_args() == 3) {
            if (is_string(func_get_arg(0)) && is_string(func_get_arg(1))) {
                $this->name = func_get_arg(0);
                $this->type = func_get_arg(1);
                $this->reserve = func_get_arg(2);
            } else {
                throw new InvalidArgumentException;
            }
        } else {
            $this->name = "default";
        }
    }

    function encode() :string {
        $object = array(
            'name' => $this->name,
            'type' => $this->type,
            'reserve' => $this->reserve
        );

        return json_encode($object);
    }

    function decode(string $json) :Player {
        $object = json_decode($json);

        var_dump($object->reserve);
        var_dump($object->name);

        return new Player($object->name, $object->type, $object->reserve);
    }

    function getName() :string {
        return $this -> name;
    }

    function getType() :string {
        return $this -> type;
    }

    function getReserve() {
        return $this->reserve;
    }

    function incReserve() {
        $this->reserve++;
    }

    function decReserve() {
        $this->reserve--;
    }
}

?>
