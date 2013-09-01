<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bolu
 * Date: 12-12-18
 * Time: PM12:49
 * To change this template use File | Settings | File Templates.
 */

/**
 * 这个Behavior应该放入ActiveRecord中，主键为id
 */
class FeedbackBehavior extends CBehavior
{
    private function composeRedisKey($key)
    {
        $owner = $this->getOwner();
        return "{$owner->tableName()}:{$owner->id}:$key";
    }

    private function getSubjectType(){
        return $this->getOwner()->tableName();
    }

    private function getSubjectId(){
        return $this->getOwner()->id;
    }


    public function deleteComment(){
        $sql="DELETE FROM comment WHERE subject_type=:subject_type AND subject_id=:subject_id";
        Yii::app()->db->createCommand($sql)->execute(array(
            'subject_type'=>$this->getSubjectType(),
            'subject_id'=>$this->getSubjectId()
        ));
    }


    public function setLikeCount($like_count){
        return Common::getRedisClient()->set($this->composeRedisKey('like_count'), $like_count);
    }

    public function getLikeCount(){
        $tmp = Common::getRedisClient()->get($this->composeRedisKey('like_count'));
        return empty($tmp) ? 0 : $tmp;
    }

    public function incrLikeCount()
    {
        return Common::getRedisClient()->incr($this->composeRedisKey('like_count'));
    }

    public function decrLikeCount()
    {
        return Common::getRedisClient()->incrBy($this->composeRedisKey('like_count'), -1);
    }

    public function setScore($score)
    {
        if ($score > 5) {
            $score = 5;
        } elseif ($score < 0) {
            $score = 0;
        }
        $score_vote_count = Common::getRedisClient()->incr($this->composeRedisKey('score_vote_count')) + 1;
        $res = Common::getRedisClient()->incrBy($this->composeRedisKey('score'), $score);
        return Common::getRedisClient()->incrBy($this->composeRedisKey('score'), $score) / (double)$score_vote_count;
    }

    public function getScore()
    {
        $score_vote_count = Common::getRedisClient()->get($this->composeRedisKey('score_vote_count')) + 1;
        return Common::getRedisClient()->get($this->composeRedisKey('score')) / (double)$score_vote_count;
    }

    public function getScoreVoteCount()
    {
        return Common::getRedisClient()->get($this->composeRedisKey('score_vote_count'));
    }
}
