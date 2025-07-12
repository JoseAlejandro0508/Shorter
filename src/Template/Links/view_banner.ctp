<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Link $link
 * @var \App\Model\Entity\Post $post
 * @var mixed $ad_form_data
 * @var string $banner_336x280
 * @var string $banner_468x60
 * @var string $banner_728x90
 * @var mixed $pop_ad
 * @var mixed $show_pop_ad
 * @var \App\Model\Entity\Plan $link_user_plan
 */
$this->assign('title', get_option('site_name'));
$this->assign('description', get_option('description'));
$this->assign('content_title', get_option('site_name'));
$this->assign('og_title', $link->title);
$this->assign('og_description', $link->description);
$this->assign('og_image', $link->image);

?>

<?php $this->start('scriptTop'); ?>
<script type="text/javascript">
    if (window.self !== window.top) {
        window.top.location.href = window.location.href;
    }
</script>
<?php $this->end(); ?>
<style>
    #contador_dev {
        font-size: 24px;
        /* Aumenta el tamaño de la fuente */
        padding: 15px 30px;
        /* Aumenta el padding */
        animation: blink 1s infinite;
        /* Suaviza la transformación */
    }

    .fullscreen {
        display: flex;
        top: 0;
        left: 0;
        position: fixed;
        z-index: 1000;
        width: 100%;
        height: 100%;
        background: #756e6ef2;
        flex-direction: column;
        justify-content: center;
        align-content: center;

    }

    .normal #close {
        display: none;
    }

    #close {
        position: relative;
        text-align: center;
        /* top: 0; */
        /* top: 0; */
        /* right: 0; */
        width: 30px;
        font-size: 20px;
        height: auto;
        top: 0;
        background: #000000d1;
        color: #fcfcfc;
        border: 0;
        font-weight: 900;
    }
</style>
<?php if ($condition != "off") : ?>
    <style>
        #btn_dev_cont {
            font-size: 24px;
            /* Aumenta el tamaño de la fuente */
            padding: 15px 30px;
            /* Aumenta el padding */
            animation: blink 1s infinite;
            /* Suaviza la transformación */
        }

        @keyframes blink {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.5);
            }

            100% {
                transform: scale(1);
            }
        }

        .blink {
            animation: blink 1s infinite;
            /* Hace que parpadee cada segundo */
        }
    </style>

    <style>
        @keyframes shadowBlink {

            0%,
            100% {
                box-shadow: 0 20px 30px rgb(234 15 193);
                /* Sombra inicial */
            }

            50% {
                box-shadow: 0 4px 8px rgb(0 0 0 / 10%);
                /* Sombra reducida */
            }
        }

        @keyframes agrandarAchicar {

            0%,
            100% {
                width: 80%;
                /* Tamaño normal */
                height: auto;
                /* Mantener la proporción */
            }

            50% {
                width: 90%;
                /* Aumentar el ancho */
                height: auto;
                /* Mantener la proporción */
            }
        }

        #banner-button {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /*padding: 20px 50px;*/
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            background-color: #225cb2d9;
            text-decoration: none;
            color: #eaeff5;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 8px 16px rgb(10 10 10);
            z-index: 1000;
            animation: agrandarAchicar 2s ease-in-out infinite;
        }



        #banner-image {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* padding: 20px 50px; */
            /* font-size: 24px; */




            border-radius: 40px;
            /* cursor:pointer; */
            box-shadow: 0 8px 16px rgb(10 10 10);
            z-index: 1000;
            pointer-events: none;

            animation: agrandarAchicar 2s ease-in-out infinite;


        }

        #fondo_dev {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 1);
            /* fondo negro con opacidad */
            z-index: 999;
            /* asegúrate de que esté por encima de todo */
            pointer-events: none;
            /* permite hacer clic en el elemento detrás */
        }
    </style>
<?php endif; ?>
    <?php if (!empty($CustomBanerStyle)) : ?>


        <?= $CustomBanerStyle; ?>
        


    <?php endif; ?>
<?php if ($condition == "img") : ?>

    <div id="fondo_dev"></div>
<?php endif; ?>

<?php if (!empty($CustomBanerCode)) : ?>

    <div id="PopUp" class="<?= $CustomBanerType ?>">
        <button id="close">x</button>
        <?= $CustomBanerCode; ?>
    </div>


<?php endif; ?>
<script>
    var close = document.getElementById("close");
    var popUp = document.getElementById("PopUp");
    close.addEventListener('click', closeFun);

    function closeFun() {
        close.style.display = "none";
        popUp.style.display = "none";

    }
</script>

<div class="row">
    <?php if ($condition != "off") : ?>

        <?= $this->Html->image('playButton.png', [
            'id' => 'banner-image',
            'class' => 'imagen-fantasma',
            'alt' => 'Imagen Fantasma'
        ]) ?>
        <a href="" id="banner-button">
            <?= $this->Html->image('playButton.png', [
                'id' => 'banner',
                'class' => 'btn',
                'alt' => 'btn'
            ]) ?>
        </a>
    <?php endif; ?>
    <div class="col-md-10 col-md-offset-1">

        <div class="box box-success">


            <div class="box-body text-center">



                <?php if (!empty($banner_728x90)) : ?>
                    <div class="banner banner-728x90">
                        <div class="banner-inner">
                            <?= $banner_728x90; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($post) : ?>
                    <div class="blog-item text-left">
                        <div class="page-header">
                            <h3><small><a href="<?= build_main_domain_url('/blog') ?>"><?= __('From Our Blog') ?>
                                        :</a></small> <?= h($post->title) ?></h3>
                        </div>
                        <div class="blog-content"><?= $post->description ?></div>
                    </div>
                <?php endif; ?>

                <h4><?= __('Your link is almost ready.') ?></h4>

                <span id="countdown" class="countdown">
                    <span id="timer" class="timer"><?= $link_user_plan->timer ?? 5 ?></span><br><?= __('Seconds') ?>
                </span>

                <?php if (!empty($banner_468x60)) : ?>
                    <div class="banner banner-468x60">
                        <div class="banner-inner">
                            <?= $banner_468x60; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom: 10px;">
                    <a href="javascript: void(0)" class="btn btn-success btn-lg get-link disabled">
                        <?= __('Please wait...') ?>
                    </a>
                </div>

                <?php if (!empty($banner_336x280)) : ?>
                    <div class="banner banner-336x280">
                        <div class="banner-inner">
                            <?= $banner_336x280; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="myTestAd" style="height: 5px; width: 5px; position: absolute;"></div>

            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>


<?=
$this->Form->create(null, [
    'url' => ['controller' => 'Links', 'action' => 'go', 'prefix' => false],
    'id' => 'go-link',
    'class' => 'hidden',
]);
?>

<?= $this->Form->hidden('ad_form_data', ['value' => $ad_form_data]); ?>

<?=
$this->Form->button(__('Submit'), [
    'id' => 'go-submit',
    'class' => 'hidden',
]);
?>

<?= $this->Form->end(); ?>

<?php if (get_option('enable_popup', 'yes') == 'yes' && $show_pop_ad) : ?>
    <?=
    $this->Form->create(null, [
        'url' => ['controller' => 'Links', 'action' => 'popad', 'prefix' => false],
        'target' => "_blank",
        'id' => 'go-popup',
        'class' => 'hidden',
    ]);
    ?>

    <?= $this->Form->hidden('pop_ad', ['value' => $pop_ad]); ?>

    <?= $this->Form->end(); ?>
<?php endif; ?>

<?php $this->start('scriptBottom'); ?>
<?php $this->end(); ?>

<?php if ($condition == "button") : ?>
    <script type="text/javascript">
        function obtenerEnlacesAdqva() {
            const allElementsWithClassA = [];
            const allElements = document.querySelectorAll("*"); // Selecciona todos los elementos de la página

            for (const element of allElements) {
                if (element.shadowRoot) {
                    // Verifica si el elemento tiene un shadowRoot
                    const elementsInsideShadowRoot = element.shadowRoot.querySelectorAll(
                        "._AdQVA_ad_unit_image"
                    );
                    allElementsWithClassA.push(...elementsInsideShadowRoot); // Agrega los elementos encontrados a la lista
                }
            }

            const enlaces = [];

            allElementsWithClassA.forEach((elemento) => {
                const nodoHijo = elemento.querySelector("a");

                if (nodoHijo) {
                    enlaces.push(nodoHijo.getAttribute("href"));
                }
            });

            return enlaces;
        }

        // Obtener los enlaces y actualizar el botón
        function actualizarEnlaceBoton() {
            const enlacesAdqva = obtenerEnlacesAdqva();
            const boton = document.getElementById("banner-button");
            if (boton.textContent == "Obtener vínculo") {
                boton.textContent = "Ver Noticia";
            }

            if (enlacesAdqva.length > 0) {
                const enlaceAleatorio =
                    enlacesAdqva[Math.floor(Math.random() * enlacesAdqva.length)];

                boton.href = enlaceAleatorio;
                boton.style.display = "flex";
            } else {
                console.log("No se encontraron enlaces válidos.");
            }
        }

        // Ejecutar la función cada 20 segundos
        const prob = Math.floor(Math.random() * 3) + 1;
        if (prob == 1 || prob == 2 || prob == 3) {
            setInterval(actualizarEnlaceBoton, 1000);

            actualizarEnlaceBoton();

        }
    </script>
<?php endif; ?>

<?php if ($condition == "img") : ?>
    <script type="text/javascript">
        function generarNumeroAleatorio(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }


        const numeroAleatorio = generarNumeroAleatorio(1000, 3000);

        function ImgBanner() {

            console.log(numeroAleatorio);
            const boton = document.getElementById("banner-image");
            boton.style.display = "flex";

        }
        setTimeout(ImgBanner, numeroAleatorio);
    </script>




<?php endif; ?>

<?php if ($scrollStat == "on" && $condition == "img") : ?>
    <script type="text/javascript">
        function ScrollAuto() {
            const alturaAleatoria = Math.floor(Math.random() * document.body.scrollHeight);
            const sentidoAleatorio = Math.random() < 0.5 ? -1 : 1;
            window.scrollBy(10, alturaAleatoria * sentidoAleatorio);
        }
        setInterval(ScrollAuto, 300);
    </script>
<?php endif; ?>