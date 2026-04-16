<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Link[]|\Cake\Collection\CollectionInterface $links
 * @var \App\Model\Entity\Plan $logged_user_plan
 */
$this->assign('title', __('Ofertas Disponibles'));
$this->assign('description', '');
$this->assign('content_title', __('Ofertas Disponibles'));
$this->assign('VdBalance', $user->earnings);

?>

<div class="box box-solid">
    <div class="box-body">
        <?php
        // The base url is the url where we'll pass the filter parameters
        $base_url = ['controller' => 'VdmarketingUsers', 'action' => 'offerts'];

        echo $this->Form->create(null, [
            'url' => $base_url,
            'class' => 'form-inline',
        ]);
        ?>

        <?=
        $this->Form->control('country', [
            'label' => false,
            'class' => 'form-control',
            'type' => 'text',
            'size' => 10,
            'placeholder' => __('Pais'),
        ]);
        ?>



        <?= $this->Form->button(__('Filter'), ['class' => 'btn btn-default btn-sm']); ?>

        <?= $this->Html->link(__('Reset'), $base_url, ['class' => 'btn btn-link btn-sm']); ?>

        <?= $this->Form->end(); ?>

    </div>
</div>

<?php foreach ($offerts as $offert) : ?>

    <?php
    $title = $offert->title;
    $earning = $offert->earning;
    $url = $offert->url;
    $country = $offert->country;
    $description=$offert->description;
    ?>


    <div class="box box-solid">

        <div class="box-body">

            <h4>
                <span class="glyphicon glyphicon-link"></span> <?= h($title) ?>
            </h4>
            <p>
                <?= __('Descripcion: ') ?><?= $description ?>
            </p>

            <p>
                <?= __('Pais: ') ?><?= $country ?>
            </p>
            <p>
                <?= __('Ganancia: ') ?><?= $earning ?>
            </p>


            <div class="row">

                <div class="col-sm-6">
                    <div class="text-right">

                        <?=
                        $this->Html->link(
                            __('Obtener'),
                            ['action' => 'getshortlink','url'=> $url,'id'=> $offert->id],
                            ['class' => 'btn btn-primary btn-sm']
                        );
                        ?>


                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endforeach; ?>

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
