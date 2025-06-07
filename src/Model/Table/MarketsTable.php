<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class MarketsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('markets');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // Define la columna 'my_array' como un campo de texto
        $this->schema()->column('my_array')['type'] = 'text'; 
    }

    
        public function getById($id): ?\Cake\Datasource\EntityInterface
        {   
            $result = $this->find()
            ->where(['id' => $id])
            ->first();
            $result->my_array = json_decode($result->my_array, true);
            return $result;
            
        }
        public function marketadd(array $data): bool
        {
            // Crea una nueva entidad
            $entity = $this->newEntity();
            $data_=[];
            // Convierte el array a JSON
            $s= json_encode($data);
            $data_['my_array'] = $s;
            
            // Carga los datos en la entidad
            $entity = $this->patchEntity($entity, $data_);
    
            // Guarda la entidad en la base de datos
            if ($this->save($entity)) {
                return true; // Devuelve `true` si el guardado es exitoso
            } else {
                return false; // Devuelve `false` si el guardado falla
            }
        }
        public function marketedit($data)
        {

            $data->my_array=json_encode($data->my_array);
            
            if ($this->save($data)) {
                return true; // Devuelve `true` si el guardado es exitoso
            } else {
                return false; // Devuelve `false` si el guardado falla
            }
        }
        public function getAll(): array
        {
            // Obtiene todos los registros de la tabla
            $entities = $this->find()
                ->all()
                ->toArray();
    
            // Convierte los arrays de texto a arrays PHP
            $ent=[];
            foreach ($entities as &$entity) {

                $entity->my_array = json_decode($entity->my_array, true);
            }
            foreach ($entities as $value) {

                if ($value->my_array == null) {
                    continue;

                }
                array_push($ent,$value);
            }

    
            return $ent;
        }
    }