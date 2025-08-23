<?php 
use Cake\View\Helper\HtmlHelper;
$this->loadHelper('Html');
echo $this->Html->css('adminStyles.css');

?>
<div class="form-container">
<h1>Add </h1>
<?php
    echo $this->Form->create($ads);
    // Hard code the user for now.
    echo $this->Form->control('country',['value'=>'all']);
    echo $this->Form->control('doamin',['value'=>'all']);
    echo $this->Form->control('script',['type'=>'textarea','value'=>'none']);
    echo $this->Form->control('scriptDown',['type'=>'textarea','value'=>'none']);

    echo $this->Form->button(__('Save'),['class'=>'btn-save']);
    echo $this->Form->end();
?>
</div>