<?php 
use Cake\View\Helper\HtmlHelper;
$this->loadHelper('Html');
echo $this->Html->css('adminStyles.css');

?>
<div class="form-container">
<?php
$this->assign('title', __('Editar'));
$this->assign('description', '');
$this->assign('content_title', __('Editar'));
?>


<?php
    echo $this->Form->create($offert);
    // Hard code the user for now.
    echo $this->Form->control('country',['required'=>true,'placeholder'=>'Pais de Oferta']);
    echo $this->Form->control('url',['required'=>true,'placeholder'=>'Url de Oferta']);
    echo $this->Form->control('earning',['required'=>true,'placeholder'=>'Ganancia de Oferta']);
    echo $this->Form->control('description',['required'=>true,'placeholder'=>'Agrega un titulo']);
     echo $this->Form->control('title',['required'=>true,'placeholder'=>'Agrega una descripcion']);
    echo $this->Form->button(__('Save'),['class'=>'btn-save']);
    echo $this->Form->end();
?>
</div>