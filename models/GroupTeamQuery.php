<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[GroupTeam]].
 *
 * @see GroupTeam
 */
class GroupTeamQuery extends \yii\db\ActiveQuery
{
    public function byOrganizer($id)
    {
        return $this->innerJoinWith('organizer', false)->andWhere([Organizer::tableName() . '.id' => $id]);
    }

    /**
     * @inheritdoc
     * @return GroupTeam[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GroupTeam|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}