<?php

use Cake\View\Helper\HtmlHelper;

$this->loadHelper('Html');

echo $this->Html->css('rankmen.css');
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="rank_container">



    <style>
        .winnerarea {
            background-image: url("<?php echo $this->Url->image('bg1.png') ?>");
            background-size: cover;
            /* O contain, o repeat-x, etc. */
            background-repeat: no-repeat;
            /* O repeat, etc. */

        }

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
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .count {
            background-image: url("<?php echo $this->Url->image('relbg.png') ?>");
            background-size: cover;
            /* O contain, o repeat-x, etc. */
            background-repeat: no-repeat;
            /* O repeat, etc. */
            height: 200px;

        }
    </style>
    <div class="winnerarea">
        <strong style="font-size: xx-large;padding:10px; background: linear-gradient(90deg, #004594, #004594); -webkit-background-clip: text; color: transparent; font-weight: 700;">
            <?= __('Noticias Top') ?>
        </strong>





        <!-- Tabla de rangos con estilos -->
        <table class="table table-striped" id="table_info">
            <thead>
                <tr>
                    <th><?= __('Posicion') ?></th>
                    <th><?= __('Usuario') ?></th>
                    <th><?= __('Link') ?></th>
                    <th><?= __('Vistas') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <figure class="rank-figure" id="f1"><img src="<?php echo $this->Url->image('medalla-de-oro.png'); ?>" style="width:45px;height:45px">
                            <?= __('Oro') ?><img src="<?php echo $this->Url->image('cinta.png'); ?>" id="counterElement" style="width:65px;height:65px">
                        </figure>
                    </td>
                    <td>
                        <figure class="rank-figure" id="f1"><?= h($rank[0]->link->user->username) ?></figure>
                    </td>
                    <td>
                        <?= "{$_SERVER['HTTP_HOST']}/{$rank[0]->link->alias}" ?>
                    </td>
                    <td>
                        <?= $rank[0]->views ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <figure class="rank-figure" id="f2"> <img src="<?php echo $this->Url->image('medalla-de-plata.png'); ?>" style="width:45px;height:45px">
                            Plata
                        </figure>
                    </td>
                    <td>
                        <figure class="rank-figure" id="f2"><?= h($rank[1]->link->user->username) ?></figure>
                    </td>
                    <td>
                        <?= "{$_SERVER['HTTP_HOST']}/{$rank[1]->link->alias}" ?>
                    </td>
                    <td>
                        <?= $rank[1]->views ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <figure class="rank-figure" id="f3"><img src="<?php echo $this->Url->image('medalla-de-bronce.png'); ?>" style="width:45px;height:45px">
                            Bronce
                        </figure>
                    </td>
                    <td>
                        <figure class="rank-figure" id="f3"><?= h($rank[2]->link->user->username) ?></figure>
                    </td>
                    <td>
                        <?= "{$_SERVER['HTTP_HOST']}/{$rank[2]->link->alias}" ?>
                    </td>
                    <td>
                        <?= $rank[2]->views ?>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>



    <!-- Tabla de usuarios con rango -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= __('Lugar') ?></th> <!-- Nueva columna para el rango -->
                <th><?= __('Usuario') ?></th>
                <th><?= __('Link') ?></th>
                <th><?= __('Vistas') ?></th>

            </tr>
        </thead>
        <tbody>
            <?php $count = 1 ?>

            <?php foreach ($rank as $user ) : ?>
                <?php if($count>10 ) : ?>
                    <?php break; ?>
                <?php endif; ?>
                <tr>
                    <td>
                        <figure class="rank-figure" id="<?= getRankStyle($user->link->id, $rank) ?>"> <!-- Obtiene el estilo del rango -->
                            <?= $count ?>
                        </figure>
                    </td>
                    <td><?= h($user->link->user->username) ?></td>
                     <td> <?= "{$_SERVER['HTTP_HOST']}/{$user->link->alias}" ?> <td>
                    <td><?= h($user->views) ?> <td>
        
                </tr>
                <?php $count += 1 ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <link rel="stylesheet" src="rank.css">
    <div id="my-data" data-json='<?php echo $data_json; ?>'></div>
    <?php

    // Función para obtener el estilo del rango
    function getRankStyle($id, $pos)
    {
        if ($id == $pos[0]->link->id) {
            return "f1_";
        } elseif ($id == $pos[1]->link->id) {
            return "f2_";
        } elseif ($id == $pos[2]->link->id) {
            return "f3_";
        }
        return "f4_";
    }
    ?>