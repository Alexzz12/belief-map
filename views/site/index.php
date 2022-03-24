<?php

/* @var $this yii\web\View */
/* @var $isAdmin bool */
/* @var $isManager bool */

use yii\helpers\Html;
use kartik\icons\Icon;
//cabinet-filing
//
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Payer Negotiation Toolkit</h1>
        <hr>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4 menu-block">

                <h2>User cabinet <?=Icon::show('briefcase', ['style' => 'float:right;']) ?></h2>
                <?= Html::a('Belief maps', ['map/index']) ?><br>
                <?= Html::a('Contacts', ['contact/index', 'all' => 1]) ?><br>
                <?= Html::a('Logbook', ['logbook/index', 'all' => 1]) ?><br>
                <?php if($isManager) {
                    echo Html::a('Your company users', ['user/index']), '<br>';
                } 
                ?>
            </div>
            <?php if($isAdmin) { ?>
                <div class="col-lg-4 menu-block admin">
                    <h2>Administration <?=Icon::show('wrench', ['style' => 'float:right;']) ?></h2>

                    <?= Html::a('Belief maps', ['map/index']) ?><br>
                    <?= Html::a('Companies', ['company/index']) ?><br>
                    <?= Html::a('Users', ['user/index']) ?><br>
                    <?= Html::a('All contacts', ['contact/index', 'all' => 1]) ?><br>
                </div>                 
            <?php } ?>
        </div>

    </div>
</div>
