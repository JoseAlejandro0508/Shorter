<?php

namespace App\Controller\Member;

use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;

/**
 * @property \App\Model\Table\WithdrawsTable $Withdraws
 */
class WithdrawsController extends AppMemberController
{
    use MailerAwareTrait;

    public function index()

    {
        $user = $this->Withdraws->Users->get($this->Auth->user('id'));
        $withdrawal_methods = array_column_polyfill(get_withdrawal_methods(), 'amount', 'id');
        $minimum_withdrawal_amount = $withdrawal_methods[$user->withdrawal_method];
        $max_withdrawal_amount = $user->publisher_earnings + $user->referral_earnings;
        $query = $this->Withdraws->find()
            ->where(['user_id' => $this->Auth->user('id'),'type'=>'Acortador']);
        $withdraws = $this->paginate($query);

        $this->set('withdraws', $withdraws);

        $total_withdrawn = $this->Withdraws->find()
            ->select(['total' => 'SUM(amount)'])
            ->where([
                'user_id' => $this->Auth->user('id'),
                'status' => 3,
                'type'=>'Acortador'
            ])
            ->first();
        $this->set('total_withdrawn', $total_withdrawn->total);

        $pending_withdrawn = $this->Withdraws->find()
            ->select(['total' => 'SUM(amount)'])
            ->where([
                'user_id' => $this->Auth->user('id'),
                'status' => 2,
                'type'=>'Acortador'
            ])
            ->first();
        $this->set('pending_withdrawn', $pending_withdrawn->total);

        $user = $this->Withdraws->Users->get($this->Auth->user('id'));
        $this->set('user', $user);
        $this->set('min', $minimum_withdrawal_amount);
        $this->set('max', $max_withdrawal_amount);
    }

    public function request()
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        $amount_ = $this->request->getData('amount');

        $user = $this->Withdraws->Users->get($this->Auth->user('id'));

        $withdraw = $this->Withdraws->newEntity();
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
        if ($data['amount'] > price_database_format($user->publisher_earnings + $user->referral_earnings)) {
            if ($user->publisher_earnings + $user->referral_earnings < $minimum_withdrawal_amount) {
                $this->Flash->error(__(
                    'Insufficient Balance'
                ));

            } else {
                $this->Flash->error(__(
                    'Withdraw amount should be equal or less than {0}.',
                    display_price_currency($user->publisher_earnings + $user->referral_earnings)
                ));
            }
            return $this->redirect(['action' => 'index']);
        }

        $withdraw->method = $method;
        $withdraw->account = $account;

        $user->publisher_earnings -= $amount_;
        $withdraw->publisher_earnings = price_database_format($amount_);
        $withdraw->referral_earnings = price_database_format(0);
        if ($user->publisher_earnings < 0) {
            $withdraw->publisher_earnings = $amount_ + $user->publisher_earnings;
            $withdraw->referral_earnings = $user->publisher_earnings * -1;
            $user->referral_earnings += $user->publisher_earnings;
            $user->publisher_earnings = 0;
        }


        $withdraw = $this->Withdraws->patchEntity($withdraw, $data);
        if ($this->Withdraws->save($withdraw)) {
            // Rest publisher balance

            $this->Withdraws->Users->save($user);

            $queuedJobsTable = TableRegistry::getTableLocator()->get('Queue.QueuedJobs');
            $queuedJobsTable->createJob('Withdraw', ['id' => $withdraw->id]);

            if ((bool)get_option('alert_admin_new_withdrawal', 1)) {
                try {
                    $this->getMailer('Notification')->send('newWithdrawal', [$withdraw, $user]);
                } catch (\Exception $exception) {
                    \Cake\Log\Log::write('error', $exception->getMessage());
                }
            }

            $this->Flash->success(__('Your withdraw has been request and under review.'));
        } else {
            $this->Flash->error(__('Unable to request the withdraw.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
