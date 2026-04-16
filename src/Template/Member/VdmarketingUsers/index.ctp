<?php
$this->assign('title', __('VdMarketing'));
$this->assign('description', '');
$this->assign('content_title', __('Estadisticas VdMarketing'));
$this->assign('VdBalance', $user->earnings);
?>



<?php

// Declaración de la función
function convertUrlsToLinks($text)
{
    // Expresión regular corregida para encontrar URLs
    $pattern = '/(https?:\/\/[^\s]+)/i'; // Asegúrate de usar esta línea
    // Reemplazar las URLs encontradas por enlaces HTML
    $text = preg_replace($pattern, '<a href="$1" target="_blank">$1</a>', $text);
    return $text;
}

?>



<div class="row">
        <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
            <div class="inner">
                <h3><?= display_price_currency($user->earnings); ?></h3>

                <p><?= __('Saldo Disponible') ?></p>
            </div>

        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="icon">

                <i class="fa fa-eye"></i>
            </div>
            <div class="inner">
                <h3><?= $TotalViews?></h3>

                <p><?= __('Vistas Mensuales') ?></p>
            </div>

        </div>
    </div>


    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="icon">
                <i class="fa fa-bank"></i>
            </div>
            <div class="inner">
                <h3><?= display_price_currency($TotalGanance); ?></h3>

                <p><?= __('Ganancias Mensuales') ?></p>
            </div>

        </div>
    </div>



    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="icon">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="inner">
                <h3>
                    <?= (!empty($TotalViews)) ? display_price_currency($TotalGanance / $TotalViews) : 0 ?>
                </h3>

                <p><?= __('Gancia Prom X Vista') ?></p>
            </div>

        </div>
    </div>
    <!-- ./col -->
</div>



<div class="box box-primary">
    <div class="box-header with-border">
        <i class="fa fa-bar-chart"></i>
        <h3 class="box-title"><?= __('Statistics') ?></h3>
    </div>
    <div class="box-body no-padding">
        <div id="chart_div" style="position: relative; height: 300px; width: 100%;"></div>
        <div class="small text-right" style="padding-right: 10px;">
            <?= __('Data is reported in {0} timezone', get_option('timezone', 'UTC')) ?>
        </div>
        <div style="height: 300px;overflow: auto;">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th><?= __('Date') ?></th>
                        <th><?= __('Views') ?></th>
             

                    </tr>
                </thead>
                <?php foreach ($ViewsPerDay as $key => $value) : ?>
                    <tr>
                        <td><?= $key ?></td>
                        <td><?= $value?></td>
                      
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>



<?php $this->start('scriptBottom'); ?>

<link rel="stylesheet" href="https://fastly.jsdelivr.net/gh/almasaeed2010/AdminLTE@v2.3.11/plugins/morris/morris.css">
<script src="https://fastly.jsdelivr.net/gh/DmitryBaranovskiy/raphael@v2.1.0/raphael-min.js"></script>
<script src="https://fastly.jsdelivr.net/gh/almasaeed2010/AdminLTE@v2.3.11/plugins/morris/morris.min.js" type="text/javascript"></script>

<script>
    jQuery(document).ready(function() {
        new Morris.Line({
            element: 'chart_div',
            resize: true,
            data: [
                <?php
                foreach ($ViewsPerDay as $key => $value) {
                    echo '{day: "' . $key . '", views: ' . $value. '},';
                }
                ?>
            ],
            xkey: 'day',
            xLabels: 'day',
            ykeys: ['views'],
            labels: ['<?= __('Views') ?>'],
            lineColors: ['#3c8dbc'],
            lineWidth: 2,
            hideHover: 'auto',
            smooth: false,
             parseTime: false,
        });
    });

</script>




<?php $this->end();