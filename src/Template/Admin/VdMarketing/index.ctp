<?php 
use Cake\View\Helper\HtmlHelper;
$this->loadHelper('Html');
echo $this->Html->css('adminStyles.css');

?>
<?php
$this->assign('title', __('Ofertas'));
$this->assign('description', '');
$this->assign('content_title', __('Ofertas'));

?>

<head>
    <button class="btn">
        <?= $this->Html->link("ADD",["action"=>"add"])?>

    </button>
   
</head>
<table>

    <tr>
        <th>ID</th>
        <th>Pais</th>
        <th>Ganancia</th>
        <th>Titulo</th>

        <th>Actions</th>
    </tr>
    <?php foreach($offerts as $offert) :?>

        <tr>
            <td> <?= $offert->id?></td>
            <td><?= $offert->country ?></td>
            <td> <?= $offert->earning ?></td>
            <td> <?= $offert->title ?></td>

            <td>
                <button class="btn">
                    <?= $this->Html->link("Edit",["action"=>"edit",$offert->id])?>
                </button>
                <button class="btn">
                    <?= $this->Html->link("Delete",["action"=>"delete",$offert->id])?>
                </button class="btn">
            </td>

        </tr>
    <?php endforeach; ?>
</table>