<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Http\Response;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Utility\Text;

class IntegrationsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cookie');
        $this->loadComponent('Captcha');

        $this->loadComponent('Security');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->viewBuilder()->setLayout('front');
        $this->Auth->allow(['vdmarketinglisten', 'viewvdoffert']);



        //        if (in_array($this->getRequest()->getParam('action'), ['view', 'go', 'popad'])) {
        //            $this->getEventManager()->off($this->Security);
        //        }
    }
    public function vdmarketinglisten($token)
    {
        //https://short.ultinoticias.com/vdintegration/[AccesToken]/listen?token=[UserToken(data2)]&profit=[earnings]
        $VdMarketingIntegrationToken = get_option('VdMarketingIntegrationToken');
        if ($VdMarketingIntegrationToken != $token) {
            $this->log("Failed Entry $token", 'debug');
            $response = ["status" => "error", "message" => "You dont have access"];
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($response));
        }
        $this->log("Entry VdIntegration", 'debug');


        $VdMarketingLog = TableRegistry::getTableLocator()->get("Vdmarketinglogs");
        $VdMarketingUsers = TableRegistry::getTableLocator()->get("Vdmarketingusers");
        $VdMarketingOfferts = TableRegistry::getTableLocator()->get("vdmarketingusersofferts");
        $VdMarketingOffertsAdmin = TableRegistry::getTableLocator()->get("vdmarketingofferts");

        $data = $this->request->getQuery();
        $token = $data["token"];
        $earn = $data["profit"];
        $this->log("Entry VdIntegration $data $_SERVER", 'debug');

        if (!isset($token) || !isset($earn)) {
            $response = ["status" => "error", "message" => "Invalid Format"];
            $this->log("Invalid Format", 'debug');

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($response));
        } else {

            $Offert = $VdMarketingOfferts
                ->find()
                ->where(['token' => $token])
                ->first();
            if (!isset($Offert)) {
                $response = ["status" => "error", "message" => "Invalid Token"];
                $this->log("Invalid Token", 'debug');
                return $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode($response));
            }
            if ($Offert->oid) {
                $OffertDates = $VdMarketingOffertsAdmin
                    ->find()
                    ->where(["id" => $Offert->oid])
                    ->first();
            }else{
                $OffertDates = $VdMarketingOffertsAdmin
                    ->find()
                    ->where(["url" => $Offert->url])
                    ->first();

            }


            if (!isset($OffertDates)) {
                $response = ["status" => "error", "message" => "Not Disponible Offert"];
                $this->log("Not Disponible Offert", 'debug');
                return $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode($response));
            }
            $data["shortearn"] = $OffertDates->earning;
            $vdlog = $VdMarketingLog->newEntity();
            $vdlog->user = $Offert->user;
            $vdlog->token = $Offert->token;
            $vdlog->info = json_encode($data);
            if ($VdMarketingLog->save($vdlog)) {
                $this->log("Log save sucefull sucefull", 'debug');
            };
            $UserVd = $VdMarketingUsers
                ->find()
                ->where(['user' => $Offert->user])
                ->first();

            if (!isset($UserVd)) {
                $this->log("User Not founded", 'debug');
                $response = ["status" => "error", "message" => "User Not founded"];
                return $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode($response));
            } else {

                $UserVd->earnings += floatval($OffertDates->earning);
                if ($VdMarketingUsers->save($UserVd)) {
                    $this->log("User update sucefull", 'debug');
                };
            }
        }


        $response = ["status" => "ok", "message" => "Data recived"];



        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($response));
    }
    public function viewvdoffert($token)
    {
        $OffertsTable = TableRegistry::getTableLocator()->get('vdmarketingusersofferts');
        $VdMarketingOffertsAdmin = TableRegistry::getTableLocator()->get("vdmarketingofferts");
        $Offert = $OffertsTable
            ->find()
            ->where(['token' => $token])
            ->first();
        if($Offert->oid){
            $Offert_dates=$VdMarketingOffertsAdmin
            ->find()
            ->where(['id'=>$Offert->oid])
            ->first();
            $Offert->url=$Offert_dates->url;

        }
        if (!isset($Offert)) {
            $this->response->withStatus(204);
            return  $this->response;
        }
        $finalurl = "$Offert->url&data1=$Offert->user&data2=$token";
        return $this->redirect($finalurl);
    }
}
