<?php

namespace App\Controller\Member;

use Cake\ORM\TableRegistry;
use Cake\Http\Response;
use Cake\Datasource\ConnectionManager;
use Cake\Routing\Router;
use Cake\I18n\Time;

class VdmarketingUsersController extends AppMemberController
{

    public function offerts()
    {
        $VdmarketingUsers = TableRegistry::getTableLocator()->get("Vdmarketingusers");
        $AuthUser = $this->Auth->user('username');

        $VdUser = $VdmarketingUsers->find()
            ->where(["user" => $AuthUser])
            ->first();

        $this->set("user", $VdUser);
        $this->viewBuilder()->setLayout("VdMarketing");
        $AuthUser = $this->Auth->user('username');
        $OfertsTable = TableRegistry::getTableLocator()->get("vdmarketingofferts");
        $OfertsUsersTable = TableRegistry::getTableLocator()->get("vdmarketingusersofferts");
        $AvailableOferts = [];
        $Filter = ['country'];
        $conditions = [];

        if ($this->getRequest()->is(["post", "put"])) {
            $data = $this->request->getData();
            foreach ($data as $param => $value) {
                if (!in_array($param, $Filter)) {
                    continue;
                }
                $conditions[$param] = $value;
            }
        }

        $UserOfferts = $OfertsUsersTable
            ->find()
            ->where(["user" => $AuthUser])
            ->toArray();
        $Offerts = $OfertsTable
            ->find()
            ->where($conditions)
            ->all();
        foreach ($Offerts as $offert) {
            if (in_array($offert->url, $UserOfferts)) {
                continue;
            }
            array_push($AvailableOferts, $offert);
        }
        $this->set("offerts", $AvailableOferts);
    }
    public function myofferts()
    {


        $VdmarketingUsers = TableRegistry::getTableLocator()->get("Vdmarketingusers");
        $AuthUser = $this->Auth->user('username');
        $connection = ConnectionManager::get('default');
        $sql="SELECT token ,COUNT(*) as frec FROM vdmarketinglogs WHERE user = :user GROUP BY token";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('user', $AuthUser, 'string');
        $stmt->execute();

        $views_per_token = $stmt->fetchAll('assoc');

        $VdUser = $VdmarketingUsers->find()
            ->where(["user" => $AuthUser])
            ->first();

        $this->set("user", $VdUser);
        $this->viewBuilder()->setLayout("VdMarketing");
        $AuthUser = $this->Auth->user('username');
        $OfertsTable = TableRegistry::getTableLocator()->get("vdmarketingofferts");
        $OfertsUsersTable = TableRegistry::getTableLocator()->get("vdmarketingusersofferts");
        $OfertsDates = [];
        $OfertsUrls = [];

        $Filter = [];
        $conditions = [];
        $conditions["user"] = $AuthUser;
        if ($this->getRequest()->is(["post", "put"])) {
            $data = $this->request->getData();
            foreach ($data as $param => $value) {
                if (!in_array($param, $Filter)) {
                    continue;
                }
                $conditions[$param] = $value;
            }
        }

        $UserOfferts = $OfertsUsersTable
            ->find()
            ->where($conditions)
            ->toArray();

        foreach ($UserOfferts as $offert) {
            if ($offert->oid) {
                $OfferDesc = $OfertsTable
                    ->find()
                    ->where(["id" => $offert->oid])
                    ->first();
            } else {
                $OfferDesc = $OfertsTable
                    ->find()
                    ->where(["url" => $offert->url])
                    ->first();
                $offert->oid=$OfferDesc->id;
                $OfertsUsersTable->save($offert);
            }
            $OfertsDates[$offert->token] = $OfferDesc;
            $url = Router::url("/vdoffert/{$offert->token}", true);
            $OfertsUrls[$offert->token] = $url;
        }
        $views_per_token_=[];
        foreach ($views_per_token  as $date) {
            $key=$date["token"];
            $value=$date["frec"];
            $views_per_token_[$key]=$value;
        }
        $this->set("ViewsPerToken", $views_per_token_);

        $this->set("offertsUrl", $OfertsUrls);
        $this->set("offertsDescription", $OfertsDates);
        $this->set("offerts", $UserOfferts);
    }

    public function getshortlink()
    {


        $link = $this->request->getQuery()["url"];
        $oid=$this->request->getQuery()["id"];
        $OffertsUsersTable = TableRegistry::getTableLocator()->get("vdmarketingusersofferts");
        $Coincidence = $OffertsUsersTable
            ->find()
            ->where(['url' => $link, 'user' => $this->Auth->user('username')])
            ->first();
        if ($Coincidence != null) {
            $this->Flash->success("Ya posee esa oferta");
            return $this->redirect(["action" => "offerts"]);
        }
        $token = $OffertsUsersTable->generateUuidToken();
        $ShortOffer = $OffertsUsersTable->newEntity();
        $ShortOffer->url = $link;
        $ShortOffer->token = $token;
        $ShortOffer->oid= $oid;
        $ShortOffer->user = $this->Auth->user('username');
        if ($OffertsUsersTable->save($ShortOffer)) {
            $this->Flash->success("Oferta obtenida con exito");
        } else {
            $this->Flash->error("Error al obtener oferta");
        }
        return $this->redirect(["action" => "offerts"]);
    }
    public function create()
    {

        $AuthUser = $this->Auth->user('username');
        $Token = $this->VdmarketingUsers->generateUuidToken();
        $user = $this->VdmarketingUsers->newEntity();
        $user->token = $Token;
        $user->user = $AuthUser;
        if ($this->VdmarketingUsers->save($user)) {
            $this->Flash->success("Usuario de vdmarketing creado exitosamente");
            return $this->redirect(["action" => "index"]);
        }
        $this->Flash->success("Error al intentar crear usuario");
    }
    public function index()
    {


        $this->viewBuilder()->setLayout("VdMarketing");
        $AuthUser = $this->Auth->user('username');
        $VdmarketingUsers = TableRegistry::getTableLocator()->get("Vdmarketingusers");
        $Logs =TableRegistry::getTableLocator()->get('vdmarketinglogs');
        $UserLogs=$Logs->find()
        ->where(['user'=>$AuthUser])
        ->toArray();
        
        
        $VdUser = $VdmarketingUsers->find()
            ->where(["user" => $AuthUser])
            ->first();



        $actualtime = Time::now();
        $startTime = $actualtime->modify('-1 month');



        if ($VdUser == null) {
            $this->create();
            return;
        }
        $UserStats = TableRegistry::getTableLocator()->get("Vdmarketinglogs")
            ->find()
            ->where(['user' => $AuthUser, 'created >' => $startTime])

            ->order(['created' => 'DESC'])
            ->toArray();

        $ViewsPerDay = [];
        $TotalViews = 0;
        $TotalGanance = 0;
        for ($i = 0; $i < 31; $i++) {
            $startTime->modify('+1 day');
            $d = $startTime->format('m-d');
            $ViewsPerDay[$d] = 0;
        }
        foreach ($UserStats as $stat) {
            $TotalViews += 1;
            $data = json_decode($stat->info);
            $TotalGanance += (float)$data->shortearn;

            $day = $stat->created->format('m-d');

            $ViewsPerDay[$day] += 1;
        }

        $this->set("TotalGanance", $TotalGanance);
        $this->set("TotalViews", $TotalViews);
        $this->set("UserLogs",$UserLogs);

        $this->set("ViewsPerDay", $ViewsPerDay);

        $this->set("user", $VdUser);
    }
    public function withdraw()
    {
        $this->viewBuilder()->setLayout("VdMarketing");
        $user = TableRegistry::getTableLocator()->get("Users")->get($this->Auth->user('id'));
        $Withdraws = TableRegistry::getTableLocator()->get("Withdraws");
        $VdUser = TableRegistry::getTableLocator()->get("Vdmarketingusers")->find()->where(["user" => $this->Auth->user('username')])->first();
        $withdrawal_methods = array_column_polyfill(get_withdrawal_methods(), 'amount', 'id');
        $minimum_withdrawal_amount = $withdrawal_methods[$user->withdrawal_method];
        $max_withdrawal_amount = $VdUser->earnings;
        $query = $Withdraws->find()
            ->where(['user_id' => $this->Auth->user('id'), 'type' => 'VdMarketing']);
        $withdraws = $this->paginate($query);

        $this->set('withdraws', $withdraws);

        $total_withdrawn = $Withdraws->find()
            ->select(['total' => 'SUM(amount)'])
            ->where([
                'user_id' => $this->Auth->user('id'),
                'status' => 3,
                'type' => 'VdMarketing'
            ])
            ->first();
        $this->set('total_withdrawn', $total_withdrawn->total);

        $pending_withdrawn = $Withdraws->find()
            ->select(['total' => 'SUM(amount)'])
            ->where([
                'user_id' => $this->Auth->user('id'),
                'status' => 2,
                'type' => 'VdMarketing'
            ])
            ->first();
        $this->set('pending_withdrawn', $pending_withdrawn->total);


        $this->set('user', $VdUser);
        $this->set('min', $minimum_withdrawal_amount);
        $this->set('max', $max_withdrawal_amount);
    }
    public function requestWithdraw()
    {
        $user = TableRegistry::getTableLocator()->get("Users")->get($this->Auth->user('id'));
        $Withdraws = TableRegistry::getTableLocator()->get("Withdraws");
        $VdUser = TableRegistry::getTableLocator()->get("Vdmarketingusers")->find()->where(["user" => $this->Auth->user('username')])->first();
        $VdUsers = TableRegistry::getTableLocator()->get("Vdmarketingusers");

        $this->getRequest()->allowMethod(['post', 'put']);
        $amount_ = $this->request->getData('amount');



        $withdraw = $Withdraws->newEntity();
        $data = [];

        $withdraw->user_id = $this->Auth->user('id');
        $withdraw->status = 2;


        $method = $user->withdrawal_method;
        $account = $user->withdrawal_account;

        if ($method !== 'wallet' && (empty($method) || empty($account))) {
            $this->Flash->error(__('You should fill your withdrawal info from your profile settings.'));

            return $this->redirect(['action' => 'index']);
        }

        $data['amount'] = price_database_format($amount_);


        $withdrawal_methods = array_column_polyfill(get_withdrawal_methods(), 'amount', 'id');

        if (!in_array($user->withdrawal_method, array_keys($withdrawal_methods))) {
            $this->Flash->error(__('Invalid withdrawal method.'));

            return $this->redirect(['action' => 'index']);
        }

        $minimum_withdrawal_amount = $withdrawal_methods[$user->withdrawal_method];

        if ($data['amount'] < $minimum_withdrawal_amount) {
            $this->Flash->error(__(
                'Withdraw amount should be equal or greater than {0}.',
                display_price_currency($minimum_withdrawal_amount)
            ));

            return $this->redirect(['action' => 'index']);
        }
        if ($data['amount'] > price_database_format($VdUser->earnings)) {
            if ($VdUser->earnings < $minimum_withdrawal_amount) {
                $this->Flash->error(__(
                    'Insufficient Balance'
                ));
            } else {
                $this->Flash->error(__(
                    'Withdraw amount should be equal or less than {0}.',
                    display_price_currency($VdUser->earnings)
                ));
            }
            return $this->redirect(['action' => 'index']);
        }

        $withdraw->method = $method;
        $withdraw->account = $account;
        $withdraw->type = "VdMarketing";
        $VdUser->earnings -= $amount_;
        $withdraw->publisher_earnings = price_database_format($amount_);
        $withdraw->referral_earnings = price_database_format(0);




        $withdraw = $Withdraws->patchEntity($withdraw, $data);
        if ($Withdraws->save($withdraw)) {
            // Rest publisher balance

            $VdUsers->save($VdUser);

            $queuedJobsTable = TableRegistry::getTableLocator()->get('Queue.QueuedJobs');
            $queuedJobsTable->createJob('Withdraw', ['id' => $withdraw->id]);


            $this->Flash->success(__('Your withdraw has been request and under review.'));
        } else {
            $this->Flash->error(__('Unable to request the withdraw.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    public function getData()
    {
        $AuthUser = $this->Auth->user('username');
        $user = $this->VdmarketingUsers->find()
            ->where(["user" => $AuthUser])->first();
        $Url = "https://vdmarketing.postaffiliatepro.com/scripts/qzcw1549?a_aid=lutorres&a_bid=75f7698b&data1=" . $user->user . "&data2=" . $user->token;
        $response = file_get_contents($Url);

        $data = json_decode($response, true);
    }
}
