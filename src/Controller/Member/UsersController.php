<?php

namespace App\Controller\Member;

use Cake\Mailer\MailerAwareTrait;
use Cake\I18n\Time;
use Cake\Http\Exception\NotFoundException;
use Cake\Cache\Cache;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\AnnouncementsTable $Announcements
 */
class UsersController extends AppMemberController
{
    use MailerAwareTrait;
    public function initialize()
    {
        parent::initialize();

        if (in_array($this->getRequest()->getParam('action'), ['getmarket'])) {
            //$this->getEventManager()->off($this->Csrf);
            $this->getEventManager()->off($this->Security);
        }
    }
    public function rankday()
    {

        date_default_timezone_set('America/Havana');
        $timestampActual = time();
        $auth_user_id = $this->Auth->user('id');
        $Options = TableRegistry::getTableLocator()->get('Options');
        $Users = TableRegistry::getTableLocator()->get('Users');
        $auth_user=$Users->get($auth_user_id);
        $options = $Options->find()->all();
        $settings = [];
        foreach ($options as $option) {
            $settings[$option->name] = [
                'id' => $option->id,
                'value' => $option->value,
            ];
        }


        $rankTime = [
            1 => (int)$settings["Rank1DPayedTime"]["value"],
            2 => (int)$settings["Rank2DPayedTime"]["value"],
            3 => (int)$settings["Rank3DPayedTime"]["value"]
        ];
        $last_record = Time::now();
        $first_record = user()->created;

        $year_month = [];

        $last_month = Time::now()->year($last_record->year)->month($last_record->month)->startOfMonth();
        $first_month = Time::now()->year($first_record->year)->month($first_record->month)->startOfMonth();

        while ($first_month <= $last_month) {
            $year_month[$last_month->format('Y-m')] = $last_month->i18nFormat('LLLL Y');

            $last_month->modify('-1 month');
        }

        $this->set('year_month', $year_month);

        $to_month = Time::now()->format('Y-m');

        $time = new Time($to_month);



        $current_time = $time->startOfMonth();

        $year = (int)$current_time->format('Y');
        $month = (int)$current_time->format('m');


        $time_zone = get_option('timezone', 'UTC');
        // Obtener la hora actual
        $check_ = Time::createFromDate($year, $month - 1, 01, $time_zone);

        $date1 = Time::createFromDate($year, $month - 1, 01, $time_zone)
            ->startOfMonth()
            ->i18nFormat('yyyy-MM-dd HH:mm:ss', 'UTC', 'en');
        /*$week_d1 = date('w', strtotime($date1));
        if (intval($week_d1) > 0) {
            $discount = intval($week_d1) * (-1);
            $date1 = strtotime($date1);
            $date1 = date('Y-m-d H:i:s', strtotime(strval($discount) . ' days', $date1));
        }*/
        $date2 = Time::createFromDate($year, $month, 01, $time_zone)
            ->endOfMonth()
            ->i18nFormat('yyyy-MM-dd HH:mm:ss', 'UTC', 'en');


        $connection = ConnectionManager::get('default');

        $time_zone_offset = Time::now($time_zone)->format('P');

        $users = $this->Users->find('all')->toArray();

        // Filtra los usuarios cuyo ID es "a1"


        $sql = "SELECT 
    Statistics.user_id,
    DATE_FORMAT(CONVERT_TZ(Statistics.created,'+00:00','" . $time_zone_offset . "'), '%Y-%m-%d') AS day,
    COUNT(CASE WHEN Statistics.publisher_earn > 0 THEN Statistics.id ELSE NULL END) AS count
    FROM 
    statistics Statistics 
    WHERE 
    Statistics.created BETWEEN :date1 AND :date2
    GROUP BY 
    Statistics.user_id, day;";
        /*  $sql = "SELECT 
    Statistics.user_id,
    DATE_FORMAT(CONVERT_TZ(Statistics.created,'+00:00','" . $time_zone_offset . "'), '%Y-%m-%d') AS day,
    COUNT(Statistics.id) AS count
    FROM 
    statistics Statistics 
    WHERE 
    Statistics.created BETWEEN :date1 AND :date2
    GROUP BY 
    Statistics.user_id, day;";*/



        $stmt = $connection->prepare($sql);
        $stmt->bindValue('date1', $date1, 'datetime');
        $stmt->bindValue('date2', $date2, 'datetime');
        $stmt->execute();

        $views_per_user = $stmt->fetchAll('assoc');




        // Obtén el último día de la semana actual (sábado)

        $ultimoDiaSemana = strtotime('yesterday');
        $inicioSemana = strtotime('today');


        $diaFinSemana = intval(date('d', $ultimoDiaSemana));
        $diaInicioSemana = intval(date('d', $inicioSemana));
        $user_views = [];

        foreach ($views_per_user as $view) {
            $user_id = $view['user_id'];
            $day = $view['day'];
            $fecha_array = explode("-", $day);
            $count = $view['count'];
            $date = strtotime($day);


            if (!isset($user_views[$user_id])) {
                $user_views[$user_id] = [];
                $user_views[$user_id]["y"] = 0;
                $user_views[$user_id]["t"] = 0;
            }


            if ($date >= $inicioSemana) {

                $user_views[$user_id]["t"] += intval($count);
            }
            if ($date >= $ultimoDiaSemana and $date < $inicioSemana) {

                $user_views[$user_id]["y"] += intval($count);
            }

            $user_views[$user_id][$day] = $count;


        }
        $views_y = [];
        $views_t = [];
        foreach ($user_views as $key => $value) {
            $views_y[$key] = $value["y"];
            $views_t[$key] = $value["t"];
        }

        uasort($views_y, function ($a, $b) {
            return $b <=> $a;
        });
        uasort($views_t, function ($a, $b) {
            return $b <=> $a;
        });

        uasort($users, function ($a, $b) use ($views_y) {
            $viewsA = $views_y[$a->id] ?? 0; // Si no existe, usa 0
            $viewsB =  $views_y[$b->id]?? 0;
            return $viewsB<=> $viewsA;
        });

        $posy = [];
        $posy_ = [];
        $count_ = 1;
        foreach ($users as $key => $value) {
            array_push($posy, $value);
            $posy_[$value->id] = strval($count_);
            $count_ += 1;
        }
        uasort($users, function ($a, $b) use ($views_t) {
            $viewsA = $views_t[$a->id] ?? 0; // Si no existe, usa 0
            $viewsB = $views_t[$b->id] ?? 0;
            return $viewsB <=> $viewsA; // Orden descendente
        });

        $pos = [];
        $pos_ = [];
        $count_ = 1;
        foreach ($users as $key => $value) {
            array_push($pos, $value);
            $pos_[$value->id] = strval($count_);
            $count_ += 1;
        }
        $diaDelMes = (int)date('j', $timestampActual);

        $Winner = false;

        if ($posy_[$auth_user_id] < 4) {

            $LastRankPay = $rankTime[$posy_[$auth_user_id]];
            if ($LastRankPay != $diaDelMes) {
                $Winner = true;
            }
        }
        $users= array_slice($users, 0, 20);



        $this->set('Winner', $Winner);
        $this->set('pos', $pos);
        $this->set('pos_', $pos_);
        $this->set('posy', $posy);
        $this->set('posy_', $posy_);


        // Calcula la diferencia en segundos
        $diferenciaSegundos = $inicioSemana + 3600 * 24 - $timestampActual;

        $data = json_encode(['month_temporizer' => $diferenciaSegundos]);
        $this->set('data_json', $data);

        $this->set('users', $users);
        $this->set('total_viewst', $views_t);
        $this->set('total_viewsy', $views_y);
        $this->set('authuser', $auth_user_id);
    }
    public function ClaimRankD()
    {

        date_default_timezone_set('America/Havana');
        $timestampActual = time();
        $auth_user_id = $this->Auth->user('id');
        $Options = TableRegistry::getTableLocator()->get('Options');
        $options = $Options->find()->all();
        $settings = [];
        foreach ($options as $option) {
            $settings[$option->name] = [
                'id' => $option->id,
                'value' => $option->value,
            ];
        }



        $last_record = Time::now();
        $first_record = user()->created;

        $year_month = [];

        $last_month = Time::now()->year($last_record->year)->month($last_record->month)->startOfMonth();
        $first_month = Time::now()->year($first_record->year)->month($first_record->month)->startOfMonth();

        while ($first_month <= $last_month) {
            $year_month[$last_month->format('Y-m')] = $last_month->i18nFormat('LLLL Y');

            $last_month->modify('-1 month');
        }

        $this->set('year_month', $year_month);

        $to_month = Time::now()->format('Y-m');

        $time = new Time($to_month);



        $current_time = $time->startOfMonth();

        $year = (int)$current_time->format('Y');
        $month = (int)$current_time->format('m');


        $time_zone = get_option('timezone', 'UTC');
        // Obtener la hora actual
        $check_ = Time::createFromDate($year, $month - 1, 01, $time_zone);

        $date1 = Time::createFromDate($year, $month - 1, 01, $time_zone)
            ->startOfMonth()
            ->i18nFormat('yyyy-MM-dd HH:mm:ss', 'UTC', 'en');
        /*$week_d1 = date('w', strtotime($date1));
        if (intval($week_d1) > 0) {
            $discount = intval($week_d1) * (-1);
            $date1 = strtotime($date1);
            $date1 = date('Y-m-d H:i:s', strtotime(strval($discount) . ' days', $date1));
        }*/
        $date2 = Time::createFromDate($year, $month, 01, $time_zone)
            ->endOfMonth()
            ->i18nFormat('yyyy-MM-dd HH:mm:ss', 'UTC', 'en');


        $connection = ConnectionManager::get('default');

        $time_zone_offset = Time::now($time_zone)->format('P');

        $users = $this->Users->find('all')->toArray();

        // Filtra los usuarios cuyo ID es "a1"


        $sql = "SELECT 
    Statistics.user_id,
    DATE_FORMAT(CONVERT_TZ(Statistics.created,'+00:00','" . $time_zone_offset . "'), '%Y-%m-%d') AS day,
    COUNT(CASE WHEN Statistics.publisher_earn > 0 THEN Statistics.id ELSE NULL END) AS count
    FROM 
    statistics Statistics 
    WHERE 
    Statistics.created BETWEEN :date1 AND :date2
    GROUP BY 
    Statistics.user_id, day;";
        /*  $sql = "SELECT 
    Statistics.user_id,
    DATE_FORMAT(CONVERT_TZ(Statistics.created,'+00:00','" . $time_zone_offset . "'), '%Y-%m-%d') AS day,
    COUNT(Statistics.id) AS count
    FROM 
    statistics Statistics 
    WHERE 
    Statistics.created BETWEEN :date1 AND :date2
    GROUP BY 
    Statistics.user_id, day;";*/



        $stmt = $connection->prepare($sql);
        $stmt->bindValue('date1', $date1, 'datetime');
        $stmt->bindValue('date2', $date2, 'datetime');
        $stmt->execute();

        $views_per_user = $stmt->fetchAll('assoc');




        // Obtén el último día de la semana actual (sábado)

        $ultimoDiaSemana = strtotime('yesterday');
        $inicioSemana = strtotime('today');


        $diaFinSemana = intval(date('d', $ultimoDiaSemana));
        $diaInicioSemana = intval(date('d', $inicioSemana));
        $user_views = [];

        foreach ($views_per_user as $view) {
            $user_id = $view['user_id'];
            $day = $view['day'];
            $fecha_array = explode("-", $day);
            $count = $view['count'];
            $date = strtotime($day);


            if (!isset($user_views[$user_id])) {
                $user_views[$user_id] = [];
                $user_views[$user_id]["y"] = 0;
                $user_views[$user_id]["t"] = 0;
            }


            if ($date >= $inicioSemana) {

                $user_views[$user_id]["t"] += intval($count);
            }
            if ($date >= $ultimoDiaSemana and $date < $inicioSemana) {

                $user_views[$user_id]["y"] += intval($count);
            }

            $user_views[$user_id][$day] = $count;


            if (intval($month) == intval($fecha_array[1])) {
                $user_views[$user_id]["total"] += intval($count);
            }
        }
        $views_y = [];
        $views_t = [];
        foreach ($user_views as $key => $value) {
            $views_y[$key] = $value["y"];
            $views_t[$key] = $value["t"];
        }

        uasort($views_y, function ($a, $b) {
            return $b <=> $a;
        });
        uasort($views_t, function ($a, $b) {
            return $b <=> $a;
        });

        uasort($users, function ($a, $b) use ($views_y) {
            $viewsA = $views_y[$a->id] ?? 0; // Si no existe, usa 0
            $viewsB =$views_y[$b->id] ?? 0;
            return $viewsB <=> $viewsA;
        });

        $posy = [];
        $posy_ = [];
        $count_ = 1;
        foreach ($users as $key => $value) {
            array_push($posy, $value);
            $posy_[$value->id] = strval($count_);
            $count_ += 1;
        }
        uasort($users, function ($a, $b) use ($views_t) {
            $viewsA = $views_t[$a->id] ?? 0; // Si no existe, usa 0
            $viewsB =$views_t[$b->id] ?? 0;
            return $viewsB <=> $viewsA;

        });

        $pos = [];
        $pos_ = [];
        $count_ = 1;
        foreach ($users as $key => $value) {
            array_push($pos, $value);
            $pos_[$value->id] = strval($count_);
            $count_ += 1;
        }
        $rankTime = [
            1 => $settings["Rank1DPayedTime"],
            2 => $settings["Rank2DPayedTime"],
            3 => $settings["Rank3DPayedTime"]
        ];
        $rankGift = [
            1 => (int)$settings["RankD1Gift"]["value"],
            2 => (int)$settings["RankD2Gift"]["value"],
            3 => (int)$settings["RankD3Gift"]["value"]
        ];
        $diaDelMes = (int)date('j', $timestampActual);


        if ($posy_[$auth_user_id] < 4 && isset($views_y[$auth_user_id]) && intval($views_y[$auth_user_id])>1000 ) {


            $LastRankPay = $rankTime[$posy_[$auth_user_id]]["value"];
            $RankPay = $rankGift[$posy_[$auth_user_id]];
            $user = $this->Users->get($this->Auth->user('id'));
            if ($LastRankPay != $diaDelMes) {

                $optionId = $rankTime[$posy_[$auth_user_id]]["id"];
                $optionEntity = $Options->get($optionId); // Cargar la entidad

                $optionEntity->value = $diaDelMes; // Asignar nuevo valor

                $Options->save($optionEntity);
                $user->publisher_earnings = price_database_format(floatval($RankPay) + floatval($user->publisher_earnings));
                $this->Users->save($user);
            }
        }
        return $this->redirect(['action' => 'rankday']);
    }

    public function cancel()
    {
        $user = $this->Users->get($this->Auth->user('id'));
        $market_tab = TableRegistry::getTableLocator()->get('Markets');
        $id = $this->request->getQuery('market_id');
        $market = $market_tab->getById($id);
        if (!isset($market->my_array['status'])) {
            $market->my_array['status'] = "pending";
        }
        if ($market->my_array['status'] == "pending") {
            $market->my_array['status'] = "canceled";
            $user->publisher_earnings = price_database_format(floatval($market->my_array["total_price"]) + floatval($user->publisher_earnings));
            $this->Users->save($user);
        }

        $market_tab->marketedit($market);
        $this->Flash->success('Successfully Canceled');
        return $this->redirect(['action' => 'orders']);
    }
    public function getmarket()
    {


        if ($this->request->is('post')) {
            $market_tab = TableRegistry::getTableLocator()->get('Markets');
            $Options = TableRegistry::getTableLocator()->get('Options');
            $option = $Options->find()->where(['name' => 'facebook_price'])->first();
            $fb_pric = floatval($option->value);
            $user = $this->Users->get($this->Auth->user('id'));
            $balance = $user->publisher_earnings + $user->referral_earnings;
            $data = $this->request->getData();
            $data_ = [];
            foreach ($data as $key => $value) {
                if ($key != '_Token') {
                    $data_[$key] = $value;
                }
            }

            $data_["total_price"] = floatval($data["cantidad"]) * $fb_pric;
            $data_["user_id"] = $user->id;
            $data_["username"] = $user->username;


            if (floatval($balance) > floatval($data["cantidad"]) * $fb_pric) {
                $actual_bal = floatval($user->publisher_earnings) - floatval($data["cantidad"]) * $fb_pric;
                if ($actual_bal < 0) {
                    $user->publisher_earnings = price_database_format($user->publisher_earnings - $user->publisher_earnings);
                    $user->referral_earnings = price_database_format($user->referral_earnings + $actual_bal);
                } else {
                    $user->publisher_earnings = price_database_format($actual_bal);
                }
                $market_tab->marketadd($data_);
                $this->Users->save($user);
                $this->Flash->success('SUCEFULL');
            } else {
                $this->Flash->error('INSUFICIENT BALANCE', ['element' => 'error']);
            }

            return $this->redirect(['action' => 'oferts']);
            //
            //$b=$market_tab  ->getAll();

            //$ofert=$data["ofert_type"];


            // Aquí puedes procesar los datos, por ejemplo, guardarlos en la base de datos
            // ... 

            // Redirecciona a otra vista o realiza otras acciones según sea necesario

            //return $this->redirect(['action' => 'index']);
        }
    }

    public function orders()
    {

        $market_tab = TableRegistry::getTableLocator()->get('Markets');
        $markets = $market_tab->getAll();

        $user = $this->Users->get($this->Auth->user('id'));
        $mymarkets = [];

        $uid = intval($user->id);
        foreach ($markets as $value) {

            $mark_uid = $value->my_array["user_id"];

            if ($mark_uid == $uid) {

                array_push($mymarkets, $value);
            } else {
                $a = 1;
            }
        }
        $this->set('mymarkets', $mymarkets);
    }

    public function oferts()
    {
        $Options = TableRegistry::getTableLocator()->get('Options');
        $options = $Options->find()->all();
        $settings = [];
        foreach ($options as $option) {
            $settings[$option->name] = [
                'id' => $option->id,
                'value' => $option->value,
            ];
        }

        // Obtener la conexión a la base de datos
        $connection = ConnectionManager::get('default');
        $Statistics = TableRegistry::getTableLocator()->get('Statistics');
        // Obtener todos los usuarios
        $users = $this->Users->find('all')->toArray();

        // Consulta para obtener la cantidad total de vistas por cada usuario
        $sql = "SELECT 
                  user_id, 
                  COUNT(*) AS view_count 
                FROM 
                  statistics 
                WHERE 
                  publisher_earn > 0 
                GROUP BY 
                  user_id
                ORDER BY 
                  view_count DESC"; // Ordenar por cantidad de vistas en orden descendente

        // Preparar y ejecutar la consulta
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $views_data = $stmt->fetchAll('assoc');

        // Almacenar la cantidad total de vistas en un array
        $total_views_per_user = [];
        foreach ($views_data as $data) {
            $total_views_per_user[$data['user_id']] = (int)$data['view_count'];
        }

        // Asegurarse de que cada usuario tenga una entrada, inicializándola a 0 si no tiene vistas
        foreach ($users as $user) {
            if (!isset($total_views_per_user[$user->id])) {
                $total_views_per_user[$user->id] = 0;
            }
        }

        // Ordenar los usuarios por cantidad de vistas en orden descendente
        uasort($users, function ($a, $b) use ($total_views_per_user) {
            return $total_views_per_user[$b->id] <=> $total_views_per_user[$a->id];
        });

        // Pasar a la vista
        $this->set('users', $users);

        $this->set('setting', $settings);
    }

    public function rank()
    {
        // Obtener la conexión a la base de datos
        $connection = ConnectionManager::get('default');
        $Statistics = TableRegistry::getTableLocator()->get('Statistics');
        // Obtener todos los usuarios
        $users = $this->Users->find('all')->toArray();

        // Consulta para obtener la cantidad total de vistas por cada usuario
        $sql = "SELECT 
                  user_id, 
                  COUNT(*) AS view_count 
                FROM 
                  statistics 
                WHERE 
                  publisher_earn > 0 
                GROUP BY 
                  user_id
                ORDER BY 
                  view_count DESC "; // Ordenar por cantidad de vistas en orden descendente

        // Preparar y ejecutar la consulta
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $views_data = $stmt->fetchAll('assoc');

        // Almacenar la cantidad total de vistas en un array
        $total_views_per_user = [];
        foreach ($views_data as $data) {
            $total_views_per_user[$data['user_id']] = (int)$data['view_count'];
        }

        // Asegurarse de que cada usuario tenga una entrada, inicializándola a 0 si no tiene vistas
        foreach ($users as $user) {
            if (!isset($total_views_per_user[$user->id])) {
                $total_views_per_user[$user->id] = 0;
            }
        }

        // Ordenar los usuarios por cantidad de vistas en orden descendente
        uasort($users, function ($a, $b) use ($total_views_per_user) {
            $viewsA =$total_views_per_user[$a->id] ?? 0; // Si no existe, usa 0
            $viewsB =  $total_views_per_user[$b->id] ?? 0;
            return $viewsB <=>$viewsA;
        });
        $users= array_slice($users, 0, 20);

        // Pasar a la vista
        $this->set('users', $users);
        $this->set('total_views_per_user', $total_views_per_user);
    }
    public function ranksem()
    {

        date_default_timezone_set('America/Havana');
        $auth_user_id = $this->Auth->user('id');

        $last_record = Time::now();
        $first_record = user()->created;

        $year_month = [];

        $last_month = Time::now()->year($last_record->year)->month($last_record->month)->startOfMonth();
        $first_month = Time::now()->year($first_record->year)->month($first_record->month)->startOfMonth();

        while ($first_month <= $last_month) {
            $year_month[$last_month->format('Y-m')] = $last_month->i18nFormat('LLLL Y');

            $last_month->modify('-1 month');
        }

        $this->set('year_month', $year_month);

        $to_month = Time::now()->format('Y-m');

        $time = new Time($to_month);



        $current_time = $time->startOfMonth();

        $year = (int)$current_time->format('Y');
        $month = (int)$current_time->format('m');



        $time_zone = get_option('timezone', 'UTC');

        // Obtener la hora actual
        $check_ = Time::createFromDate($year, $month, 01, $time_zone);

        $date1 = Time::createFromDate($year, $month - 1, 01, $time_zone)
            ->startOfMonth()
            ->i18nFormat('yyyy-MM-dd HH:mm:ss', 'UTC', 'en');
        /* $week_d1 = date('w', strtotime($date1));
        if (intval($week_d1) > 0) {
            $discount = intval($week_d1) * (-1);
            $date1 = strtotime($date1);
            $date1 = date('Y-m-d H:i:s', strtotime(strval($discount) . ' days', $date1));
        }*/
        $date2 = Time::createFromDate($year, $month, 01, $time_zone)
            ->endOfMonth()
            ->i18nFormat('yyyy-MM-dd HH:mm:ss', 'UTC', 'en');


        $connection = ConnectionManager::get('default');

        $time_zone_offset = Time::now($time_zone)->format('P');

        $users = $this->Users->find('all')->toArray();

        // Filtra los usuarios cuyo ID es "a1"


        $sql = "SELECT 
    Statistics.user_id,
    DATE_FORMAT(CONVERT_TZ(Statistics.created,'+00:00','" . $time_zone_offset . "'), '%Y-%m-%d') AS day,
    COUNT(CASE WHEN Statistics.publisher_earn > 0 THEN Statistics.id ELSE NULL END) AS count
FROM 
    statistics Statistics 
WHERE 
    Statistics.created BETWEEN :date1 AND :date2
GROUP BY 
    Statistics.user_id, day";
        /*  $sql = "SELECT 
    Statistics.user_id,
    DATE_FORMAT(CONVERT_TZ(Statistics.created,'+00:00','" . $time_zone_offset . "'), '%Y-%m-%d') AS day,
    COUNT(Statistics.id) AS count
FROM 
    statistics Statistics 
WHERE 
    Statistics.created BETWEEN :date1 AND :date2
GROUP BY 
    Statistics.user_id, day;";*/



        $stmt = $connection->prepare($sql);
        $stmt->bindValue('date1', $date1, 'datetime');
        $stmt->bindValue('date2', $date2, 'datetime');
        $stmt->execute();

        $views_per_user = $stmt->fetchAll('assoc');


        $timestampActual = time();

        // Obtén el último día de la semana actual (sábado)

        $ultimoDiaSemana = strtotime('this Sunday');
        // $this->Flash->success(__($ultimoDiaSemana));
        $inicioSemana = strtotime("last Sunday");

        // Define la fecha objetivo como timestamp (último día de la semana actual, 23:59:59)
        $fechaObjetivo = strtotime(date('Y-m-d', $ultimoDiaSemana) . ' 23:59:59');


        //$diaFinSemana = intval(date('d', $ultimoDiaSemana));
        //$diaInicioSemana = intval(date('d', $inicioSemana));
        $user_views = [];

        // if ($diaInicioSemana >$diaFinSemana) {
        // $diaFinSemana+=30;   
        //    }
        foreach ($views_per_user as $view) {
            $user_id = $view['user_id'];
            $day = $view['day'];
            $fecha_array = explode("-", $day);
            // $this->Flash->success(__(strtotime($day)));
            // $this->Flash->success(__($inicioSemana));
            // $this->Flash->success(__($ultimoDiaSemana));
            $tiempo_ = strtotime($day);

            $count = $view['count'];
            $date_ = Time::createFromDate(intval($fecha_array[0]), intval($fecha_array[1]), intval($fecha_array[2]), $time_zone);
            $dayOfWeek = intval(date('w', strtotime($date_)));



            if (!isset($user_views[$user_id])) {
                $user_views[$user_id] = [];
                $user_views[$user_id]["total"] = 0;
                $user_views[$user_id]["sem"] = 0;
                $user_views[$user_id]["last"] = 0;
            }


            if ($tiempo_ > $inicioSemana and $tiempo_ <= $ultimoDiaSemana) {

                $user_views[$user_id]["sem"] += intval($count);
            }
            if ($tiempo_ <= $inicioSemana and $tiempo_ > $inicioSemana - 3600 * 24 * 7) {

                $user_views[$user_id]["last"] += intval($count);
            }

            $user_views[$user_id][$day] = $count;


            if (intval($month) == intval($fecha_array[1])) {
                $user_views[$user_id]["total"] += intval($count);
            }
        }
        $views_sem = [];
        $views_men = [];
        $views_last = [];
        foreach ($user_views as $key => $value) {
            $views_sem[$key] = $value["sem"];
            $views_men[$key] = $value["total"];
            $views_last[$key] = $value["last"];
        }
        uasort($views_last, function ($a, $b) {
            return $b <=> $a;
        });
        uasort($views_sem, function ($a, $b) {
            return $b <=> $a;
        });
        uasort($views_men, function ($a, $b) {
            return $b <=> $a;
        });
        uasort($users, function ($a, $b) use ($views_last) {
            $viewsA = $views_last[$a->id] ?? 0; // Si no existe, usa 0
            $viewsB =$views_last[$b->id] ?? 0;
            return $viewsB <=> $viewsA;
        });
        $poslast = [];
        $pos_last = [];
        $count_ = 1;
        foreach ($users as $key => $value) {
            array_push($poslast, $value);
            $pos_last[$value->id] = strval($count_);
            $count_ += 1;
        }

        uasort($users, function ($a, $b) use ($views_sem) {
            $viewsA =  $views_sem[$a->id] ?? 0; // Si no existe, usa 0
            $viewsB =$views_sem[$b->id] ?? 0;
            return $viewsB <=> $viewsA;
        });
        $pos = [];
        $pos_ = [];
        $count_ = 1;
        foreach ($users as $key => $value) {
            array_push($pos, $value);
            $pos_[$value->id] = strval($count_);
            $count_ += 1;
        }
        
        $users= array_slice($users, 0, 20);


        $this->set('pos', $pos);
        $this->set('pos_', $pos_);
        $this->set('poslast', $poslast);
        $this->set('pos_last', $pos_last);



        // Calcula la diferencia en segundos
        $diferenciaSegundos = $fechaObjetivo - $timestampActual;

        $data = json_encode(['month_temporizer' => $diferenciaSegundos]);
        $this->set('data_json', $data);

        $this->set('users', $users);
        $this->set('total_views_per_user', $views_sem);
        $this->set('total_views_per_userlast', $views_last);
    }

    public function rankmen()

    {
        date_default_timezone_set('America/Havana');
        $auth_user_id = $this->Auth->user('id');

        $last_record = Time::now();
        $first_record = user()->created;

        $year_month = [];

        $last_month = Time::now()->year($last_record->year)->month($last_record->month)->startOfMonth();
        $first_month = Time::now()->year($first_record->year)->month($first_record->month)->startOfMonth();

        while ($first_month <= $last_month) {
            $year_month[$last_month->format('Y-m')] = $last_month->i18nFormat('LLLL Y');

            $last_month->modify('-1 month');
        }

        $this->set('year_month', $year_month);

        $to_month = Time::now()->format('Y-m');

        $time = new Time($to_month);



        $current_time = $time->startOfMonth();

        $year = (int)$current_time->format('Y');
        $month = (int)$current_time->format('m');


        $time_zone = get_option('timezone', 'UTC');
        // Obtener la hora actual
        $check_ = Time::createFromDate($year, $month, 01, $time_zone);

        $date1 = Time::createFromDate($year, $month, 01, $time_zone)
            ->startOfMonth()
            ->i18nFormat('yyyy-MM-dd HH:mm:ss', 'UTC', 'en');
        $week_d1 = date('w', strtotime($date1));
        if (intval($week_d1) > 0) {
            $discount = intval($week_d1) * (-1);
            $date1 = strtotime($date1);
            $date1 = date('Y-m-d H:i:s', strtotime(strval($discount) . ' days', $date1));
        }
        $date2 = Time::createFromDate($year, $month, 01, $time_zone)
            ->endOfMonth()
            ->i18nFormat('yyyy-MM-dd HH:mm:ss', 'UTC', 'en');

        $connection = ConnectionManager::get('default');

        $time_zone_offset = Time::now($time_zone)->format('P');

        $users = $this->Users->find('all')->toArray();

        // Filtra los usuarios cuyo ID es "a1"


        $sql = "SELECT 
    Statistics.user_id,
    DATE_FORMAT(CONVERT_TZ(Statistics.created,'+00:00','" . $time_zone_offset . "'), '%Y-%m-%d') AS day,
    COUNT(CASE WHEN Statistics.publisher_earn > 0 THEN Statistics.id ELSE NULL END) AS count
FROM 
    statistics Statistics 
WHERE 
    Statistics.created BETWEEN :date1 AND :date2
GROUP BY 
    Statistics.user_id, day;";
        /*$sql = "SELECT 
    Statistics.user_id,
    DATE_FORMAT(CONVERT_TZ(Statistics.created,'+00:00','" . $time_zone_offset . "'), '%Y-%m-%d') AS day,
    COUNT(Statistics.id) AS count
FROM 
    statistics Statistics 
WHERE 
    Statistics.created BETWEEN :date1 AND :date2
GROUP BY 
    Statistics.user_id, day;";*/



        $stmt = $connection->prepare($sql);
        $stmt->bindValue('date1', $date1, 'datetime');
        $stmt->bindValue('date2', $date2, 'datetime');
        $stmt->execute();

        $views_per_user = $stmt->fetchAll('assoc');


        $user_views = [];

        foreach ($views_per_user as $view) {
            $user_id = $view['user_id'];
            $day = $view['day'];
            $fecha_array = explode("-", $day);
            $count = $view['count'];
            $date_ = Time::createFromDate(intval($fecha_array[0]), intval($fecha_array[1]), intval($fecha_array[2]), $time_zone);
            $dayOfWeek = intval(date('w', strtotime($date_)));



            if (!isset($user_views[$user_id])) {
                $user_views[$user_id] = [];
                $user_views[$user_id]["total"] = 0;
                $user_views[$user_id]["sem"] = 0;
            }

            if ($dayOfWeek == 0) {
                $user_views[$user_id]["sem"] = 0;
            }
            $user_views[$user_id][$day] = $count;

            $user_views[$user_id]["sem"] += intval($count);
            if (intval($month) == intval($fecha_array[1])) {
                $user_views[$user_id]["total"] += intval($count);
            }
        }
        $views_sem = [];
        $views_men = [];
        foreach ($user_views as $key => $value) {
            $views_sem[$key] = $value["sem"];
            $views_men[$key] = $value["total"];
        }

        uasort($views_sem, function ($a, $b) {
            return $b <=> $a;
        });
        uasort($views_men, function ($a, $b) {
            return $b <=> $a;
        });

        uasort($users, function ($a, $b) use ($views_men) {
            $viewsA = $views_men[$a->id] ?? 0; // Si no existe, usa 0
            $viewsB = $views_men[$b->id] ?? 0;
            return $viewsB <=> $viewsA;
        });
        $pos = [];
        $pos_ = [];
        $count_ = 1;
        foreach ($users as $key => $value) {
            array_push($pos, $value);
            $pos_[$value->id] = strval($count_);
            $count_ += 1;
        }
        $users= array_slice($users, 0, 20);

        $timestampActual = time();

        // Obtén el último día del mes actual
        $ultimoDiaMes = date('t');

        // Define la fecha objetivo como timestamp (último día del mes actual, 23:59:59)
        $fechaObjetivo = strtotime(date('Y-m-' . $ultimoDiaMes . ' 23:59:59'));

        // Calcula la diferencia en segundos
        $diferenciaSegundos = $fechaObjetivo - $timestampActual;


        $data = json_encode(['month_temporizer' => $diferenciaSegundos]);
        $this->set('data_json', $data);

        $this->set('users', $users);
        $this->set('total_views_per_user', $views_men);
        $this->set('pos', $pos);
        $this->set('pos_', $pos_);
    }
    public function ranklinks()

    {
        $Users = TableRegistry::getTableLocator()->get('Users');


        $popularLinks = $Users->Statistics->find()
            ->contain(['Links', 'Links.Users'])
            ->select([
                'Links.id',
                'Links.alias',
                'Links.url',
                'Links.title',
                'Links.domain',
                'Links.created',
                'Links.user_id',
                'Users.username',

                'views' => "count(case when Statistics.publisher_earn > 0 then Statistics.publisher_earn end)",
                'publisher_earnings' => 'SUM(Statistics.publisher_earn)'
            ])

            ->order(['views' => 'DESC'])

            ->group('Statistics.link_id')

            ->toArray();

        $this->set('rank', $popularLinks);
    }
    public function dashboard()
    {
        $auth_user_id = $this->Auth->user('id');

        $last_record = Time::now();
        $first_record = user()->created;

        $year_month = [];

        $last_month = Time::now()->year($last_record->year)->month($last_record->month)->startOfMonth();
        $first_month = Time::now()->year($first_record->year)->month($first_record->month)->startOfMonth();

        while ($first_month <= $last_month) {
            $year_month[$last_month->format('Y-m')] = $last_month->i18nFormat('LLLL Y');

            $last_month->modify('-1 month');
        }

        $this->set('year_month', $year_month);

        $to_month = Time::now()->format('Y-m');
        if (
            $this->getRequest()->getQuery('month') &&
            array_key_exists($this->getRequest()->getQuery('month'), $year_month)
        ) {
            $to_month = explode('-', $this->getRequest()->getQuery('month'));
            $year = (int)$to_month[0];
            $month = (int)$to_month[1];
        } else {
            $time = new Time($to_month);
            $current_time = $time->startOfMonth();

            $year = (int)$current_time->format('Y');
            $month = (int)$current_time->format('m');
        }

        $time_zone = get_option('timezone', 'UTC');
        $date1 = Time::createFromDate($year, $month, 01, $time_zone)
            ->startOfMonth()
            ->i18nFormat('yyyy-MM-dd HH:mm:ss', 'UTC', 'en');
        $date2 = Time::createFromDate($year, $month, 01, $time_zone)
            ->endOfMonth()
            ->i18nFormat('yyyy-MM-dd HH:mm:ss', 'UTC', 'en');

        $connection = ConnectionManager::get('default');

        $time_zone_offset = Time::now($time_zone)->format('P');

        $CurrentMonthDays = Cache::read('currentMonthDays_' . $auth_user_id . '_' . $date1 . '_' . $date2, '15min');
        if ($CurrentMonthDays === false) {
            $sql = "SELECT 
                  CASE
                    WHEN Statistics.publisher_earn > 0
                    THEN
                      DATE_FORMAT(CONVERT_TZ(Statistics.created,'+00:00','" . $time_zone_offset . "'), '%Y-%m-%d')
                  END  AS `day`,
                  CASE
                    WHEN Statistics.publisher_earn > 0
                    THEN
                      COUNT(Statistics.id)
                  END AS `count`,
                  CASE
                    WHEN Statistics.publisher_earn > 0
                    THEN
                      SUM(Statistics.publisher_earn)
                  END AS `publisher_earnings`
                FROM 
                  statistics Statistics 
                WHERE 
                  (
                    Statistics.created BETWEEN :date1 AND :date2 
                    AND Statistics.user_id = {$auth_user_id}
                  ) 
                GROUP BY 
                  day";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('date1', $date1, 'datetime');
            $stmt->bindValue('date2', $date2, 'datetime');
            $stmt->execute();
            $views_publisher = $stmt->fetchAll('assoc');

            $sql = "SELECT 
                  CASE
                    WHEN Statistics.referral_earn > 0
                    THEN
                      DATE_FORMAT(CONVERT_TZ(Statistics.created,'+00:00','" . $time_zone_offset . "'), '%Y-%m-%d')
                  END  AS `day`,
                  CASE
                    WHEN Statistics.referral_earn > 0
                    THEN
                      SUM(Statistics.referral_earn)
                  END AS `referral_earnings`
                FROM 
                  statistics Statistics 
                WHERE 
                  (
                    Statistics.created BETWEEN :date1 AND :date2 
                    AND Statistics.referral_id = {$auth_user_id}
                  ) 
                GROUP BY 
                  day";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('date1', $date1, 'datetime');
            $stmt->bindValue('date2', $date2, 'datetime');
            $stmt->execute();
            $views_referral = $stmt->fetchAll('assoc');

            $CurrentMonthDays = [];

            $targetTime = Time::createFromDate($year, $month, 01)->startOfMonth();

            for ($i = 1; $i <= $targetTime->format('t'); $i++) {
                $CurrentMonthDays[$year . "-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" .
                    str_pad($i, 2, '0', STR_PAD_LEFT)] = [
                    'view' => 0,
                    'publisher_earnings' => 0,
                    'referral_earnings' => 0,
                ];
            }

            foreach ($views_publisher as $view) {
                if (!$view['day']) {
                    continue;
                }

                $day = $view['day'];
                $CurrentMonthDays[$day]['view'] = $view['count'];
                $CurrentMonthDays[$day]['publisher_earnings'] = $view['publisher_earnings'];
            }
            unset($view);
            foreach ($views_referral as $view) {
                if (!$view['day']) {
                    continue;
                }

                $day = $view['day'];
                $CurrentMonthDays[$day]['referral_earnings'] = $view['referral_earnings'];
            }
            unset($view);

            if ((bool)get_option('cache_member_statistics', 1)) {
                Cache::write(
                    'currentMonthDays_' . $auth_user_id . '_' . $date1 . '_' . $date2,
                    $CurrentMonthDays,
                    '15min'
                );
            }
        }
        $this->set('CurrentMonthDays', $CurrentMonthDays);

        $this->set('total_views', array_sum(array_column_polyfill($CurrentMonthDays, 'view')));
        $this->set('total_earnings', array_sum(array_column_polyfill($CurrentMonthDays, 'publisher_earnings')));
        $this->set('referral_earnings', array_sum(array_column_polyfill($CurrentMonthDays, 'referral_earnings')));


        /* $popularLinks = Cache::read('popularLinks_' . $this->Auth->user('id').'_'.$date1.'_'.$date2, '15min');
        if ($popularLinks === false) {
            $popularLinks = $this->Users->Statistics->find()
                ->contain(['Links'])
                ->select([
                    'Links.alias',
                    'Links.url',
                    'Links.title',
                    'Links.domain',
                    'Links.created',
                    'views' => "count(case when Statistics.publisher_earn > 0 then Statistics.publisher_earn end)",
                    'publisher_earnings' => 'SUM(Statistics.publisher_earn)'
                ])
                ->where([
                    "Statistics.created BETWEEN :date1 AND :date2",
                    'Statistics.user_id' => $this->Auth->user('id')
                ])
                ->order(['views' => 'DESC'])
                ->bind(':date1', $date1, 'datetime')
                ->bind(':date2', $date2, 'datetime')
                ->limit(10)
                ->group('Statistics.link_id')
                ->toArray();
            Cache::write('popularLinks_' . $this->Auth->user('id').'_'.$date1.'_'.$date2, $popularLinks, '15min');
        }

        $this->set('popularLinks', $popularLinks);
        */



        $this->loadModel('Announcements');

        $announcements = $this->Announcements->find()
            ->where(['Announcements.published' => 1])
            ->order(['Announcements.id DESC'])
            ->limit(3)
            ->toArray();
        $this->set('announcements', $announcements);
    }

    public function referrals()
    {
        if ((bool)get_option('enable_referrals', 1) === false) {
            throw new NotFoundException(__('Invalid request'));
        }
        $query = $this->Users->find()
            ->where(['referred_by' => $this->Auth->user('id')]);
        $referrals = $this->paginate($query);

        $this->set('referrals', $referrals);
    }

    public function profile()
    {
        $user = $this->Users->find()->contain(['Plans'])->where(['Users.id' => $this->Auth->user('id')])->first();

        if ($this->getRequest()->is(['post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->getRequest()->data);
            //debug($user->errors());
            if ($this->Users->save($user)) {
                if ($this->Auth->user('id') === $user->id) {
                    $data = $user->toArray();
                    unset($data['password']);

                    $this->Auth->setUser($data);
                }
                $this->Flash->success(__('Profile has been updated'));
                $this->redirect(['action' => 'profile']);
            } else {
                $this->Flash->error(__('Oops! There are mistakes in the form. Please make the correction.'));
            }
        }
        unset($user->password);
        $this->set('user', $user);
    }

    public function plans()
    {
        if ((bool)get_option('enable_premium_membership') === false) {
            throw new NotFoundException(__('404 Not Found'));
        }

        $user = $this->Users->findById($this->Auth->user('id'))->contain(['Plans'])->first();
        $this->set('user', $user);

        $plans = $this->Users->plans->find()->where(['enable' => 1, 'hidden' => 0]);
        $this->set('plans', $plans);
    }

    public function payPlan($id = null, $period = null)
    {
        if ((bool)get_option('enable_premium_membership') === false) {
            throw new NotFoundException(__('404 Not Found'));
        }

        $this->getRequest()->allowMethod(['post']);

        if (!$id || !$period) {
            throw new NotFoundException(__('Invalid request'));
        }

        $plan = $this->Users->Plans->findById($id)->first();

        $amount = $plan->yearly_price;
        $period_name = __("Yearly");
        if ($period === 'm') {
            $amount = $plan->monthly_price;
            $period_name = __("Monthly");
        }

        $data = [
            'status' => 2, //Unpaid Invoice
            'user_id' => $this->Auth->user('id'),
            'description' => __("{0} Premium Membership: {1}", [$period_name, $plan->title]),
            'type' => 1, //Plan Invoice
            'rel_id' => $plan->id, //Plan Id
            'payment_method' => '',
            'amount' => price_database_format($amount),
            'data' => serialize([
                'payment_period' => $period,
            ]),
        ];

        $invoice = $this->Users->Invoices->newEntity($data);

        if ($this->Users->Invoices->save($invoice)) {
            if ((bool)get_option('alert_admin_created_invoice', 0)) {
                $this->getMailer('Notification')->send('newInvoice', [$invoice, $this->logged_user]);
            }

            $this->Flash->success(__('An invoice with id: {0} has been generated.', $invoice->id));

            return $this->redirect(['controller' => 'Invoices', 'action' => 'view', $invoice->id]);
        }
    }

    public function changeEmail($username = null, $key = null)
    {
        if (!$username && !$key) {
            $user = $this->Users->findById($this->Auth->user('id'))->first();

            if ($this->getRequest()->is(['post', 'put'])) {
                $uuid = \Cake\Utility\Text::uuid();

                $user->activation_key = \Cake\Utility\Security::hash($uuid, 'sha1', true);

                $user = $this->Users->patchEntity($user, $this->getRequest()->data, ['validate' => 'changEemail']);

                if ($this->Users->save($user)) {
                    // Send rest email
                    $this->getMailer('User')->send('changeEmail', [$user]);

                    $this->Flash->success(__('Kindly check your email to confirm it.'));

                    $this->redirect(['action' => 'changeEmail']);
                } else {
                    $this->Flash->error(__('Oops! There are mistakes in the form. Please make the correction.'));
                }
            }
            $this->set('user', $user);
        } else {
            $user = $this->Users->find('all')
                ->contain(['Plans'])
                ->where([
                    'Users.status' => 1,
                    'Users.username' => $username,
                    'Users.activation_key' => $key,
                ])
                ->first();

            if (!$user) {
                $this->Flash->error(__('Invalid Activation.'));

                return $this->redirect(['action' => 'changeEmail']);
            }

            $user->email = $user->temp_email;
            $user->temp_email = '';
            $user->activation_key = '';

            if ($this->Users->save($user)) {
                if ($this->Auth->user('id') === $user->id) {
                    $data = $user->toArray();
                    unset($data['password']);

                    $this->Auth->setUser($data);
                }
                $this->Flash->success(__('Your email has been confirmed.'));

                return $this->redirect(['action' => 'signin', 'prefix' => 'auth']);
            } else {
                $this->Flash->error(__('Unable to confirm your email.'));

                return $this->redirect(['action' => 'changeEmail']);
            }
        }
    }

    public function changePassword()
    {
        $user = $this->Users->findById($this->Auth->user('id'))->first();

        if ($this->getRequest()->is(['post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->getRequest()->data, ['validate' => 'changePassword']);
            //debug($user->errors());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Password has been updated'));
                $this->redirect(['action' => 'changePassword']);
            } else {
                $this->Flash->error(__('Oops! There are mistakes in the form. Please make the correction.'));
            }
        }
        unset($user->password);
        $this->set('user', $user);
    }
}
