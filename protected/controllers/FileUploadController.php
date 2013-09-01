<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 勃
 * Date: 13-5-26
 * Time: 下午3:34
 * To change this template use File | Settings | File Templates.
 */
class FileUploadController extends Controller
{
    public function filters()
    {
        return array(
            'accessControl'
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('test', 'list', 'html')
            ),
            array('allow',
                'actions' => array('index', 'delete', 'create'),
                'users' => array('@')
            ),
            array(
                'deny',
                'users' => array('*')
            )
        );
    }

    public function actionList()
    {

    }

    public function actionIndex()
    {
        Yii::app()->fileUpload->initialize();
    }

    public function actionCreate()
    {
        $files = Yii::app()->fileUpload->post();
        $ret_val = array();
        foreach ($files as $file) {
            unset($file->object);
            $file->thumbnail_url = Common::getFileIconUrl($file->postfix);
            $ret_val[] = $file;
        }
        Yii::app()->fileUpload->generate_response(array('files' => $files), true);
    }

    public function actionDelete($id)
    {
        $file = UploadFile::model()->findByPk($id);
        if (empty($file)) {
            AjaxResponse::resourceNotFound();
        }
        if ($file->user_id != Yii::app()->user->id) {
            AjaxResponse::forbidden();
        }
        if ($file->delete()) {
            AjaxResponse::success();
        }
        AjaxResponse::nothingChanged();
    }

    public function actionListOfSubject($subject_type, $subject_id)
    {

    }

    public function actionHtml()
    {
        $this->smarty->render('html');
    }



}
