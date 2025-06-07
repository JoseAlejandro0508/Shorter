<?php

use Cake\View\Helper\HtmlHelper;

$this->loadHelper('Html');

echo $this->Html->css('rankmen.css');
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="rank_container">
<style>
    .winnerarea{
        background-image: url("<?php echo $this->Url->image('bg1.png') ?>"); 
        background-size:cover; /* O contain, o repeat-x, etc. */
        background-repeat: no-repeat; /* O repeat, etc. */

    }
    
</style>
    <div class ="winnerarea">
    <h1 class="animated-heading"> <?= __('Monthly Ranking') ?></h1>

    <div class="count">
        <div class="timer">
        <div class="counter-wrapper" id="counterElement">

            <div class="counter">
                <div class="counter__box black-white">
                    <p class="counter__time" id="days"></p>
                    <p class="counter__duration">days</p>
                </div>
                <div class="counter__box sky-blue">
                    <p class="counter__time" id="hours"></p>
                    <p class="counter__duration">hours</p>
                </div>
                <p class="dots">:</p>
                <div class="counter__box sky-blue">
                    <p class="counter__time" id="minutes"></p>
                    <p class="counter__duration">minutes</p>
                </div>
                <p class="dots">:</p>
                <div class="counter__box sky-blue">
                    <p class="counter__time" id="seconds"></p>
                    <p class="counter__duration">seconds</p>
                </div>

            </div>
        </div>
        </div>
    </div>




    <!-- Tabla de rangos con estilos -->
    <table class="table table-striped" id="table_info">
        <thead>
            <tr>
                <th>Position</th>
                <th>Winner</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <figure class="rank-figure" id="f1"><img src="<?php echo $this->Url->image('medalla-de-oro.png'); ?>" style = "width:45px;height:45px"> 
                        GOLD<img src="<?php echo $this->Url->image('cinta.png'); ?>" id = "counterElement" style = "width:65px;height:65px"> 
                    </figure>
                </td>
                <td ><figure class="rank-figure" id="f1"><?= h($pos[0]->username) ?></figure></td>
            </tr>
            <tr>
                <td>
                    <figure class="rank-figure" id="f2" > <img src="<?php echo $this->Url->image('medalla-de-plata.png'); ?>" style = "width:45px;height:45px"> 
                        SILVER
                    </figure>
                </td>
                <td ><figure class="rank-figure" id="f2"><?= h($pos[1]->username) ?></figure></td>
            </tr>
            <tr>
                <td>
                    <figure class="rank-figure" id = "f3" ><img src="<?php echo $this->Url->image('medalla-de-bronce.png'); ?>" style = "width:45px;height:45px"> 
                        BRONZE
                    </figure>
                </td>
                <td><figure class="rank-figure" id="f3"><?= h($pos[2]->username) ?></figure></td>
            </tr>

        </tbody>
    </table>
    </div>

    <!-- Tabla de usuarios con rango -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rank</th> <!-- Nueva columna para el rango -->
                <th>ID</th>
                <th>Username</th>
                <th>Number of Views</th>
                <th>Member Since</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td>
                        <figure class="rank-figure" id="<?= getRankStyle($user->id,$pos) ?>"> <!-- Obtiene el estilo del rango -->
                            <?= getRank($user->id,$pos,$pos_) ?>
                        </figure>
                    </td>
                    <td><?= h($user->id) ?></td>
                    <td><?= h($user->username) ?></td>
                    <td><?= h($total_views_per_user[$user->id]) ?></td>
                    <td><?= h($user->created ? $user->created->format('Y-m-d H:i:s') : 'Sin fecha') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <link rel="stylesheet" src="rank.css">
    <div id="my-data" data-json='<?php echo $data_json; ?>'></div>
    <!-- Tabla de rangos -->
    <script>
        $(document).ready(function() {
            var data = JSON.parse($('#my-data').attr('data-json'));
            dif = parseInt(data.month_temporizer);
            var distance = dif*1000;
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
                distance-=1000;
            }, 1000);
        });
    </script>



</div>


<?php

function getRank($id,$pos,$pos_)
{
    if($id == $pos[0]->id){
        return "GOLDEN";
    }elseif($id == $pos[1]->id){
        return "SILVER";
    }
    elseif($id == $pos[2]->id){
        return "BRONZE";
    }
    return $pos_[$id];
}
// Función para obtener el estilo del rango
function getRankStyle($id,$pos)
{
    if($id == $pos[0]->id){
        return "f1_";
    }elseif($id == $pos[1]->id){
        return "f2_";
    }
    elseif($id == $pos[2]->id){
        return "f3_";
    }
    return "f4_";
}

?>