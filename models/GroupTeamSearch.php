<?php

namespace app\models;

use common\models\Team;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GroupTeam;

/**
 * GroupTeamSearch represents the model behind the search form about `app\models\GroupTeam`.
 */
class GroupTeamSearch extends GroupTeam
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'team_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = GroupTeam::find();
        $query->joinWith(['group', 'team']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->defaultOrder = ['team_id' => SORT_ASC];

        $dataProvider->sort->attributes['team_id'] = [
            'asc' => [Team::tableName() . '.name' => SORT_ASC],
            'desc' => [Team::tableName() . 'name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'group_id' => $this->group_id,
            'team_id' => $this->team_id,
        ]);

        return $dataProvider;
    }
}