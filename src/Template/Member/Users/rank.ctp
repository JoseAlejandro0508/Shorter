<?php 
use Cake\View\Helper\HtmlHelper;
$this->loadHelper('Html');

echo $this->Html->css('rank.css');
?>

<div class = "rank_container">

<strong style="font-size: 50px;padding:10px;background: linear-gradient(90deg, #ff00d6, #0777f7);-webkit-background-clip: text;color: #00000000;font-weight: 700;">
      Ranking Global    </strong>

<!-- Tabla de rangos con estilos -->
<table class="table table-striped" id ="table_info">
    <thead>
        <tr>
            <th>Rango</th>
            <th>Requisitos de Vistas</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <figure class="rank-figure" style="background: linear-gradient(to right, #FF5733, #FFC300);"> <!-- Novato -->
                    Novato
                </figure>
            </td>
            <td>0 - 500</td>
        </tr>
        <tr>
            <td>
                <figure class="rank-figure" style="background: linear-gradient(to right, #007bff, #00C9FF);"> <!-- Cadete -->
                    Cadete
                </figure>
            </td>
            <td>501 - 999</td>
        </tr>
        <tr>
            <td>
                <figure class="rank-figure" style="background: linear-gradient(to right, #28a745, #77DD77);"> <!-- Profesional -->
                    Profesional
                </figure>
            </td>
            <td>1000 - 4999</td>
        </tr>
        <tr>
            <td>
                <figure class="rank-figure" style="background: linear-gradient(to right, #FFD700, #FFC300);"> <!-- Estrella -->
                    Estrella
                </figure>
            </td>
            <td>5000 - 49999</td>
        </tr>
        <tr>
            <td>
                <figure class="rank-figure" style="background: linear-gradient(to right, #dc3545, #f08080);"> <!-- Leyenda -->
                    Leyenda
                </figure>
            </td>
            <td>50000 - 999999</td>
        </tr>
        <tr>
            <td>
                <figure class="rank-figure" style="background: linear-gradient(to right, #FF6347, #FFB6C1);"> <!-- Platino -->
                    Platino
                </figure>
            </td>
            <td>1000000 - 4999999</td>
        </tr>
        <tr>
            <td>
                <figure class="rank-figure" style="background: linear-gradient(to right, #00BCD4, #4682B4);"> <!-- Trafficker -->
                    Trafficker
                </figure>
            </td>
            <td>5000000 - 10000000</td>
        </tr>
    </tbody>
</table>

<!-- Tabla de usuarios con rango -->
<table class="table table-striped">
    <thead>
        <tr>
            <th><?=__('Lugar')?></th> <!-- Nueva columna para el rango -->
            <th>ID</th>
            <th>Nombre de Usuario</th>
            <th>Cantidad de Vistas</th>
            <th>Miembro Desde</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td>
                    <figure class="rank-figure" style="<?= getRankStyle($total_views_per_user[$user->id]) ?>"> <!-- Obtiene el estilo del rango -->
                        <?= getRank($total_views_per_user[$user->id]) ?>
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

<!-- Tabla de rangos -->
</div>


<?php

function getRank($views) {
    if ($views >= 5000000 && $views <= 10000000) {
        return "Trafficker";
    } elseif ($views >= 1000000 && $views <= 4999999) {
        return "Platino";
    } elseif ($views >= 50000 && $views <= 999999) {
        return "Leyenda";
    } elseif ($views >= 5000 && $views <= 49999) {
        return "Estrella";
    } elseif ($views >= 1000 && $views <= 4999) {
        return "Profesional";
    } elseif ($views >= 501 && $views <= 999) {
        return "Cadete";
    } else {
        return "Novato";
    }
}
// Función para obtener el estilo del rango
function getRankStyle($views) {
    if ($views >= 5000000 && $views <= 10000000) {
        return "background: linear-gradient(to right, #00BCD4, #4682B4);"; // Trafficker
    } elseif ($views >= 1000000 && $views <= 4999999) {
        return "background: linear-gradient(to right, #FF6347, #FFB6C1);"; // Platino
    } elseif ($views >= 50000 && $views <= 999999) {
        return "background: linear-gradient(to right, #dc3545, #f08080);"; // Leyenda
    } elseif ($views >= 5000 && $views <= 49999) {
        return "background: linear-gradient(to right, #FFD700, #FFC300);"; // Estrella
    } elseif ($views >= 1000 && $views <= 4999) {
        return "background: linear-gradient(to right, #28a745, #77DD77);"; // Profesional
    } elseif ($views >= 501 && $views <= 999) {
        return "background: linear-gradient(to right, #007bff, #00C9FF);"; // Cadete
    } else {return "background: linear-gradient(to right, #FF5733, #FFC300);"; // Novato
    }
}

?>