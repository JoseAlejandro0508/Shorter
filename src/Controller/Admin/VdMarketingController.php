<?php

namespace App\Controller\Admin;

use Cake\ORM\TableRegistry;
use Cake\Http\Response;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Text;

class VdMarketingController extends AppAdminController
{
    public function index(){
        $OffertsTable=TableRegistry::getTableLocator()->get("vdmarketingofferts");
        $this->loadComponent("Paginator");
        $offerts=$this->Paginator->paginate($OffertsTable->find());
        $this->set(compact("offerts"));
      
        
    }
    public function delete($id){
        $OffertsTable=TableRegistry::getTableLocator()->get("vdmarketingofferts");
        $offert=$OffertsTable->get($id);
        if($OffertsTable->delete($offert)){
            $this->Flash->success("Eliminado con exito");
            
        }
        else{
            $this->Flash->error("Error al eliminar");

        }
        return $this->redirect(['action'=>'index']);
        

    }
    public function newaccestoken($change=false){
  
        $OptionsTable=TableRegistry::getTableLocator()->get('options');
        $VdOfferts=TableRegistry::getTableLocator()->get('vdmarketingusersofferts');
        $AccesToken=$OptionsTable
        ->find()
        ->where(['name'=>'VdMarketingIntegrationToken'])
        ->first();
        if($AccesToken->value=='none')
        {
            $AccesToken->value=Text::uuid();
            if($OptionsTable->save($AccesToken)){
                $this->Flash->success("Token de acceso actualizado con exito");
            }


        }     
        if($change){
            $AccesToken->value=Text::uuid();
            if($OptionsTable->save($AccesToken)){
                $this->Flash->success("Token de acceso actualizado con exito");
            }

        } 
        $this->set("AccesToken",$AccesToken->value);


    }

    public function edit($id){
        $OffertsTable=TableRegistry::getTableLocator()->get("vdmarketingofferts");
        $offert=$OffertsTable->get($id);
        if($this->getRequest()->is(['post', 'put'])){
           $OffertsTable->patchEntity($offert,$this->request->getData());
            if ($OffertsTable->save($offert)) {
                $this->Flash->success(__('Your offert has been edited.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to edit your offert.'));


        }
        $this->set("offert",$offert);
    }
    public function add(){
        $OffertsTable=TableRegistry::getTableLocator()->get("vdmarketingofferts");

        $offert=$OffertsTable->newEntity();
        if($this->getRequest()->is("post")){
            $offert =$OffertsTable->patchEntity($offert, $this->request->getData());



            if ($OffertsTable->save($offert)) {
                $this->Flash->success(__('Your offert has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your offert.'));
        }
        $this->set("offert",$offert);
    }
    public function logs()
    {
        $LogsTable=TableRegistry::getTableLocator()->get("vdmarketinglogs");
        $conditions = [];

        $filter_fields = ['id', 'user', 'token'];

        //Transform POST into GET
        if ($this->getRequest()->is(['post', 'put']) && isset($this->getRequest()->data['Filter'])) {
            $filter_url = [];

            $filter_url['controller'] = $this->getRequest()->params['controller'];

            $filter_url['action'] = $this->getRequest()->params['action'];

            // We need to overwrite the page every time we change the parameters
            $filter_url['page'] = 1;

            // for each filter we will add a GET parameter for the generated url
            foreach ($this->getRequest()->data['Filter'] as $name => $value) {
                if (in_array($name, $filter_fields) && strlen($value)) {
                    // You might want to sanitize the $value here
                    // or even do a urlencode to be sure
                    $filter_url[$name] = urlencode($value);
                }
            }
            // now that we have generated an url with GET parameters,
            // we'll redirect to that page
            return $this->redirect($filter_url);
        } else {
            // Inspect all the named parameters to apply the filters
            foreach ($this->getRequest()->getQuery() as $param_name => $value) {
                $value = urldecode($value);
                if (in_array($param_name, $filter_fields)) {
                    if (in_array($param_name, ['token'])) {
                        $conditions[] = [
                            [$param_name . ' LIKE' => '%' . $value . '%'],
                        ];
                    } elseif (in_array($param_name, ['id', 'user'])) {
                        $conditions[$param_name] = $value;
                    }
                    $this->getRequest()->data['Filter'][$param_name] = $value;
                }
            }
        }

        $query = $LogsTable->find()

            ->where($conditions);
           
        $logs= $this->paginate($query);

        $this->set('logs', $logs);
    }

}
