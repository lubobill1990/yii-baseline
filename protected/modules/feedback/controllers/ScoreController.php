<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bolu
 * Date: 12-12-18
 * Time: AM10:52
 * To change this template use File | Settings | File Templates.
 */
class ScoreController extends Controller
{
    function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('show', 'test'),
                'users' => array('*')
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('score'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array(''),
                'users' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );

    }

    function filters()
    {
        return array(
            'accessControl'
        );
    }

    function prepareScore($subjectType,$subjectId){
        $subject_score = Score::model()->find("subject_type=:subject_type and subject_id=:subject_id", array('subject_type' => $subjectType, 'subject_id' => $subjectId));
        if (empty($subject_score)) {
            $subject_score = new Score();
            $subject_score->attributes = array('subject_type' => $subjectType, 'subject_id' => $subjectId);
            if ($subject_score->save()) {
                return $subject_score;
            } else {
                return null;
            }
        }
        return $subject_score;
    }

    function actionScore($subjectType,$subjectId){
        if(!array_key_exists('score',$_REQUEST)){
            return;
        }
        $subject_score=$this->prepareScore($subjectType,$subjectId);
        if(empty($subject_score)){
            return;
        }
        $score_item=new ScoreItem();
        $score_item->attributes=array('user_id'=>Yii::app()->user->id,'score_id'=>$subject_score->id,'score'=>$_REQUEST['score']);
        $transaction=Yii::app()->db->beginTransaction();
        try{
            if(!$score_item->save()){
                $transaction->rollback();
                return;
            }
            $subject_class = ucfirst($subjectType);
            $subject = new $subject_class;
            $subject->id = $subjectId;
            $current_score = $subject->setScore($_REQUEST['score']);
            $transaction->commit();
            echo CJSON::encode(array('new_score' => $current_score,'new_score_vote_count'=>$subject->scoreVoteCount));
        }catch (Exception $e){
            $transaction->rollback();
        }

    }
}
