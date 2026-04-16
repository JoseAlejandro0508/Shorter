<?php



namespace App\Model\Table;

use Cake\ORM\Table;

class VdmarketinglogsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }
}