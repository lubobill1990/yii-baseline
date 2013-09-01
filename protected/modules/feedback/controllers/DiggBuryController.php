<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bolu
 * Date: 12-12-18
 * Time: AM10:51
 * To change this template use File | Settings | File Templates.
 */
class DiggBuryController extends Controller
{
    function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('show', 'test'),
                'users' => array('*')
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('digg', 'bury', 'diggorbury'),
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
            'accessControl',
            'ajaxOnly + bury, digg',
        );
    }

    /**
     * 如果该用户已经对这个subject顶过，则去掉
     * @param $subjectType
     * @param $subjectId
     * 输出：json array('digg_count'=>int,'bury_count'=>int)
     * @throws CDbException
     * @return void
     */
    public function actionDigg($subjectType, $subjectId)
    {
        //TODO 验证subjectType合法性

        $subject_class = ucfirst($subjectType);
        $subject = new $subject_class;
        $subject->id = $subjectId;

        $bury = DiggBury::model()->findByAttributes(array(
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'user_id' => Yii::app()->user->id,
            'type' => 'bury'
        ));
        //如果已经踩过，则要撤销踩
        if (!empty($bury)) {
            $bury->delete();
            $subject->decrBuryCount();
        }
        $digg = new DiggBury();
        $digg->attributes = array(
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'user_id' => Yii::app()->user->id,
            'type' => 'digg',
        );
        try {
            if ($digg->save()) {
                $current_digg_count = $subject->incrDiggCount();
                echo CJSON::encode(array('digg_count' => $current_digg_count, 'bury_count' => $subject->getBuryCount()));
            } else {
                throw new CDbException('unknown');
            }
        } catch (CDbException $e) {
            if (!empty($bury)) {
                $bury = new DiggBury();
                $bury->attributes = array(
                    'subject_type' => $subjectType,
                    'subject_id' => $subjectId,
                    'user_id' => Yii::app()->user->id,
                    'type' => 'bury');
                $bury->save();
                $subject->incrBuryCount();
            }
            if (YII_DEBUG)
                var_dump($e);
        }


    }

    public function actionBury($subjectType, $subjectId)
    {
        //TODO 验证subjectType合法性

        $subject_class = ucfirst($subjectType);
        $subject = new $subject_class;
        $subject->id = $subjectId;

        $digg = DiggBury::model()->findByAttributes(array(
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'user_id' => Yii::app()->user->id,
            'type' => 'digg'
        ));
        //如果已经踩过，则要撤销踩
        if (!empty($digg)) {
            $digg->delete();
            $subject->decrDiggCount();
        }
        $bury = new DiggBury();
        $bury->attributes = array(
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'user_id' => Yii::app()->user->id,
            'type' => 'bury',
        );
        try {
            if ($bury->save()) {
                $current_count = $subject->incrBuryCount();
                echo CJSON::encode(array('digg_count' => $subject->getDiggCount(), 'bury_count' => $current_count));
            } else {
                throw new CDbException('unknown');
            }
        } catch (CDbException $e) {
            if (!empty($digg)) {
                $digg = new DiggBury();
                $digg->attributes = array(
                    'subject_type' => $subjectType,
                    'subject_id' => $subjectId,
                    'user_id' => Yii::app()->user->id,
                    'type' => 'bury');
                $digg->save();
                $subject->incrDiggCount();
            }
            if (YII_DEBUG)
                var_dump($e);
        }
    }
}
