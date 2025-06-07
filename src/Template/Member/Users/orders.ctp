<?php

use Cake\View\Helper\HtmlHelper;

$this->loadHelper('Html');

echo $this->Html->css('oferts.css');
function get_actions($market,$Url) {

    $confirmUrl =$Url->build(['action' => 'confirm', 'market_id' => $market->id]);
    $cancelUrl = $Url->build(['action' => 'cancel', 'market_id' => $market->id]);
    if($market->my_array['status']=="complete"){
        return "<div class ='completed'>Completed</div>";

    }
    if($market->my_array['status']=="canceled"){
        return "<div class ='canceled'>Canceled</div>";

    }
    return '<a href="' . $cancelUrl . '"><button class="btn-action2">Cancel</button></a>';
}
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

        width: 90%;
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-content: center;
        justify-content: center;
        border-radius: 20px;
        padding: 10px;
        align-items: center;
        margin-bottom: 10px;

    }
    .price-cont{
        width: 100%;
        display: flex;
        margin-right: -37px;
        flex-direction: row;
        justify-content: space-between;
        flex-wrap: wrap;

        align-content: flex-end;

    }
    .btn-action2 {
        color: azure;
        background-color: red;
        border-radius: 10px;
        width: 100%;
        box-shadow: 3px 8px 8px 1px rgb(22 22 22 / 82%);
        font-size: 20px;

        font-weight: 1000;
        width: 100px;
        height: 40px;
        animation: counter-pulse 1s ease-in-out infinite;
    }
    .canceled{
        color: azure;
        background-image: linear-gradient(to right, #f00, #ed3f0b, #ef8500);
        border-radius: 10px;
        width: 100%;
        box-shadow: 0px 0px 7px 6px rgb(253 87 12 / 81%);
        font-size: 20px;

        font-weight: 1000;
        width: 100px;
        height: 40px;
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-content: center;
        justify-content: center;

    }
    .completed{
        color: azure;
        background-image: linear-gradient(to right, #046b09, #0bed85, #04ff00);
        border-radius: 10px;
        width: 100%;
        box-shadow: 0px 0px 7px 6px rgb(22 217 62 / 73%);
        font-size: 20px;

        font-weight: 1000;
        width: 130px;
        height: 40px;
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-content: center;
        justify-content: center;

    }

</style>


<div class="market-container" toggle="modal" type="button" class="btn btn-primary" data-toggle="modal" data-target="#formModal">

    <?php foreach ($mymarkets as $market) : ?>
  
        <div class="ofert-container" >
        <div>
            <h3 class="name"><?= __($market->my_array["ofert_type"]." X".strval($market->my_array["cantidad"])) ?></h3>

        </div>
        <div class="price-cont">
            <?=get_actions($market,$this->Url)?>
            <div class="image-container">
                <img class="etiqueta" src="<?php echo $this->Url->image('etiqueta.png'); ?>" style="width:110px">
                <div class="text-overlay"><?="-$ ".strval($market->my_array["total_price"])?></div>
            </div>


        </div>

    </div>
    <?php endforeach; ?>

</div>


