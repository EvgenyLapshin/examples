<?php

namespace common\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use common\models\GroupTeam;
use common\models\Match;
use common\models\MatchEventPlayer;
use common\models\MatchEventTypeAction;
use common\models\MatchTeam;
use common\models\MatchTeamQuery;

/**
 * Class GroupTeamStatisticsBehavior
 * Behavior with statistics for team in group.
 *
 * @property array $playedIds List ids played matches.
 * @property array $awayPlayedIds List ids played away matches.
 * @property array $homePlayedIds List ids played home matches.
 *
 * @property integer $played Quantity played matches.
 * @property integer $awayPlayed Quantity played away matches.
 * @property integer $homePlayed Quantity played home matches.
 * @property integer $won
 * @property integer $awayWon
 * @property integer $homeWon
 * @property integer $drawn
 * @property integer $awayDrawn
 * @property integer $homeDrawn
 * @property integer $lost
 * @property integer $awayLost
 * @property integer $homeLost
 *
 * @property integer $goalsFor
 * @property integer $awayGoalsFor
 * @property integer $homeGoalsFor
 * @property integer $goalsAgainst
 * @property integer $awayGoalsAgainst
 * @property integer $homeGoalsAgainst
 * @property integer $goalDifference
 * @property integer $homeGoalDifference
 * @property integer $awayGoalDifference
 * @property integer $averageGoals
 * @property integer $awayAverageGoals
 * @property integer $homeAverageGoals
 * @property integer $averageGoalsAgainst
 * @property integer $awayAverageGoalsAgainst
 * @property integer $homeAverageGoalsAgainst
 *
 * @property integer $points
 * @property integer $awayPoints
 * @property integer $homePoints
 *
 *
 * ***** Between equal *****
 *
 * @property array $playedIdsBetweenEqual
 * @property array $awayPlayedIdsBetweenEqual
 * @property array $homePlayedIdsBetweenEqual
 *
 * @property integer $playedBetweenEqual
 * @property integer $awayPlayedBetweenEqual
 * @property integer $homePlayedBetweenEqual
 * @property integer $wonBetweenEqual
 * @property integer $awayWonBetweenEqual
 * @property integer $homeWonBetweenEqual
 * @property integer $drawnBetweenEqual
 * @property integer $awayDrawnBetweenEqual
 * @property integer $homeDrawnBetweenEqual
 * @property integer $lostBetweenEqual
 * @property integer $awayLostBetweenEqual
 * @property integer $homeLostBetweenEqual
 *
 * @property integer $pointsBetweenEqual
 * @property integer $awayPointsBetweenEqual
 * @property integer $homePointsBetweenEqual
 *
 * @property integer $goalsForBetweenEqual
 * @property integer $awayGoalsForBetweenEqual
 * @property integer $homeGoalsForBetweenEqual
 * @property integer $goalsAgainstBetweenEqual
 * @property integer $awayGoalsAgainstBetweenEqual
 * @property integer $homeGoalsAgainstBetweenEqual
 * @property integer $goalDifferenceBetweenEqual
 * @property integer $awayGoalDifferenceBetweenEqual
 * @property integer $homeGoalDifferenceBetweenEqual
 *
 * @property GroupTeam $owner
 *
 * @package common\components
 */
class GroupTeamStatsBehavior extends Behavior
{
    private $_playedIds;
    private $_awayPlayedIds;
    private $_homePlayedIds;

    private $_won;
    private $_awayWon;
    private $_homeWon;
    private $_lost;
    private $_awayLost;
    private $_homeLost;
    private $_drawn;
    private $_awayDrawn;
    private $_homeDrawn;

    private $_goalsFor;
    private $_awayGoalsFor;
    private $_homeGoalsFor;
    private $_goalsAgainst;
    private $_awayGoalsAgainst;
    private $_homeGoalsAgainst;

    private $_playedIdsBetweenEqual;
    private $_awayPlayedIdsBetweenEqual;
    private $_homePlayedIdsBetweenEqual;
    private $_playedBetweenEqual;
    private $_awayPlayedBetweenEqual;
    private $_homePlayedBetweenEqual;

    private $_wonBetweenEqual;
    private $_awayWonBetweenEqual;
    private $_homeWonBetweenEqual;
    private $_drawnBetweenEqual;
    private $_awayDrawnBetweenEqual;
    private $_homeDrawnBetweenEqual;
    private $_lostBetweenEqual;
    private $_awayLostBetweenEqual;
    private $_homeLostBetweenEqual;

    private $_pointsBetweenEqual;
    private $_awayPointsBetweenEqual;
    private $_homePointsBetweenEqual;

    private $_goalsForBetweenEqual;
    private $_awayGoalsForBetweenEqual;
    private $_homeGoalsForBetweenEqual;
    private $_goalsAgainstBetweenEqual;
    private $_awayGoalsAgainstBetweenEqual;
    private $_homeGoalsAgainstBetweenEqual;
    private $_goalsDifferenceBetweenEqual;
    private $_awayGoalsDifferenceBetweenEqual;
    private $_homeGoalsDifferenceBetweenEqual;

    public function attributeLabels()
    {
        return [
            'played' => Yii::t('table', 'Played'),
            'awayPlayed' => Yii::t('table', 'Away Played'),
            'homePlayed' => Yii::t('table', 'Home Played'),
            'won' => Yii::t('table', 'Won'),
            'awayWon' => Yii::t('table', 'Away Won'),
            'homeWon' => Yii::t('table', 'Home Won'),
            'drawn' => Yii::t('table', 'Drawn'),
            'awayDrawn' => Yii::t('table', 'Away Drawn'),
            'homeDrawn' => Yii::t('table', 'Home Drawn'),
            'lost' => Yii::t('table', 'Lost'),
            'awayLost' => Yii::t('table', 'Away Lost'),
            'homeLost' => Yii::t('table', 'Home Lost'),
            'goalsFor' => Yii::t('table', 'Goals For'),
            'awayGoalsFor' => Yii::t('table', 'Away Goals For'),
            'homeGoalsFor' => Yii::t('table', 'Home Goals For'),
            'goalsAgainst' => Yii::t('table', 'Goals Against'),
            'awayGoalsAgainst' => Yii::t('table', 'Away Goals Against'),
            'homeGoalsAgainst' => Yii::t('table', 'Home Goals Against'),
            'goalDifference' => Yii::t('table', 'Goal Difference'),
            'awayGoalDifference' => Yii::t('table', 'Away Goal Difference'),
            'homeGoalDifference' => Yii::t('table', 'Home Goal Difference'),
            'points' => Yii::t('table', 'Points'),
            'awayPoints' => Yii::t('table', 'Away Points'),
            'homePoints' => Yii::t('table', 'Home Points'),
            'averageGoals' => Yii::t('table', 'Average Goals'),
            'awayAverageGoals' => Yii::t('table', 'Away Average Goals'),
            'homeAverageGoals' => Yii::t('table', 'Home Average Goals'),
            'averageGoalsAgainst' => Yii::t('table', 'Average Goals Against'),
            'awayAverageGoalsAgainst' => Yii::t('table', 'Away Average Goals Against'),
            'homeAverageGoalsAgainst' => Yii::t('table', 'Home Average Goals Against'),
        ];
    }

    public function getPlayedIds()
    {
        if ($this->_playedIds == NULL) {
            $this->setPlayedIds();
        }

        return $this->_playedIds;
    }

    public function setPlayedIds()
    {
        $this->_playedIds = Match::find()
            ->select('id')
            ->played()
            ->innerJoinWith([
                'matchTeams' => function ($query) {
                    /** @var $query ActiveQuery */
                    $query
                        ->select([])
                        ->andWhere([
                            'AND',
                            ['=', MatchTeam::tableName() . '.team_id', $this->owner->team_id],
                            ['=', MatchTeam::tableName() . '.group_id', $this->owner->group_id],
                        ]);
                }
            ])
            ->column();
    }

    public function getAwayPlayedIds()
    {
        if ($this->_awayPlayedIds == NULL) {
            $this->setAwayPlayedIds();
        }

        return $this->_awayPlayedIds;
    }

    public function setAwayPlayedIds()
    {
        $this->_awayPlayedIds = Match::find()
            ->select('id')
            ->played()
            ->innerJoinWith([
                'awayMatchTeam' => function ($query) {
                    /** @var $query ActiveQuery */
                    $query
                        ->select([])
                        ->andWhere([
                            'AND',
                            ['=', MatchTeam::tableName() . '.team_id', $this->owner->team_id],
                            ['=', MatchTeam::tableName() . '.group_id', $this->owner->group_id],
                        ]);
                }
            ])
            ->column();
    }

    public function getHomePlayedIds()
    {
        if ($this->_homePlayedIds == NULL) {
            $this->setHomePlayedIds();
        }

        return $this->_homePlayedIds;
    }

    public function setHomePlayedIds()
    {
        $this->_homePlayedIds = Match::find()
            ->select('id')
            ->played()
            ->innerJoinWith([
                'homeMatchTeam' => function ($query) {
                    /** @var $query ActiveQuery */
                    $query
                        ->select([])
                        ->andWhere([
                            'AND',
                            ['=', MatchTeam::tableName() . '.team_id', $this->owner->team_id],
                            ['=', MatchTeam::tableName() . '.group_id', $this->owner->group_id],
                        ]);
                }
            ])
            ->column();
    }

    public function goalsEventsInCondition()
    {
        return [
            'in',
            MatchEventPlayer::tableName() . '.match_event_type_action_id',
            MatchEventTypeAction::getGoalsEvents()
        ];
    }

    public function getPlayed()
    {
        return count($this->playedIds);
    }

    public function getAwayPlayed()
    {
        return count($this->awayPlayedIds);
    }

    public function getHomePlayed()
    {
        return count($this->homePlayedIds);
    }

    public function getWon()
    {
        if ($this->_won === NULL) {
            $this->setWon();
        }

        return $this->_won;
    }

    public function setWon()
    {
        if ($this->playedIds) {
            $this->_won = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->playedIds,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_WON,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_won = 0;
        }
    }

    public function getAwayWon()
    {
        if ($this->_awayWon === NULL) {
            $this->setAwayWon();
        }

        return $this->_awayWon;
    }

    public function setAwayWon()
    {
        if ($this->awayPlayedIds) {
            $this->_awayWon = Match::find()
                ->innerJoinWith([
                    'awayMatchTeam' => function ($query) {
                        /** @var MatchTeamQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->awayPlayedIds,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_WON,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_awayWon = 0;
        }
    }

    public function getHomeWon()
    {
        if ($this->_homeWon === NULL) {
            $this->setHomeWon();
        }

        return $this->_homeWon;
    }

    public function setHomeWon()
    {
        if ($this->homePlayedIds) {
            $this->_homeWon = Match::find()
                ->innerJoinWith([
                    'homeMatchTeam' => function ($query) {
                        /** @var MatchTeamQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->homePlayedIds,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_WON,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_homeWon = 0;
        }
    }

    public function getDrawn()
    {
        if ($this->_drawn === NULL) {
            $this->setDrawn();
        }

        return $this->_drawn;
    }

    public function setDrawn()
    {
        if ($this->playedIds) {
            $this->_drawn = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->playedIds,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_DRAW,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_drawn = 0;
        }
    }

    public function getAwayDrawn()
    {
        if ($this->_awayDrawn === NULL) {
            $this->setAwayDrawn();
        }

        return $this->_awayDrawn;
    }

    public function setAwayDrawn()
    {
        if ($this->awayPlayedIds) {
            $this->_awayDrawn = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->awayPlayedIds,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_DRAW,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_awayDrawn = 0;
        }
    }

    public function getHomeDrawn()
    {
        if ($this->_homeDrawn === NULL) {
            $this->setHomeDrawn();
        }

        return $this->_homeDrawn;
    }

    public function setHomeDrawn()
    {
        if ($this->homePlayedIds) {
            $this->_homeDrawn = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->homePlayedIds,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_DRAW,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_homeDrawn = 0;
        }
    }

    public function getLost()
    {
        if ($this->_lost === NULL) {
            $this->setLost();
        }

        return $this->_lost;
    }

    public function setLost()
    {
        if ($this->playedIds) {
            $this->_lost = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->playedIds,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_LOST,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_lost = 0;
        }
    }

    public function getAwayLost()
    {
        if ($this->_awayLost === NULL) {
            $this->setAwayLost();
        }

        return $this->_awayLost;
    }

    public function setAwayLost()
    {
        if ($this->awayPlayedIds) {
            $this->_awayLost = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->awayPlayedIds,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_LOST,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_awayLost = 0;
        }
    }

    public function getHomeLost()
    {
        if ($this->_homeLost === NULL) {
            $this->setHomeLost();
        }

        return $this->_homeLost;
    }

    public function setHomeLost()
    {
        if ($this->homePlayedIds) {
            $this->_homeLost = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->homePlayedIds,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_LOST,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_homeLost = 0;
        }
    }

    public function getGoalsFor()
    {
        if ($this->_goalsFor === NULL) {
            $this->setGoalsFor();
        }

        return $this->_goalsFor;
    }

    public function setGoalsFor()
    {
        if ($this->playedIds) {
            $this->_goalsFor = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['=', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->playedIds],
                    $this->goalsEventsInCondition(),
                ])->count();
        } else {
            $this->_goalsFor = 0;
        }
    }

    public function getAwayGoalsFor()
    {
        if ($this->_awayGoalsFor === NULL) {
            $this->setAwayGoalsFor();
        }

        return $this->_awayGoalsFor;
    }

    public function setAwayGoalsFor()
    {
        if ($this->awayPlayedIds) {
            $this->_awayGoalsFor = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['=', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->awayPlayedIds],
                    $this->goalsEventsInCondition(),
                ])->count();
        } else {
            $this->_awayGoalsFor = 0;
        }
    }

    public function getHomeGoalsFor()
    {
        if ($this->_homeGoalsFor === NULL) {
            $this->setHomeGoalsFor();
        }

        return $this->_homeGoalsFor;
    }

    public function setHomeGoalsFor()
    {
        if ($this->homePlayedIds) {
            $this->_homeGoalsFor = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['=', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->homePlayedIds],
                    $this->goalsEventsInCondition(),
                ])->count();
        } else {
            $this->_homeGoalsFor = 0;
        }
    }

    public function getGoalsAgainst()
    {
        if ($this->_goalsAgainst === NULL) {
            $this->setGoalsAgainst();
        }

        return $this->_goalsAgainst;
    }

    public function setGoalsAgainst()
    {
        if ($this->playedIds) {
            $this->_goalsAgainst = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['<>', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->playedIds],
                    $this->goalsEventsInCondition(),
                ])
                ->count();
        } else {
            $this->_goalsAgainst = 0;
        }
    }

    public function getAwayGoalsAgainst()
    {
        if ($this->_awayGoalsAgainst === NULL) {
            $this->setAwayGoalsAgainst();
        }

        return $this->_awayGoalsAgainst;
    }

    public function setAwayGoalsAgainst()
    {
        if ($this->awayPlayedIds) {
            $this->_awayGoalsAgainst = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['<>', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->awayPlayedIds],
                    $this->goalsEventsInCondition(),
                ])
                ->count();
        } else {
            $this->_awayGoalsAgainst = 0;
        }
    }

    public function getHomeGoalsAgainst()
    {
        if ($this->_homeGoalsAgainst === NULL) {
            $this->setHomeGoalsAgainst();
        }

        return $this->_homeGoalsAgainst;
    }

    public function setHomeGoalsAgainst()
    {
        if ($this->homePlayedIds) {
            $this->_homeGoalsAgainst = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['<>', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->homePlayedIds],
                    $this->goalsEventsInCondition(),
                ])
                ->count();
        } else {
            $this->_homeGoalsAgainst = 0;
        }
    }

    public function getGoalDifference()
    {
        return $this->goalsFor - $this->goalsAgainst;
    }

    public function getAwayGoalDifference()
    {
        return $this->awayGoalsFor - $this->awayGoalsAgainst;
    }

    public function getHomeGoalDifference()
    {
        return $this->homeGoalsFor - $this->homeGoalsAgainst;
    }

    public function getPoints()
    {
        return
            $this->won * $this->owner->stage->pointsForWon
            +
            $this->drawn * $this->owner->stage->pointsForDraw
            +
            $this->lost * $this->owner->stage->pointsForLost;
    }

    public function getAwayPoints()
    {
        return
            $this->awayWon * $this->owner->stage->pointsForWon
            +
            $this->awayDrawn * $this->owner->stage->pointsForDraw
            +
            $this->awayLost * $this->owner->stage->pointsForLost;
    }

    public function getHomePoints()
    {
        return
            $this->homeWon * $this->owner->stage->pointsForWon
            +
            $this->homeDrawn * $this->owner->stage->pointsForDraw
            +
            $this->homeLost * $this->owner->stage->pointsForLost;
    }

    public function getAverageGoals()
    {
        if ($this->goalsFor) {
            return floor($this->goalsFor / $this->played);
        } else {
            return 0;
        }
    }

    public function getAwayAverageGoals()
    {
        if ($this->awayGoalsFor) {
            return floor($this->awayGoalsFor / $this->awayPlayed);
        } else {
            return 0;
        }
    }

    public function getHomeAverageGoals()
    {
        if ($this->homeGoalsFor) {
            return floor($this->homeGoalsFor / $this->homePlayed);
        } else {
            return 0;
        }
    }

    public function getAverageGoalsAgainst()
    {
        if ($this->goalsAgainst) {
            return floor($this->goalsAgainst / $this->played);
        } else {
            return 0;
        }
    }

    public function getAwayAverageGoalsAgainst()
    {
        if ($this->awayGoalsAgainst) {
            return floor($this->awayGoalsAgainst / $this->awayPlayed);
        } else {
            return 0;
        }
    }

    public function getHomeAverageGoalsAgainst()
    {
        if ($this->homeGoalsAgainst) {
            return floor($this->homeGoalsAgainst / $this->homePlayed);
        } else {
            return 0;
        }
    }

    public function getPlayedIdsBetweenEqual()
    {
        return $this->_playedIdsBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     * @return GroupTeam[]
     */
    public function getIdsBetweenEqual($list)
    {
        $teamIds = ArrayHelper::getColumn($list, 'team_id');
        $key = array_search($this->owner->team_id, $teamIds);
        if ($key !== false) {
            unset($teamIds[$key]);
        }

        return $teamIds;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setPlayedIdsBetweenEqual($list)
    {
        $teamIds = $this->getIdsBetweenEqual($list);

        $this->_playedIdsBetweenEqual = Match::find()
            ->select('id')
            ->joinWith('matchTeams')
            ->where([
                Match::tableName() . '.id' => $this->playedIds,
                MatchTeam::tableName() . '.team_id' => $teamIds,
            ])
            ->groupBy('id')
            ->column();
    }

    public function getAwayPlayedIdsBetweenEqual()
    {
        return $this->_awayPlayedIdsBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setAwayPlayedIdsBetweenEqual($list)
    {
        $teamIds = $this->getIdsBetweenEqual($list);

        $this->_awayPlayedIdsBetweenEqual = Match::find()
            ->select(Match::tableName() . '.id')
            ->joinWith('matchTeams')
            ->where([
                Match::tableName() . '.id' => $this->awayPlayedIds,
                MatchTeam::tableName() . '.team_id' => $teamIds,
            ])
            ->groupBy('id')
            ->column();
    }

    public function getHomePlayedIdsBetweenEqual()
    {
        return $this->_homePlayedIdsBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setHomePlayedIdsBetweenEqual($list)
    {
        $teamIds = $this->getIdsBetweenEqual($list);

        $this->_homePlayedIdsBetweenEqual = Match::find()
            ->select(Match::tableName() . '.id')
            ->joinWith('matchTeams')
            ->where([
                Match::tableName() . '.id' => $this->homePlayedIds,
                MatchTeam::tableName() . '.team_id' => $teamIds,
            ])
            ->groupBy('id')
            ->column();
    }

    public function getPlayedBetweenEqual()
    {
        return $this->_playedBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setPlayedBetweenEqual($list)
    {
        $this->setPlayedIdsBetweenEqual($list);
        $this->_playedBetweenEqual = count($this->playedIdsBetweenEqual);
    }

    public function getAwayPlayedBetweenEqual()
    {
        return $this->_awayPlayedBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setAwayPlayedBetweenEqual($list)
    {
        $this->setAwayPlayedIdsBetweenEqual($list);
        $this->_awayPlayedBetweenEqual = count($this->awayPlayedIdsBetweenEqual);
    }

    public function getHomePlayedBetweenEqual()
    {
        return $this->_homePlayedBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setHomePlayedBetweenEqual($list)
    {
        $this->setHomePlayedIdsBetweenEqual($list);
        $this->_homePlayedBetweenEqual = count($this->homePlayedIdsBetweenEqual);
    }

    public function getWonBetweenEqual()
    {
        return $this->_wonBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setWonBetweenEqual($list)
    {
        $this->setPlayedIdsBetweenEqual($list);
        if ($this->playedIdsBetweenEqual) {
            $this->_wonBetweenEqual = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->playedIdsBetweenEqual,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_WON,
                        ]);
                    }
                ])
                ->groupBy('id')
                ->count();
        } else {
            $this->_wonBetweenEqual = 0;
        }
    }

    public function getAwayWonBetweenEqual()
    {
        return $this->_awayWonBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setAwayWonBetweenEqual($list)
    {
        $this->setAwayPlayedIdsBetweenEqual($list);
        if ($this->awayPlayedIdsBetweenEqual) {
            $this->_awayWonBetweenEqual = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->awayPlayedIdsBetweenEqual,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_WON,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_awayWonBetweenEqual = 0;
        }
    }

    public function getHomeWonBetweenEqual()
    {
        return $this->_homeWonBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setHomeWonBetweenEqual($list)
    {
        $this->setHomePlayedIdsBetweenEqual($list);
        if ($this->homePlayedIdsBetweenEqual) {
            $this->_homeWonBetweenEqual = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->homePlayedIdsBetweenEqual,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_WON,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_homeWonBetweenEqual = 0;
        }
    }

    public function getDrawnBetweenEqual()
    {
        return $this->_drawnBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setDrawnBetweenEqual($list)
    {
        $this->setPlayedIdsBetweenEqual($list);
        if ($this->playedIdsBetweenEqual) {
            $this->_drawnBetweenEqual = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->playedIdsBetweenEqual,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_DRAW,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_drawnBetweenEqual = 0;
        }
    }

    public function getAwayDrawnBetweenEqual()
    {
        return $this->_awayDrawnBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setAwayDrawnBetweenEqual($list)
    {
        $this->setAwayPlayedIdsBetweenEqual($list);
        if ($this->awayPlayedIdsBetweenEqual) {
            $this->_awayDrawnBetweenEqual = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->awayPlayedIdsBetweenEqual,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_DRAW,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_awayDrawnBetweenEqual = 0;
        }
    }

    public function getHomeDrawnBetweenEqual()
    {
        return $this->_homeDrawnBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setHomeDrawnBetweenEqual($list)
    {
        $this->setHomePlayedIdsBetweenEqual($list);
        if ($this->homePlayedIdsBetweenEqual) {
            $this->_homeDrawnBetweenEqual = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->homePlayedIdsBetweenEqual,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_DRAW,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_homeDrawnBetweenEqual = 0;
        }
    }

    public function getLostBetweenEqual()
    {
        return $this->_lostBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setLostBetweenEqual($list)
    {
        $this->setPlayedIdsBetweenEqual($list);
        if ($this->playedIdsBetweenEqual) {
            $this->_lostBetweenEqual = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->playedIdsBetweenEqual,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_LOST,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_lostBetweenEqual = 0;
        }
    }

    public function getAwayLostBetweenEqual()
    {
        return $this->_awayLostBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setAwayLostBetweenEqual($list)
    {
        $this->setAwayPlayedIdsBetweenEqual($list);
        if ($this->awayPlayedIdsBetweenEqual) {
            $this->_awayLostBetweenEqual = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->awayPlayedIdsBetweenEqual,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_LOST,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_awayLostBetweenEqual = 0;
        }
    }

    public function getHomeLostBetweenEqual()
    {
        return $this->_homeLostBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setHomeLostBetweenEqual($list)
    {
        $this->setHomePlayedIdsBetweenEqual($list);
        if ($this->homePlayedIdsBetweenEqual) {
            $this->_homeLostBetweenEqual = Match::find()
                ->innerJoinWith([
                    'matchTeams' => function ($query) {
                        /** @var ActiveQuery $query */
                        $query->andWhere([
                            MatchTeam::tableName() . '.match_id' => $this->homePlayedIdsBetweenEqual,
                            MatchTeam::tableName() . '.team_id' => $this->owner->team_id,
                            MatchTeam::tableName() . '.match_result' => MatchTeam::MATCH_RESULT_LOST,
                        ]);
                    }
                ])
                ->count();
        } else {
            $this->_homeLostBetweenEqual = 0;
        }
    }

    public function getPointsBetweenEqual()
    {
        return $this->_pointsBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setPointsBetweenEqual($list)
    {
        $this->setWonBetweenEqual($list);
        $this->setDrawnBetweenEqual($list);
        $this->setLostBetweenEqual($list);

        $this->_pointsBetweenEqual =
            $this->wonBetweenEqual * $this->owner->stage->pointsForWon
            +
            $this->drawnBetweenEqual * $this->owner->stage->pointsForDraw
            +
            $this->lostBetweenEqual * $this->owner->stage->pointsForLost;
    }

    public function getAwayPointsBetweenEqual()
    {
        return $this->_awayPointsBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setAwayPointsBetweenEqual($list)
    {
        $this->setWonBetweenEqual($list);
        $this->setDrawnBetweenEqual($list);
        $this->setLostBetweenEqual($list);

        $this->_awayPointsBetweenEqual =
            $this->awayWonBetweenEqual * $this->owner->stage->pointsForWon
            +
            $this->awayDrawnBetweenEqual * $this->owner->stage->pointsForDraw
            +
            $this->awayLostBetweenEqual * $this->owner->stage->pointsForLost;
    }

    public function getHomePointsBetweenEqual()
    {
        return $this->_homePointsBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setHomePointsBetweenEqual($list)
    {
        $this->setWonBetweenEqual($list);
        $this->setDrawnBetweenEqual($list);
        $this->setLostBetweenEqual($list);

        $this->_homePointsBetweenEqual =
            $this->homeWonBetweenEqual * $this->owner->stage->pointsForWon
            +
            $this->homeDrawnBetweenEqual * $this->owner->stage->pointsForDraw
            +
            $this->homeLostBetweenEqual * $this->owner->stage->pointsForLost;
    }

    public function getGoalsForBetweenEqual()
    {
        return $this->_goalsForBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setGoalsForBetweenEqual($list)
    {
        $this->setPlayedIdsBetweenEqual($list);
        if ($this->playedIdsBetweenEqual) {
            $this->_goalsForBetweenEqual = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['=', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->playedIdsBetweenEqual],
                    $this->goalsEventsInCondition(),
                ])->count();
        } else {
            $this->_goalsForBetweenEqual = 0;
        }
    }

    public function getAwayGoalsForBetweenEqual()
    {
        return $this->_awayGoalsForBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setAwayGoalsForBetweenEqual($list)
    {
        $this->setAwayPlayedIdsBetweenEqual($list);
        if ($this->awayPlayedIdsBetweenEqual) {
            $this->_awayGoalsForBetweenEqual = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['=', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->awayPlayedIdsBetweenEqual],
                    $this->goalsEventsInCondition(),
                ])->count();
        } else {
            $this->_awayGoalsForBetweenEqual = 0;
        }
    }

    public function getHomeGoalsForBetweenEqual()
    {
        return $this->_homeGoalsForBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setHomeGoalsForBetweenEqual($list)
    {
        $this->setHomePlayedIdsBetweenEqual($list);
        if ($this->homePlayedIdsBetweenEqual) {
            $this->_homeGoalsForBetweenEqual = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['=', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->homePlayedIdsBetweenEqual],
                    $this->goalsEventsInCondition(),
                ])->count();
        } else {
            $this->_homeGoalsForBetweenEqual = 0;
        }
    }

    public function getGoalsAgainstBetweenEqual()
    {
        return $this->_goalsAgainstBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setGoalsAgainstBetweenEqual($list)
    {
        $this->setPlayedIdsBetweenEqual($list);
        if ($this->playedIdsBetweenEqual) {
            $this->_goalsAgainstBetweenEqual = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['<>', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->playedIdsBetweenEqual],
                    $this->goalsEventsInCondition(),
                ])
                ->count();
        } else {
            $this->_goalsAgainstBetweenEqual = 0;
        }
    }

    public function getAwayGoalsAgainstBetweenEqual()
    {
        return $this->_awayGoalsAgainstBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setAwayGoalsAgainstBetweenEqual($list)
    {
        $this->setAwayPlayedIdsBetweenEqual($list);
        if ($this->awayPlayedIdsBetweenEqual) {
            $this->_awayGoalsAgainstBetweenEqual = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['<>', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->awayPlayedIdsBetweenEqual],
                    $this->goalsEventsInCondition(),
                ])
                ->count();
        } else {
            $this->_awayGoalsAgainstBetweenEqual = 0;
        }
    }

    public function getHomeGoalsAgainstBetweenEqual()
    {
        return $this->_homeGoalsAgainstBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setHomeGoalsAgainstBetweenEqual($list)
    {
        $this->setHomePlayedIdsBetweenEqual($list);
        if ($this->homePlayedIdsBetweenEqual) {
            $this->_homeGoalsAgainstBetweenEqual = MatchEventPlayer::find()
                ->where([
                    'AND',
                    ['<>', MatchEventPlayer::tableName() . '.team_id', $this->owner->team_id],
                    ['in', MatchEventPlayer::tableName() . '.match_id', $this->homePlayedIdsBetweenEqual],
                    $this->goalsEventsInCondition(),
                ])
                ->count();
        } else {
            $this->_homeGoalsAgainstBetweenEqual = 0;
        }
    }

    public function getGoalsDifferenceBetweenEqual()
    {
        return $this->_goalsDifferenceBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setGoalsDifferenceBetweenEqual($list)
    {
        $this->setGoalsForBetweenEqual($list);
        $this->setGoalsAgainstBetweenEqual($list);

        $this->_goalsDifferenceBetweenEqual = $this->goalsForBetweenEqual - $this->goalsAgainstBetweenEqual;
    }

    public function getAwayGoalsDifferenceBetweenEqual()
    {
        return $this->_awayGoalsDifferenceBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setAwayGoalsDifferenceBetweenEqual($list)
    {
        $this->setAwayGoalsForBetweenEqual($list);
        $this->setAwayGoalsAgainstBetweenEqual($list);

        $this->_awayGoalsDifferenceBetweenEqual = $this->awayGoalsForBetweenEqual - $this->awayGoalsAgainstBetweenEqual;
    }

    public function getHomeGoalsDifferenceBetweenEqual()
    {
        return $this->_homeGoalsDifferenceBetweenEqual;
    }

    /**
     * @param GroupTeam[] $list
     */
    public function setHomeGoalsDifferenceBetweenEqual($list)
    {
        $this->setHomeGoalsForBetweenEqual($list);
        $this->setHomeGoalsAgainstBetweenEqual($list);

        $this->_homeGoalsDifferenceBetweenEqual = $this->homeGoalsForBetweenEqual - $this->homeGoalsAgainstBetweenEqual;
    }
}