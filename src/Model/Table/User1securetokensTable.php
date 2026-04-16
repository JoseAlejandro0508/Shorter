<?php



namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;

class User1securetokensTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }
    public function generateUuidToken()
    {
        $token = '';
        $maxAttempts = 500;
        $attempts = 0;

        do {
            $token = Text::uuid(); // Genera UUID v4
            $exists = $this->exists(['token' => $token]);
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);

        return $token;
    }
}
