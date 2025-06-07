<?php

use Cake\View\Helper\HtmlHelper;

$this->loadHelper('Html');

echo $this->Html->css('oferts.css');


?>

<style>
    .image-container {
        position: relative;
        top: 10px;

        /* Necesario para el posicionamiento absoluto del texto */
    }

    .text-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 20px;
        z-index: 10;
        font-weight: 1000;
        /* Asegúrate que el texto está por encima de la imagen */
    }

    .ofert-container {
         background: linear-gradient(to right, #2e79e9, #0000ff);
        /*background-image: url("/public_html/img/bgfb.jpg");
       
        background-size: cover;
        background-repeat: no-repeat;*/
        width: 90%;
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-content: center;
        justify-content: center;
        border-radius: 20px;
        padding: 10px;
        align-items: center;
        box-shadow: 5px 5px 10px rgba(208, 5, 248, 0.67);
        animation: counter-pulse 1s ease-in-out infinite;
    }
</style>

<div class="market-container" toggle="modal" type="button" class="btn btn-primary" data-toggle="modal" data-target="#formModal">
    <div class="ofert-container">
        <div>
            <h3 class="name"><?= __('Facebook Accounts') ?></h3>

        </div>
        <div class="price-cont">

            <div class="image-container">
                <img class="etiqueta" src="<?php echo $this->Url->image('etiqueta.png'); ?>" style="width:110px">
                <div class="text-overlay">$ <?= $setting["facebook_price"]["value"] ?></div>
            </div>


        </div>

    </div>

</div>


<?php
// Puedes usar esta variable para cambiar la apariencia del botón
$buttonText = "Agregar al Carrito";
?>

<style>
    /* Estilos CSS para el modal */
    .modal-content {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background-color: #007bff;
        color: #fff;
        padding: 15px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .modal-title {
        margin-bottom: 0;
    }

    .close {
        color: #fff;
        opacity: 1;
        float: right;
        font-size: 20px;
        font-weight: bold;
    }

    .modal-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .modal-footer {
        padding: 15px;
        border-top: 1px solid #ced4da;
        background-color: #f2f2f2;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .btn {
        border-radius: 4px;
    }

    .btn-primary {
        background-color: #007bff;
        color: #fff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0069d9;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: #fff;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    /* Estilos de la animación del spinner */
    .spinner-border {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        border: 4px solid #007bff;
        border-radius: 50%;
        border-color: #007bff transparent #007bff transparent;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>



<!-- Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Ingresa tus datos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo $this->Form->create(null, ['url' => ['action' => 'getmarket']]); ?>
                <input type="hidden" name="ofert_type" value="Cuentas de Fcebook">

                <div class="form-group">
                    <label for="whatsapp">Número de WhatsApp:</label>
                    <div class="input-group">
                        <select class="form-control" id="prefijo" onchange="change_()">
                            <option value="">Seleccione Prefijo</option>
                            <?php for ($i = 1; $i <= 100; $i++) : ?>
                                <option value="+<?= $i ?>">+<?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="Ingrese su número de WhatsApp" onchange="change_()">
                    </div>
                    <p style="color:red"><?= __("Es importante que escriba su número correctamente, pues será contactado por este medio") ?></p>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad de artículos:</label>
                    <input type="number" class="form-control" id="cantidad" name="cantidad" placeholder="Ingrese la cantidad">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="sr-only">Loading...</span>
                        <?php echo $buttonText; ?>
                    </button>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    function change_() {
        $pref = document.getElementById("prefijo");
        $num = document.getElementById("whatsapp");
        $num.value = $pref.value.toString() + " " + $num.value.split(" ")[1];


    }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script>
    // Código JavaScript para manejar la animación del spinner

    // Obtén el botón de envío
    const submitButton = document.querySelector('button[type="submit"]');

    // Obtén el spinner
    const spinner = submitButton.querySelector('.spinner-border');

    // Agrega un evento de clic al botón de envío
    submitButton.addEventListener('click', () => {
        // Muestra el spinner
        spinner.style.display = 'inline-block';

        // Desactiva el botón de envío
        submitButton.disabled = true;

        // Simula el envío del formulario (reemplaza esto con tu lógica real)
        setTimeout(() => {
            // Oculta el spinner
            spinner.style.display = 'none';

            // Reactiva el botón de envío
            submitButton.disabled = false;

            // Aquí puedes realizar la acción de envío del formulario
            // por ejemplo, enviar una solicitud AJAX al servidor
        }, 2000); // Tiempo de espera de 2 segundos para simular el envío
    });
</script>