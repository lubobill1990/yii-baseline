<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bolu
 * Date: 12-12-16
 * Time: PM11:52
 * To change this template use File | Settings | File Templates.
 */

class CommentController extends Controller
{

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('show', 'index', 'list','listIFrame'),
                'users' => array('*')
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('submit', 'delete'),
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
        return array(
            'accessControl'
        );
    }

    /**
     * 显示站内最近的所有评论
     */
    public function actionIndex()
    {
        $this->smarty->render('index.tpl');
    }

    public function actionShow($id)
    {
        $this->setPageTitle('评论回复');
        //TODO 判断subjectType
        $comment = Comment::model()->findByPk($id);
        $current_comment_with_parent_comments = $comment->bottomUp(2);
        $children_comment_tree = $comment->topDown(3, 3);
        $this->smarty->renderAll('show.tpl', array(
            'current_comment_with_parent_comments' => $current_comment_with_parent_comments,
            'children_comment_tree' => $children_comment_tree,
            'current_comment' => $comment,
            'with_quote' => false
        ));
    }

    public function actionList($subject_type, $subject_id)
    {
        $pagination_params = Pagination::getParamsFromRequest('comment', $_REQUEST);
        $comments = Comment::getLatest($subject_type, $subject_id, $pagination_params['start'], $pagination_params['items_per_page']);
        if (Yii::app()->request->isPostRequest) {
            $this->smarty->render('_list.tpl', array(
                'comments' => $comments,
            ));
            Yii::app()->end();
        }

        $this->smarty->render('_show_list_content.tpl', array(
            'comments' => $comments,
            'comment_items_per_page' => $pagination_params['items_per_page'],
            'comment_count' => Comment::countOfSubject($subject_type,$subject_id),
            'subject_type' => $subject_type,
            'subject_id' => $subject_id
        ));

    }

    public function actionSubmit()
    {
        if (Yii::app()->request->isPostRequest) {
            $comment = new Comment();
            $comment->attributes = $_POST['Comment'];
            $comment->user_id = Yii::app()->user->id;
            if ($comment->save()) {
                AjaxResponse::success($this->smarty->fetchString('_item', array('comment' => $comment)));
            } else {
                AjaxResponse::saveError(CHtml::errorSummary($comment));
            }
        }
    }

    public function actionTopDown()
    {
        $comment_id = $_REQUEST['id'];
        $depth = isset($_REQUEST['depth']) && is_numeric($_REQUEST['depth']) ? $_REQUEST['depth'] : PHP_INT_MAX;
        $width = isset($_REQUEST['width']) && is_numeric($_REQUEST['width']) ? $_REQUEST['width'] : PHP_INT_MAX;
        $comment = Comment::model()->findByPk($comment_id);
        echo CJSON::encode($comment->topDown($depth, $width));
    }

    public function actionBottomUp()
    {
        $comment_id = $_REQUEST['id'];
        $depth = isset($_REQUEST['depth']) && is_numeric($_REQUEST['depth']) ? $_REQUEST['depth'] : PHP_INT_MAX;
        $comment = Comment::model()->findByPk($comment_id);
        echo CJSON::encode($comment->bottomUp($depth));
    }

    public function actionContext()
    {
        $comment_id = $_REQUEST['id'];
        $order = $_REQUEST['order'];
        $count = isset($_REQUEST['count']) && is_numeric($_REQUEST['count']) ? $_REQUEST['count'] : PHP_INT_MAX;

        if (!in_array($order, array('asc', 'desc'))) {
            echo CJSON::encode(array());
            return;
        }
        $comment = Comment::model()->findByPk($comment_id);
        if (empty($comment)) {
            echo CJSON::encode(array());
            return;
        }
        echo CJSON::encode($comment->context($count));
    }

    public function actionSubject()
    {
        $comment_id = $_REQUEST['id'];
        $comment = Comment::model()->findByPk($comment_id);
        echo CJSON::encode($comment->subject());
    }

    public function actionListIFrame($subject_type,$subject_id){
        $pagination_params = Pagination::getParamsFromRequest('comment', $_REQUEST);
        $comments = Comment::getLatest($subject_type, $subject_id, $pagination_params['start'], $pagination_params['items_per_page']);
        if (Yii::app()->request->isPostRequest) {
            $this->smarty->render('_list.tpl', array(
                'comments' => $comments,
            ));
            Yii::app()->end();
        }

        $this->smarty->render('list_iframe.tpl', array(
            'comments' => $comments,
            'comment_items_per_page' => $pagination_params['items_per_page'],
            'comment_count' => Comment::countOfSubject($subject_type,$subject_id),
            'subject_type' => $subject_type,
            'subject_id' => $subject_id
        ));
    }

    /*

    public function actions()
    {
        // return external action classes, e.g.:
        return array(
            'action1'=>'path.to.ActionClass',
            'action2'=>array(
                'class'=>'path.to.AnotherActionClass',
                'propertyName'=>'propertyValue',
            ),
        );
    }
    */
}