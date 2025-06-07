<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Invoice[]|\Cake\Collection\CollectionInterface $invoices
 */
$this->assign('title', __('Manage Invoices'));
$this->assign('description', '');
$this->assign('content_title', __('Manage Invoices'));
$id = 0;
?>
<style>
    .btn-action1 {
        color: azure;
        background-color: green;
        margin-bottom: 20px;
        border-radius: 10px;
        width: 100%;
        box-shadow: 3px 8px 8px 1px rgb(22 22 22 / 82%);

    }
    .btn-action2 {
        color: azure;
        background-color: red;
        border-radius: 10px;
        width: 100%;
        box-shadow: 3px 8px 8px 1px rgb(22 22 22 / 82%);
    }
    .completed{
        color: azure;
        background-color: green;
        border-radius: 10px;
        height: 30px;
        text-align: center;
        box-shadow: 3px 8px 10px 3px rgb(0 128 0 / 81%);

    }
    .canceled{
        color: azure;
        background-color: red;
        border-radius: 10px;
        height: 30px;
        text-align: center;
        box-shadow: 3px 8px 10px 3px rgb(247 4 4 / 75%);

    }

</style>
<div class="box box-primary">
    <div class="box-body no-padding">

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <tr>
                    <th><?= __('Actions'); ?></th>
                    <th><?= __('ID'); ?></th>
                    <th><?= __('User ID'); ?></th>
                    <th><?= __('Username'); ?></th>
                    <th><?= __('Phone'); ?></th>
                    <th><?= __('Purchased Offer'); ?></th>
                    <th><?= __('Purchases'); ?></th>
                    <th><?= __('Amount'); ?></th>
                    <th><?= __('Fecha'); ?></th>



                </tr>

                <?php foreach ($markets as $market) : ?>

                    <tr data-action="confirm" data-id="<?= $market->id ?>">

                        <td><?= get_actions($market,$this->Url); ?></td>
                        <td><?= $id += 1; ?></td>
                        <td><?= $market->my_array["user_id"]; ?></td>
                        <td><?= $market->my_array["username"]; ?></td>
                        <td><?= $market->my_array["whatsapp"]; ?></td>
                        <td><?= $market->my_array["ofert_type"]; ?></td>
                        <td><?= $market->my_array["cantidad"]; ?></td>
                        <td><?= $market->my_array["total_price"]; ?></td>
                        <td><?= $market->created; ?></td>


                    </tr>
                <?php endforeach; ?>
                <?php  ?>
            </table>
        </div>

    </div><!-- /.box-body -->
</div>
<?php

function get_actions($market,$Url) {

    $confirmUrl =$Url->build(['action' => 'confirm', 'market_id' => $market->id]);
    $cancelUrl = $Url->build(['action' => 'cancel', 'market_id' => $market->id]);
    if($market->my_array['status']=="complete"){
        return "<div class ='completed'>Completed</div>";

    }
    if($market->my_array['status']=="canceled"){
        return "<div class ='canceled'>Canceled</div>";

    }
    return '<a href="' . $confirmUrl . '"><button class="btn-action1">Confirmar</button></a>' .
           '<a href="' . $cancelUrl . '"><button class="btn-action2">Cancel</button></a>';
}
?>