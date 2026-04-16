<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Link[]|\Cake\Collection\CollectionInterface $offerts
 * @var \App\Model\Entity\Plan $logged_user_plan
 */
$this->assign('title', __('Manage My Offerts'));
$this->assign('description', '');
$this->assign('content_title', __('Manage My Offerts'));
$this->assign('VdBalance', $user->earnings);

?>
<style>
    .info_container {
        display: flex;
        flex-direction: row;
        justify-content: space-evenly;
    }

    .info {
        background: #076cd3b8;
        border-radius: 10px;
        width: 30%;
        height: 10%;
        text-align: center;

    }

    .info p {
        color: azure;
    }
</style>

<div class="box box-solid">
    <div class="box-body">
        <?php
        // The base url is the url where we'll pass the filter parameters
        $base_url = ['controller' => 'VdmarketingUsers', 'action' => 'myofferts'];

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

      

        <?= $this->Form->button(__('Filtrar'), ['class' => 'btn btn-default btn-sm']); ?>


        <?= $this->Form->end(); ?>

    </div>
</div>

<?php foreach ($offerts as $offert) : ?>

    <?php
    $short_url = $offertsUrl[$offert->token];
    if(!isset( $offertsDescription[$offert->token])){
        continue;
    }
    $info = $offertsDescription[$offert->token]->description;
    $title =$offertsDescription[$offert->token]->description->title;
    $earning= $offertsDescription[$offert->token]->earning;
    $country=$offertsDescription[$offert->token]->country;
    $views=$ViewsPerToken[$offert->token];
    $userearnings=$offertsDescription[$offert->token]->earning*$views;

    ?>


    <div class="box box-solid">

        <div class="box-body">

            <h4><a href="<?= $short_url ?>" target="_blank" rel="nofollow noopener noreferrer">
                    <span class="glyphicon glyphicon-link"></span> <?= h($title) ?></a></h4>
            <p>
                <span>Descripcion: </span><?=$info ?>
            </p>
            <p>
                <span>Ganancia: </span><?=$earning ?>
            </p>
            <p>
                <span>Pais: </span><?=$country ?>
            </p>
            <div class="info_container">
                <div class="info">
                    <p> <i class="fa fa-eye"></i><?= __('Views: ') ?><?= $views?></p>
                </div>
                <div class="info">
                    <p> <i class="fa fa-money"></i><?= __('Earnings: ') ?> <?= $userearnings ?></p>
                </div>
            </div>
            <p class="text-muted">

                <small>
                

                    <i class="fa fa-calendar"></i> <?= display_date_timezone($offert->created); ?>

                </small>
            </p>
            <div class="row">
                <div class="col-sm-6">
                    <div class="input-group"><input type="text" class="form-control input-sm" value="<?= $short_url ?>" readonly="" onfocus="javascript:this.select()">
                        <div class="input-group-addon copy-it" data-clipboard-text="<?= $short_url ?>" data-toggle="tooltip" data-placement="bottom" title="<?= __('Copy') ?>"><i class="fa fa-clone"></i></div>
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
