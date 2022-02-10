<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Cell */
/* @var $cellCodes array */
/* @var $code string */
/* @var $colors array*/
/* @var $color string */
/* @var $shifts \app\models\Shift[] */
/* @var $logbookForm string */

$map = $model->answer1->map;
$this->title = $code;
$this->params['breadcrumbs'][] = ['label' => 'Maps', 'url' => ['map/index']];
$this->params['breadcrumbs'][] = ['label' => $map->name, 'url' => ['map/view', 'id' => $map->id]];
$this->params['breadcrumbs'][] = $this->title;

$js = "$('.shift-block').on('click', function(){
        let num = $(this).data('num');
        console.log(num);
        $('.shift-block.active').removeClass('active');
        $('.cell-button').css('opacity', 0);
        $('.shift-block[data-num='+num+']').addClass('active');
        $('.cell-button[data-num='+num+']').css('opacity', 1);
        num -= 1;
        location.hash = 'cell-content-'+num;        
    });
    $('.shift-block.active').click();
    location.hash = '';
    ";
$this->registerJs($js);
$shiftCells = [];
$barColors = [];
$cellButtons = [];
if($shifts) {
    $shiftCells[] = $shifts[0]->cellStart;
    $cellCode = $cellCodes[$shifts[0]->cellStart->id];
    $barColors[] = $colors[$cellCode];
    $cellButtons[] = Html::tag('div', $cellCode, ['class' => 'cell-button', 'data-num' => 1]);
    foreach($shifts as $i => $shift) {
        $shiftCells[] = $shift->cellEnd;
        $cellCode = $cellCodes[$shift->cellEnd->id];
        $barColors[] = $colors[$cellCode]; 
        $cellButtons[] = Html::tag('div', $cellCode, ['class' => 'cell-button', 'data-num' => $i + 2]);
    }    
}
?>
<h2><?=$map->name ?> - <?=$code ?></h2>
<div class="cell-view">
    
    <div class="row">
        <div class="col-lg-4">
            <h5 style="background: <?=$color ?>" > Ideal Journey and Approach Shifts</h5>
        </div>        
        <div class="col-lg-8">
            <h5 style="background: <?=$color ?>"><?= $code ?> Approach & Funding Shifts</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <?php 
                if($code == 'A1') {
                    echo Html::tag("div", "No approach shifts required for A1 profile payers", ['class' => "answer-block"]), "\n";
                } else {
                    $rows = array_slice(['A', 'B', 'C', 'D', 'E'], 0, $map->size);
                    $columns = array_slice(['5', '4', '3', '2', '1'], 5 - $map->size, $map->size);
                    $wide = 7 - $map->size;
                    foreach($rows as $row) {
                        echo Html::beginTag('div', ['class' => 'row']);
                        foreach($columns as $column) {
                            $arrow = '';
                            $cellCode = $row.$column;
                            foreach($shifts as $shift) {
                                if($cellCodes[$shift->cell_start_id] == $cellCode) {
                                    $endCell = $cellCodes[$shift->cell_end_id];
                                    if($endCell[0] == $row) {
                                        $vectorClass = '';
                                    } elseif($endCell[1] == $column) {
                                        $vectorClass = 'arrow-top';
                                    } else {
                                        $vectorClass = 'arrow-right-top';
                                    }
                                    $arrow = Html::img('images/arrow.png', ['class' => "arrow $vectorClass"]);
                                }
                            }                            
                            echo Html::tag("div", $cellCode. $arrow, ['class' => "col-lg-$wide cell", 'style' => 'background: '.$colors[$cellCode]]), "\n";
                        }
                        echo Html::endTag('div');
                    }
                }
            ?>            
        </div>
        <?php if($code == 'A1') { ?>
        <div class="col-lg-8">
            <div class="answer-block">You have successfully achieved your payer ideal approach/funding goal! No further shifts are required.</div>
        </div>
        <?php } else { ?>
        <div class="col-lg-1">
            <div class="row">
                <div style="background: <?=$color ?>" class="shift-title"> <?= $map->question1_text ?></div>
            </div>
            <div class="row">
                <div style="background: <?=$color ?>" class="shift-title"> <?= $map->question2_text ?></div>
            </div>            
        </div>
        <div class="col-lg-7">
            <div class="row">                
                <?php
                    $count = count($shiftCells);
                    foreach($shiftCells as $i => $shiftCell) { 
                            $cellCode = $cellCodes[$shiftCell->id];
                            $cellColor = $colors[$cellCode]; 
                            $active = $cellCode == $code ? 'active' : '';
                        ?>
                        <div class="col-lg-<?= $wide?>">
                            <div style="background: <?=$cellColor ?>" class="shift-block <?=$active ?>" data-num="<?=$i+1 ?>"> 
                                <?=$shiftCell->question1_compact ?>                                
                            </div>
                            <?php if($i < $count - 1) { ?>
                                <image src="images/arrow.png" class="arrow-between">
                            <?php } ?>
                        </div>
                    <?php }
                ?>                
            </div>
            <div class="row" style="margin-top: 5px">                
                <?php
                    foreach($shiftCells as $i => $shiftCell) { 
                            $cellCode = $cellCodes[$shiftCell->id];
                            $cellColor = $colors[$cellCode];
                            $active = $cellCode == $code ? 'active' : '';
                        ?>
                        <div class="col-lg-<?= $wide?>">
                            <div style="background: <?=$cellColor ?>" class="shift-block <?=$active ?>" data-num="<?=$i+1 ?>"> <?=$shiftCell->question2_compact ?></div>
                        </div>
                    <?php }
                ?>
            </div>                    
        </div>
        <?= Html::tag('div', implode("\n", $cellButtons), ['id' => 'map-progress', 'style' => "background-image: linear-gradient(90deg, ".implode(', ',$barColors).");"]); ?>
        
        <h3>Full content journey</h3>
        <div class="full-content-container">
        <?php 
        $count = count($shiftCells);
        foreach($shiftCells as $i => $shiftCell) {
                $num = $i +1;
                $links = '';
                if($shiftCell->links) {
                    $urls = explode(' ', $shiftCell->links);
                    $links = [];
                    foreach($urls as $j => $url) {                        
                        $linkNum = $j + 1;
                        $links[] = Html::a('link '.$linkNum, $url);
                    }
                    $links = Html::tag('div', implode(', ', $links));                    
                }
                echo Html::tag('div',$num.') '.$shiftCell->content.$links, ['class' => 'cell-content', 'id' => 'cell-content-'.$i]);
                if($i < $count - 1) {
                    echo Html::tag('div', '<image src="images/arrow.png" class="arrow-small arrow-bottom">', ['class' => 'container-arrow']);
                }
        }
    } ?>
        </div>
    </div>    
</div>
<p>
    &nbsp;
</p>
<?php
 if($logbookForm) {
     echo "<h2>Add logbook</h2>\n", $logbookForm;
 }
