<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Plan $plan
 */
?>
<?=
$this->Form->create(null, [
    'url' => ['controller' => 'Links', 'action' => 'shorten', 'prefix' => false],
    'id' => 'shorten',
]);
?>

<?php
$this->Form->setTemplates([
    'inputContainer' => '{{content}}',
    'error' => '{{content}}',
    'inputContainerError' => '{{content}}',
]);
?>
<div class="form-group">
    <?=
    $this->Form->control('url', [
        'label' => false,
        'type' => 'text',
        'placeholder' => __('Your URL Here'),
        'required' => 'required',
        'class' => 'form-control',
    ]);
    ?>
</div>

<div class="row">
    <div class="col-sm-3">

    <div class="form-group">
            
            <?=
            $this->Form->control('description', [
                'label' => __('Description'),
                'type' => 'textarea',
                'placeholder' => __('Escribe una descripcion'),
                'class' => 'form-control input-sm',
                'id' => 'description',
            ]);
            ?>
        </div>
    </div>

    <?php if ($plan->alias) : ?>
        <div class="col-sm-3">
            <div class="form-group">
                <?=
                $this->Form->control('alias', [
                    'label' => __('Alias'),
                    'type' => 'text',
                    'placeholder' => __('Alias'),
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        </div>
    <?php endif; ?>


    <?php if ($plan->multi_domains) : ?>
        <div class="col-sm-3">
            <?php if (count(get_multi_domains_list())) : ?>
                <div class="form-group">
                    <?=
                    $this->Form->control('domain', [
                        'label' => __('Domain'),
                        'options' => get_multi_domains_list(),
                        'default' => '',
                        'empty' => get_default_short_domain(),
                        'class' => 'form-control input-sm'
                    ]);
                    ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($plan->link_expiration) : ?>
        <div class="col-sm-3 link-expiration">
            <style>
                .link-expiration label {
                    display: block;
                }
            </style>
            <div class="form-group">
                <?=
                $this->Form->control('expiration', [
                    'label' => __('Expiration date'),
                    'class' => 'form-control input-sm',
                    'type' => 'datetime',
                    'default' => null,
                    'empty' => true,
                    'value' => null,
                    'minYear' => date('Y'),
                    'maxYear' => date('Y') + 10,
                    'orderYear' => 'asc',
                ]);
                ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-sm-3">
        <div class="form-group">
            <?php
            $ads_options = get_allowed_ads();

            if (count($ads_options) > 1) {
                echo $this->Form->control('ad_type', [
                    'label' => __('Advertising Type'),
                    'options' => $ads_options,
                    'default' => get_option('member_default_advert', 1),
                    //'empty'   => __( 'Choose' ),
                    'class' => 'form-control input-sm',
                ]);
            } else {
                $default_ad = get_option('member_default_advert', 1);
                if (!array_key_exists($default_ad, get_allowed_ads())) {
                    $default_ad = array_key_first(get_allowed_ads());
                }
                echo $this->Form->hidden('ad_type', ['value' => $default_ad]);
            }
            ?>
        </div>
    </div>

</div>

<?= $this->Form->button(__('Shorten'), ['class' => 'btn btn-submit btn-primary btn-xs']); ?>

<?= $this->Form->end(); ?>

<div class="shorten add-link-result"></div>
<style>
    .form-group {
        position: relative;
        /* Necesario para el scroll */
    }

    #description {
        width: 100%;
        /* Ocupa todo el ancho de la columna */
        height: 100px;
        /* Ajusta la altura según tus necesidades */
        resize: vertical;
        /* Permite al usuario redimensionar el textarea verticalmente */
        overflow-y: auto;
        /* Habilita el scroll vertical */
        border: 1px solid #ccc;
        /* Estilo básico para el borde */
        padding: 10px;
        /* Agrega padding interno */
        white-space: pre-wrap;
        /* ¡Nuevo! Permite que el texto se ajuste a las líneas */

    }

</style>

