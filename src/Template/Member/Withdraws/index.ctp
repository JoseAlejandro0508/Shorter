<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Withdraw[]|\Cake\Collection\CollectionInterface $withdraws
 * @var \App\Model\Entity\User $user
 * @var mixed $pending_withdrawn
 * @var mixed $total_withdrawn
 */
$this->assign('title', __('Withdraw Funds'));
$this->assign('description', '');
$this->assign('content_title', __('Withdraw Funds'));
?>

<?php
$statuses = [
    1 => __('Approved'),
    2 => __('Pending'),
    3 => __('Complete'),
    4 => __('Cancelled'),
    5 => __('Returned'),
];

$withdrawal_methods = array_column_polyfill(get_withdrawal_methods(), 'name', 'id');
?>

<div class="row">
    <div class="col-sm-4">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?= display_price_currency($user->publisher_earnings + $user->referral_earnings); ?></h3>
                <p><?= __('Available Balance') ?></p>
            </div>
            <div class="icon"><i class="fa fa-money"></i></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?= display_price_currency($pending_withdrawn); ?></h3>
                <p><?= __('Pending Withdrawn') ?></p>
            </div>
            <div class="icon"><i class="fa fa-share"></i></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?= display_price_currency($total_withdrawn); ?></h3>
                <p><?= __('Total Withdraw') ?></p>
            </div>
            <div class="icon"><i class="fa fa-usd"></i></div>
        </div>
    </div>
</div>


<div class="box box-primary">
    <div class="box-body">
        <?php if ((bool)get_option('enable_withdraw', 1)) : ?>
            <div class="text-center">
                <button id="open-modal"><i class="fa fa-sign-out"></i><?= __('Withdraw') ?></button>

                <div id="withdrawal-modal" class="modal">
                    <div class="modal-content">
                        <span class="close-modal">&times;</span>
                        <h2><?= __('Withdraw') ?></h2>
                        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'request']]); ?>
                        <?= $this->Form->control('amount', [
                            'label' => 'Amount',
                            'type' => 'number',
                            'min' => floatval($min),
                            'required' => true,
                            'error' => [
                                'message' => 'The amount to be withdrawn must be at least ' . $minWithdrawalAmount,
                                'class' => 'error-message'
                            ]
                        ]); ?>
                        

                        <?= $this->Form->button(__('Confirm'), ['class' => 'btn btn-confirm']); ?>
                        <?= $this->Form->end(); ?>
                        <div class="button-group">
                            <button id="min-amount" class="btn"><?= __('Min') ?></button>
                            <button id="max-amount" class="btn"><?= __('Max') ?></button>
                        </div>
                    </div>
                </div>

                <hr>

                <p>
                    <?= __(
                        "When your account reaches the minimum amount or more, you may request your " .
                            "earnings by clicking the above button. The payment is then sent to your withdraw account during " .
                            "business days no longer than {0} days after requesting. Please do not contact us regarding " .
                            "payments before due dates.",
                        get_option('withdraw_days', 4)
                    ) ?>
                </p>

                <p>
                    <?= __(
                        'In order to receive your payments you need to fill your payment method and payment ID ' .
                            '<a href="{0}">here</a> if you haven\'t done so. You are also requested to fill all the required ' .
                            'fields in the Account Details section with accurate data.',
                        $this->Url->build(['controller' => 'Users', 'action' => 'profile', 'prefix' => 'member'])
                    ) ?>
                </p>

                <hr>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('id', __('ID')) ?></th>
                            <th><?= $this->Paginator->sort('created', __('Date')) ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= $this->Paginator->sort('publisher_earnings', __('Publisher Earnings')) ?></th>
                            <?php if ((bool)get_option('enable_referrals', 1)) : ?>
                                <th><?= $this->Paginator->sort('referral_earnings', __('Referral Earnings')) ?></th>
                            <?php endif; ?>
                            <th><?= __('Total Amount') ?></th>
                            <th><?= __('Withdrawal Method') ?></th>
                            <th><?= __('Withdrawal Account') ?></th>
                        </tr>
                    </thead>
                    <?php foreach ($withdraws as $withdraw) : ?>
                        <tr>
                            <td><?= $withdraw->id ?></td>
                            <td><?= display_date_timezone($withdraw->created); ?></td>
                            <td><?= $statuses[$withdraw->status] ?></td>
                            <td><?= display_price_currency($withdraw->publisher_earnings); ?></td>
                            <?php if ((bool)get_option('enable_referrals', 1)) : ?>
                                <td><?= display_price_currency($withdraw->referral_earnings); ?></td>
                            <?php endif; ?>
                            <td><?= display_price_currency($withdraw->amount); ?></td>
                            <td><?= (isset($withdrawal_methods[$withdraw->method])) ?
                                    $withdrawal_methods[$withdraw->method] : $withdraw->method ?></td>
                            <td><?= nl2br(h($withdraw->account)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php unset($withdraw); ?>
                </table>
            </div>

            <hr>

            <ul>
                <li><?= __("Pending: The payment is being checked by our team.") ?></li>
                <li><?= __("Approved: The payment has been approved and is waiting to be sent.") ?></li>
                <li><?= __("Complete: The payment has been successfully sent to your payment account.") ?></li>
                <li><?= __("Cancelled: The payment has been cancelled.") ?></li>
                <li><?= __("Returned: The payment has been returned to your account.") ?></li>
            </ul>
            </div><!-- /.box-body -->
    </div>

    <ul class="pagination">
        <?php
        $this->Paginator->setTemplates([
            'ellipsis' => '<li><a href="javascript: void(0)">...</a></li>',
        ]);

        if ($this->Paginator->hasPrev()) {
            echo $this->Paginator->prev('«');
        }

        echo $this->Paginator->numbers([
            'modulus' => 4,
            'first' => 2,
            'last' => 2,
        ]);

        if ($this->Paginator->hasNext()) {
            echo $this->Paginator->next('»');
        }
        ?>
    </ul>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #f2f2f2;
            /* Gris claro */
            margin: 15% auto;
            padding: 20px;
            border: 2px solid #007bff;
            /* Bordes azules */
            width: 90%;
            border-radius: 5px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .button-group {
            display: flex;
            justify-content: space-around;
            margin-bottom: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-confirm {
            background-color: #4CAF50;
            /* Verde */
            width: 100%;
        }

        #open-modal {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            /* Verde */
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        #min-amount {
            background-color: #f44336;
            /* Rojo */
        }

        #max-amount {
            background-color: #008CBA;
            /* Azul */
        }

        input[type=number] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
    </style>
    <script>
        document.getElementById('open-modal').addEventListener('click', function() {

            document.getElementById('withdrawal-modal').style.display = 'block';
        });

        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('withdrawal-modal').style.display = 'none';
        });

        // Ajustar el monto mínimo
        document.getElementById('min-amount').addEventListener('click', function() {
            event.stopPropagation(); 
            if(<?= floatval($min) ?> >  <?= floatval($max) ?>){
                document.querySelector('input[name="amount"]').value = 0;
                return 0;
            }
            document.querySelector('input[name="amount"]').value = <?= $min ?>;
        });

        // Ajustar el monto máximo
        document.getElementById('max-amount').addEventListener('click', function() {
            event.stopPropagation(); 
            document.querySelector('input[name="amount"]').value = <?= $max ?>;
        });

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            var modal = document.getElementById('withdrawal-modal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>