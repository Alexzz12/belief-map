<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\icons\Icon;

/* @var $this yii\web\View */
/* @var $model app\models\Map */
/* @var $answers1 array */
/* @var $answers2 array */
/* @var $cellCodes array */
/* @var $contacts array */
/* @var $colors array */
/* @var $isAdmin bool */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Maps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$cellCodes = array_flip($cellCodes);
$this->registerJsVar('cellCodes', $cellCodes);

$js = "$('#map-question1, #map-question2, #map-contactname').on('change', function() {        
        let q1 = $('#map-question1').val();
        let q2 = $('#map-question2').val();
        let contact = $('#map-contactname').val();
        let result = '';
        if(q1 && q2) {
            cell = cellCodes[q1+q2] ? cellCodes[q1+q2] : null;
            $('.cell').css('color', '#000');
            if(cell) {                
                result = 'submit to '+ q1 + q2;
                if(!contact) {
                    result = 'need select contact';
                }
                $('#btn-submit').prop('disabled', contact ? false : true);                
                $('.cell[data-code='+q1 + q2 +']').css('color', 'red');
            } else {
                result = 'N/A';
                $('#btn-submit').prop('disabled', true);
            }
        } else {
            result = 'need answers';
            $('#btn-submit').prop('disabled', true);
        }
        $('#btn-submit').text(result);
    }).change();";
$this->registerJs($js);
$axisWidth = 75 * $model->size + 25;
?>
<style>
    parent {
          width: <?= $axisWidth?>px
    }
</style>
<div class="map-view">

    <h1><?= Html::encode($this->title) ?></h1>        
    
    <div class="map-form">
        <div class="row">
            <div class="col-lg-6">
                <?php $form = ActiveForm::begin(); ?> 

                <?php
                    if($isAdmin) {
                        echo $form->field($model, 'intro')->textArea(['maxwidth' => 2000])->label('edit only by admins');
                    } else {
                        echo Html::tag('p', $model->intro);
                    }
                ?>
                <?= $form->field($model, 'contactName')->widget(Select2::class, [
                    'data' => $contacts,
                    'options' => ['placeholder' => 'Insert or select  contact ...'],
                    'pluginOptions' => [
                        'tags' => true                
                    ],
                ]); ?>

                <?= $form->field($model, 'question1')->dropDownList($answers1, ['prompt' => 'Select your answer'])->label($model->question1_text) ?>

                <?= $form->field($model, 'question2')->dropDownList($answers2, ['prompt' => 'Select your answer'])->label($model->question2_text) ?>               

                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-info', 'id' => 'btn-submit', 'disabled' => true]) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="map-preview col-lg-6" style="padding-left: 30px;">
            <?php             
                $rows = array_slice(['A', 'B', 'C', 'D', 'E'], 0, $model->size);
                $columns = array_slice(['5', '4', '3', '2', '1'], 5 - $model->size, $model->size);
                $wide = 7 - $model->size;
                foreach($rows as $row) {
                    echo Html::beginTag('div', ['class' => 'row']);
                    foreach($columns as $column) {
                        $arrow = '';
                        $cellCode = $row.$column;
                        if(in_array($cellCode, ['B2','C3','D4', 'E5'])) {
                            $arrow = Html::tag('div', Icon::show('arrow-right'), ['class' => "arrow-select arrow-right-top"]);
                        }
                        $color = $colors[$cellCode];
                        echo Html::tag("div", $cellCode.$arrow, ['class' => "cell", 'style' => "background: conic-gradient(from 45deg, $color, {$color}80)", 'data-code' => $cellCode]), "\n";
                    }
                    echo Html::endTag('div');
                }        
            ?>
                <parent class="vertical">
                    <span class="legend">&nbsp;Payer&nbsp;belief&nbsp;</span>
                    <div class="line">
                        <div class="bullet"></div>
                    </div>
                </parent>
                <parent>
                    <span class="legend">&nbsp;Payer&nbsp;funding&nbsp;</span>
                    <div class="line">
                        <div class="bullet"></div>
                    </div>
                </parent>
            </div>
        </div>        
    </div>
        

</div>
