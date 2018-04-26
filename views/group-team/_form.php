<?php

use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\GroupTeam;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\GroupTeam */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="group-team-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'team_id')->label(Yii::t('tournament', 'Find Group'))->widget(Select2::class, [
        'initValueText' => $model->team ? $model->team->name : '',
        'data' => ArrayHelper::map($model->getAvailableTeams(), 'id', 'name'),
        'options' => ['placeholder' => '']
    ]); ?>

    <div class="form-group">
        <?= submitFormButtons()->getSaveButton() ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>