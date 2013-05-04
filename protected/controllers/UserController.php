<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bolu
 * Date: 12-11-10
 * Time: 下午3:04
 * To change this template use File | Settings | File Templates.
 */
class UserController extends Controller
{
    public function actionSignup(){
        if(Yii::app()->request->isPostRequest){
            $model=new User();
            $model->attributes=$_POST['User'];
            $model->save();
            $this->redirect(Yii::app()->user->loginUrl);
        }else{
            $this->smarty->renderAll('signup');
        }
    }
    public function actionLogin()
    {
        $model=new LoginForm;

        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->smarty->renderAll('login',array('model'=>$model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }
}
