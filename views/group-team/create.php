<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GroupTeam */

$this->title = Yii::t('tournament', 'Create Group Team');
$this->params['breadcrumbs'][] = ['label' => Yii::t('tournament', 'Tournaments'), 'url' => ['/tournament/index']];
$this->params['breadcrumbs'][] = ['label' => $model->group->stage->tournament->name, 'url' => ['/tournament/update', 'id' => $model->group->stage->tournament_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('tournament', 'Stages'), 'url' => ['/tournament/update', 'id' => $model->group->stage->tournament_id, 'tab' => 'stages']];
$this->params['breadcrumbs'][] = ['label' => $model->group->stage->name, 'url' => ['/stage/update', 'id' => $model->group->stage_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('tournament', 'Groups'), 'url' => ['/stage/update', 'id' => $model->group->stage_id, 'tab' => 'groups']];
$this->params['breadcrumbs'][] = ['label' => $model->group->name, 'url' => ['/group/update', 'id' => $model->group_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('tournament', 'Group Teams'), 'url' => ['/group/update', 'id' => $model->group_id, 'tab' => 'teams']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="group-team-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>