<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Link[]|\Cake\Collection\CollectionInterface $links
 */
?>
<?php
$this->assign('title', __('Administrar Conversiones'));
$this->assign('description', '');
$this->assign('content_title', __('Administrar Conversiones'));
?>

<div class="box box-solid">
    <div class="box-body">
        <?php
        $base_url = ['controller' => 'VdMarketing', 'action' => 'logs'];

        echo $this->Form->create(null, [
            'url' => $base_url,
            'class' => 'form-inline',
        ]);
        ?>

        <?=
        $this->Form->control('Filter.id', [
            'label' => false,
            'class' => 'form-control',
            'type' => 'text',
            'size' => 0,
            'placeholder' => __('Id'),
        ]);
        ?>

        <?=
        $this->Form->control('Filter.user', [
            'label' => false,
            'class' => 'form-control',
            'type' => 'text',
            'size' => 0,
            'placeholder' => __('User'),
        ]);
        ?>

        <?=
        $this->Form->control('Filter.token', [
            'label' => false,
            'class' => 'form-control',
            'type' => 'text',
            'size' => 10,
            'placeholder' => __('Token'),
        ]);
        ?>





        <?= $this->Form->button(__('Filter'), ['class' => 'btn btn-default btn-sm']); ?>



        <?= $this->Form->end(); ?>

    </div>
</div>

<div class="box box-primary">
    <div class="box-body no-padding">
        <div class="table-responsive">

            <table class="table table-hover table-striped">
                <tr>
                    <th>Id</th>
                    <th style="width:150px;"><?= __('User') ?></th>
                    <th><?= __('Token') ?></th>
                    <th><?= __('User Earn'); ?></th>
                    <th><?= __('Real Earn'); ?></th>
                    <th><?= __('Created') ?></th>

                </tr>

                <?php foreach ($logs as $log) : ?>
                    <tr>
                        <td>
                            <?= $log->id ?>
                        </td>
                        <td>
                            <?= $log->user ?>

                        </td>
                        <td>
                            <?= $log->token ?>
                        </td>
                        <td>
                            <?php
                            $d = json_decode($log->info)->shortearn;
                            echo($d);

                            ?>
                        </td>
                        <td>
                            <?php
                            $d = json_decode($log->info)->profit;
                            echo($d);
                            ?>
                        </td>

                        <td>
                            <?= display_date_timezone($log->created); ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </table>
            <?= $this->Form->end(); ?>
        </div>
    </div>
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

<?php $this->start('scriptBottom'); ?>
<script>
    $('#select-all').change(function() {
        $('.allcheckbox').prop('checked', $(this).prop('checked'));
    });
    $('.allcheckbox').change(function() {
        if ($(this).prop('checked') == false) {
            $('#select-all').prop('checked', false);
        }
        if ($('.allcheckbox:checked').length == $('.allcheckbox').length) {
            $('#select-all').prop('checked', true);
        }
    });
</script>
<?php $this->end(); ?>