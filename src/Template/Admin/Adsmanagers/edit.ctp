<?php 
use Cake\View\Helper\HtmlHelper;
$this->loadHelper('Html');
echo $this->Html->css('adminStyles.css');

?>
<div class="form-container">


<h1>Edit</h1>

<?php
    echo $this->Form->create($ad);
    // Hard code the user for now.
    echo $this->Form->control('country');
    echo $this->Form->control('domain');
    echo $this->Form->control('script',['type'=>'textarea']);
    echo $this->Form->control('scriptDown',['type'=>'textarea']);
    echo $this->Form->button(__('Save'),['class'=>'btn-save']);
    echo $this->Form->end();
?>
</div>