<?php 
use Cake\View\Helper\HtmlHelper;
$this->loadHelper('Html');
echo $this->Html->css('adminStyles.css');

?>
<h1>Configuraciones de Anuncios</h1>
<head>
    <button class="btn">
        <?= $this->Html->link("ADD",["action"=>"add"])?>

    </button>
   
</head>
<table>

    <tr>
        <th>ID</th>
        <th>Pais</th>
        <th>Dominio</th>

        <th>Actions</th>
    </tr>
    <?php foreach($ads as $ad) :?>

        <tr>
            <td> <?= $ad->id?></td>
            <td><?= $ad->country ?></td>
            <td> <?= $ad->domain ?></td>

            <td>
                <button class="btn">
                    <?= $this->Html->link("Edit",["action"=>"edit",$ad->id])?>
                </button>
                <button class="btn">
                    <?= $this->Html->link("Delete",["action"=>"delete",$ad->id])?>
                </button class="btn">
            </td>

        </tr>
    <?php endforeach; ?>
</table>