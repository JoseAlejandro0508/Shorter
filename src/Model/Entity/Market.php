<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Market extends Entity
{
    protected $_accessible = [
        'id' => true,
        'my_array' => true,
    ];

    // Puedes definir getters y setters si lo necesitas
    public function getMyArray()
    {
        return $this->my_array;
    }

    public function setMyArray($array)
    {
        $this->my_array = $array;
    }
}
