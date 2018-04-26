<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\GroupTeamStatsBehavior;

/**
 * This is the model class for table "group_team".
 *
 * @property integer $group_id
 * @property integer $team_id
 * @property integer $tournament_id
 *
 * @property Team $team
 * @property Group $group
 * @property Stage $stage
 * @property Tournament $tournament
 * @property TournamentTeam $tournamentTeam
 * @property Organizer $organizer
 * @property MatchTeam[] $matchesTeam
 * @property Match[] $matches
 *
 * @property Team[] $availableTeams
 *
 * @mixin GroupTeamStatsBehavior
 */
class GroupTeam extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_team';
    }

    public function behaviors()
    {
        return [
            GroupTeamStatsBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'team_id'], 'required'],
            [['group_id', 'team_id'], 'integer'],
            [
                ['team_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Team::class,
                'targetAttribute' => ['team_id' => 'id']
            ],
            [
                ['group_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Group::class,
                'targetAttribute' => ['group_id' => 'id']
            ],
            [
                ['tournament_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Tournament::class,
                'targetAttribute' => ['tournament_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $statsLabels = $this->getBehavior(0)->attributeLabels();
        return [
            'group_id' => Yii::t('tournament', 'Group'),
            'team_id' => Yii::t('team', 'Team'),
        ] + $statsLabels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::class, ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStage()
    {
        return $this->hasOne(Stage::class, ['id' => 'stage_id'])->via('group');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament()
    {
        return $this->hasOne(Tournament::class, ['id' => 'tournament_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournamentTeam()
    {
        return $this->hasOne(TournamentTeam::class, ['team_id' => 'team_id', 'tournament_id' => 'tournament_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizer()
    {
        return $this->hasOne(Organizer::class, ['id' => 'organizer_id'])->via('tournament');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatchesTeam()
    {
        return $this->hasMany(MatchTeam::class, ['team_id' => 'team_id', 'group_id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatches()
    {
        return $this->hasMany(Match::class, ['id' => 'match_id'])->via('matchesTeam')->groupBy('id');
    }

    /**
     * @return Team[]
     */
    public function getAvailableTeams()
    {
        $groupIds = $this->group->stage->getGroups()->select('id')->column();

        $excludeTeamIds = self::find()->where(['in', 'group_id', $groupIds]);
        if (!$this->isNewRecord) {
            $excludeTeamIds->andWhere(['<>', 'team_id', $this->team_id]);
        }
        $excludeTeamIds->select('team_id')->scalar();

        return Team::find()
            ->joinWith(['tournamentTeams'])
            ->where([TournamentTeam::tableName() . '.tournament_id' => $this->group->stage->tournament_id])
            ->andWhere(['not in', 'team_id', $excludeTeamIds])
            ->defaultOrder()
            ->all();
    }

    public function getGroupsByStage()
    {
        return $this->group->stage->groups;
    }

    /**
     * @inheritdoc
     * @return GroupTeamQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GroupTeamQuery(get_called_class());
    }
}