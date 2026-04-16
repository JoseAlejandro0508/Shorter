<?php



namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;

class VdmarketingoffertsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }

}
