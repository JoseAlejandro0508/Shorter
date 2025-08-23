<?php

namespace App\Controller\Admin;

use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;


class AdsmanagersController extends AppAdminController
{
    public function index(){
        $this->loadComponent("Paginator");
        $ads=$this->Paginator->paginate($this->Adsmanagers->find());
        $this->set(compact("ads"));
      
        
    }
    public function delete($id){

        $ad=$this->Adsmanagers->get($id);
        if($this->Adsmanagers->delete($ad)){
            $this->Flash->success("Eliminado con exito");
            
        }
        else{
            $this->Flash->error("Error al eliminar");

        }
        return $this->redirect(['action'=>'index']);
        

    }
    public function edit($id){
        $ad=$this->Adsmanagers->get($id);
        if($this->getRequest()->is(['post', 'put'])){
            $this->Adsmanagers->patchEntity($ad,$this->request->getData());
            if ($this->Adsmanagers->save($ad)) {
                $this->Flash->success(__('Your ad has been edited.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to edit your add.'));


        }
        $this->set("ad",$ad);
    }
    public function add(){
        $ads=$this->Adsmanagers->newEntity();
        if($this->getRequest()->is("post")){
            $ads = $this->Adsmanagers->patchEntity($ads, $this->request->getData());



            if ($this->Adsmanagers->save($ads)) {
                $this->Flash->success(__('Your article has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }
        $this->set("ads",$ads);
    }



}
