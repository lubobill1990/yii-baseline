<?php

class LikeController extends Controller
{
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('peopleList'),
                'users' => array('*')
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('like', 'undoLike'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin'),
                'users' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function filters()
    {
        // return the filter configuration for this controller, e.g.:
        return array(
            'accessControl',
            'postOnly + like, undoLike'
        );
    }

    public function actionLike($subjectType, $subjectId)
    {
        //TODO 先要验证subjectType
        //进攻式编程
        $like = new Like();
        $like->attributes = array(
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'user_id' => Yii::app()->user->id
        );
        try {
            if ($like->save()) {
                echo CJSON::encode(array('like_count' => $like->getSubjectLikeCount()));
                Yii::app()->end();
            } else {

            }
        } catch (CDbException $ex) {
            //如果已经收藏了，出现duplicate key的exception
        }
    }

    public function actionUndoLike($subjectType, $subjectId)
    {
        //TODO 先要验证subjectType
        $like = Like::model()->findByAttributes(array(
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'user_id' => Yii::app()->user->id
        ));
        if (!empty($like)) {
            if ($like->delete()) {
                echo CJSON::encode(array('like_count' => $like->getSubjectLikeCount()));
                Yii::app()->end();
            }
        }
    }

    /**
     * 列出一个小列表，中间是收藏某个subject的用户
     * @param $subjectType
     * @param $subjectId
     */
    public function actionPeopleList($subjectType, $subjectId)
    {
        $subject_likes = Like::model()->findAllByAttributes(array('subject_type' => $subjectType, 'subject_id' => $subjectId), array('select' => 'user_id', 'limit' => 20));
        if (empty($subject_likes)) {
            return;
        }
        $user_ids = array();
        foreach ($subject_likes as $subject_like) {
            $user_ids[] = $subject_like->user_id;
        }
        $user_list = User::model()->findAllByAttributes(array('id' => $user_ids), array('select' => array('id', 'email', 'username'),));
        $this->smarty->render('user/_mini_list.tpl', array('user_list' => $user_list), true, false);
    }
}