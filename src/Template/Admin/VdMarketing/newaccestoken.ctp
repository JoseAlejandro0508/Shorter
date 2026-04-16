<?php
$this->assign('title', __('Access Token'));
$this->assign('description', '');
$this->assign('content_title', __('Acces Token'));
?>
<div class="box box-solid">
        <div class="box-body">



            <div class="row">
                <div class="col-sm-6">
                    <div class="input-group"><input type="text" class="form-control input-sm" value="<?= $AccesToken ?>" readonly="" onfocus="javascript:this.select()">
                        <div class="input-group-addon copy-it" data-clipboard-text="<?=$AccesToken  ?>" data-toggle="tooltip" data-placement="bottom" title="<?= __('Copy') ?>"><i class="fa fa-clone"></i></div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="text-right">
              
                            <?=
                            $this->Html->link(
                                __('Change'),
                                ['action' => 'newaccestoken',true],
                                ['class' => 'btn btn-primary btn-sm']
                            );
                            ?>
                 

         
                    </div>
                </div>
            </div>
        </div>
</div>