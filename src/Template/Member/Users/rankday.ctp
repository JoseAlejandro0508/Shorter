<?php

use Cake\View\Helper\HtmlHelper;

$this->loadHelper('Html');

echo $this->Html->css('allRank.css');
echo $this->Html->css('rankmen.css');
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="rank_container">



    <style>



        .animated-heading1 {
            font-family: 'Arial Black', sans-serif;
            /* O la fuente que prefieras */
            font-weight: 700;
            /* Grosor de fuente */
 
            /* Gradiente inicial: rojo a azul */
            padding: 10px 20px;
            /* Espacio interno */
            border-radius: 10px;
            /* Bordes redondeados */
            color: white;
            /* Color del texto */
            text-align: center;
            /* Centrado del texto */
  
            /* Animación */

        }
        @keyframes parpadeo {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% {opacity: 1; }
        }
        @keyframes WinnerAnimation {
            0% { scale: 1; }
            50% { scale: 0.95; }
            100% {scale: 1; }
        }
        .count{

            /* O repeat, etc. */
            height: 200px;

        }
        .RankWinerContainer{
            animation:WinnerAnimation 1s infinite;
            position: absolute;
            background: linear-gradient(45deg, #51e722, #19813f);
            display: flex;
            flex-direction: column;
            align-content: center;
            justify-content: center;
            align-items: center;
            /* top: 200px; */
            /* left: 50%; */
            /* transform: translate(-50%, -50%); */
            /* z-index: 2000; */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #16bc008f;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgb(0 0 0 / 47%);
            z-index: 1000;
            width: 90%;
            text-align: center;
        }
        .RankWinerContainer button{
            padding: 8px;
            width: 150px;
            /* color: aqua; */
            background: linear-gradient(45deg, #d9d9d9, #fff4f480);
            border-radius: 10px;
            color: black;
            font-size: 20px;
    font-weight: 600;
        }
        .RankWinerContainer h1{
            font-size: 30px;
            font-weight: 900;
        }

        

    </style>
    <?php if ($Winner) : ?>
        <div class="RankWinerContainer">

            <h1>Felicidades ha sido uno de los ganadores</h1>
            <a href="<?php echo $this->Url->build(['controller' => 'Users', 'action' => 'ClaimRankD']); ?>"><button><span>Reclamar</span></button></a>
        </div>

    <?php endif; ?>
    <div class="winnerarea">
        <div class="TitleContainer">
            <strong style="padding:10px;background: linear-gradient(90deg, #ff00d6, #0777f7);-webkit-background-clip: text;color: #00000000;font-weight: 700;">
      Ganadores del Dia </strong>
        </div>






        <!-- Tabla de rangos con estilos -->
        <table class="table table-striped" id="table_info">
            <thead>
                <tr>
                    <th><?=__('Posicion')?></th>
                    <th><?=__('Ganador')?></th>
                    <th><?=__('Vistas')?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <figure class="rank-figure" id="f1"><img src="<?php echo $this->Url->image('medalla-de-oro.png'); ?>" style="width:45px;height:45px">
                            <?=__('Oro')?><img src="<?php echo $this->Url->image('cinta.png'); ?>" id="counterElement" style="width:65px;height:65px">
                        </figure>
                    </td>
                    <td>
                        <figure class="rank-figure" id="f1"><?= h($posy[0]->username) ?></figure>
                    </td>
                    <td>
                        <?=$total_viewsy[$posy[0]->id]?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <figure class="rank-figure" id="f2"> <img src="<?php echo $this->Url->image('medalla-de-plata.png'); ?>" style="width:45px;height:45px">
                            Plata
                        </figure>
                    </td>
                    <td>
                        <figure class="rank-figure" id="f2"><?= h($posy[1]->username) ?></figure>
                    </td>
                    <td>
                        <?=$total_viewsy[$posy[1]->id]?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <figure class="rank-figure" id="f3"><img src="<?php echo $this->Url->image('medalla-de-bronce.png'); ?>" style="width:45px;height:45px">
                            Bronce
                        </figure>
                    </td>
                    <td>
                        <figure class="rank-figure" id="f3"><?= h($posy[2]->username) ?></figure>
                    </td>
                    <td>
                        <?=$total_viewsy[$posy[2]->id]?>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
    <div class="count">
        <div class="TitleContainer">
<strong style="padding:10px;background: linear-gradient(90deg, #ff00d6, #0777f7);-webkit-background-clip: text;color: #00000000;font-weight: 700;">
      Ranking Diario    </strong>
        </div>
        <div class="timer">
            <div class="counter-wrapper" id="counterElement">

                <div class="counter">
                    <div class="counter__box black-white">
                        <p class="counter__time" id="days"></p>
                        <p class="counter__duration"><?=__('dias')?></p>
                    </div>
                    <div class="counter__box sky-blue">
                        <p class="counter__time" id="hours"></p>
                        <p class="counter__duration"><?=__('horas')?></p>
                    </div>
                    <p class="dots">:</p>
                    <div class="counter__box sky-blue">
                        <p class="counter__time" id="minutes"></p>
                        <p class="counter__duration"><?=__('minutos')?></p>
                    </div>
                    <p class="dots">:</p>
                    <div class="counter__box sky-blue">
                        <p class="counter__time" id="seconds"></p>
                        <p class="counter__duration"><?=__('segundos')?></p>
                    </div>

                </div>
            </div>
        </div>
    </div>

<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?=__('Lugar')?></th> <!-- Nueva columna para el rango -->
                <th><?=__('Plan')?></th>
                <th><?=__('Usuario')?></th>
                <th><?=__('Vistas')?></th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td>
                        <figure class="rank-figure" id="<?= getRankStyle($user->id, $pos) ?>"> <!-- Obtiene el estilo del rango -->
                            <?= getRank($user->id, $pos, $pos_) ?>
                        </figure>
                    </td>
                <td>
                        <?php  $logged_userPlan=get_user_plan($user->id)?>
                        
                        <i style="<?=  $logged_userPlan->Style ?>"id="PlanIco"class="fa fa-<?=  $logged_userPlan->Icon ?>"></i> 
                        <span style="<?=  $logged_userPlan->Style ?>" id="PlanText"><?=   $logged_userPlan->title ?></span>
                    </td>
                    <td><?= h($user->username) ?></td>
                    <td><?= h($total_viewst[$user->id]) ?></td>
              
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <!-- Tabla de usuarios con rango -->

    <link rel="stylesheet" src="rank.css">
    <div id="my-data" data-json='<?php echo $data_json; ?>'></div>
    <!-- Tabla de rangos -->
    <script>
        $(document).ready(function() {
            var data = JSON.parse($('#my-data').attr('data-json'));
            dif = parseInt(data.month_temporizer);
            var distance = dif * 1000;
            var x = setInterval(function() {

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                let countdownDays = document.getElementById("days");
                let countdownHours = document.getElementById("hours");
                let countdownMinutes = document.getElementById("minutes");
                let countdownSeconds = document.getElementById("seconds");

                countdownDays.innerHTML = days;
                countdownHours.innerHTML = hours;
                countdownMinutes.innerHTML = minutes;
                countdownSeconds.innerHTML = seconds;
                // If the count down is finished, write some text
                if (distance <= 0) {
                    clearInterval(x);
                    //document.getElementById("demo").innerHTML = `<div class="exp">Started on 30 Jan 2023</div>`;

                    countdownDays.innerHTML = '00';
                    countdownHours.innerHTML = '00';
                    countdownMinutes.innerHTML = '00';
                    countdownSeconds.innerHTML = '00';
                }
                //location.reload();
                distance -= 1000;
            }, 1000);
        });
    </script>



</div>


<?php

function getRank($id, $pos, $pos_)
{
    if ($id == $pos[0]->id) {
        return "GOLDEN";
    } elseif ($id == $pos[1]->id) {
        return "Plata";
    } elseif ($id == $pos[2]->id) {
        return "Bronce";
    }
    return $pos_[$id];
}
// Función para obtener el estilo del rango
function getRankStyle($id, $pos)
{
    if ($id == $pos[0]->id) {
        return "f1_";
    } elseif ($id == $pos[1]->id) {
        return "f2_";
    } elseif ($id == $pos[2]->id) {
        return "f3_";
    }
    return "f4_";
}

?>