<?php

namespace app\controllers;

use app\components\BaseController;
use common\models\Group;
use common\models\GroupTeam;
use common\models\Tournament;
use yii\web\NotFoundHttpException;

/**
 * GroupTeamController implements the CRUD actions for GroupTeam model.
 */
class GroupTeamController extends BaseController
{
    /**
     * Creates a new GroupTeam model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $group_id
     * @param $group_id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionCreate($group_id)
    {
        if (!$group = Group::findOne($group_id)) {
            throw new NotFoundHttpException('Group does not exist.');
        }

        $model = new GroupTeam();
        $model->group_id = $group->id;
        $model->tournament_id = $group->stage->tournament_id;

        if ($model->load(request()->post()) && $model->save()) {
            if (submitFormButtons()->isSave()) {
                return $this->redirect(['/group/update', 'id' => $model->group_id, 'tab' => 'teams']);
            } else {
                return $this->redirect(['update', 'group_id' => $model->group_id, 'team_id' => $model->team_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing GroupTeam model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $group_id
     * @param integer $team_id
     * @return mixed
     */
    public function actionDelete($group_id, $team_id)
    {
        $model = $this->findModel($group_id, $team_id);
        $model->delete();

        return $this->redirect(['/group/update', 'id' => $model->group_id, 'tab' => 'teams']);
    }

    /**
     * Finds the GroupTeam model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $group_id
     * @param integer $team_id
     * @return GroupTeam the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($group_id, $team_id)
    {
        $query = GroupTeam::find()->where([
            GroupTeam::tableName() . '.group_id' => $group_id,
            GroupTeam::tableName() . '.team_id' => $team_id,
            Tournament::tableName() . '.organizer_id' => organizer()->id
        ])->innerJoinWith('group.stage.tournament', false);

        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}