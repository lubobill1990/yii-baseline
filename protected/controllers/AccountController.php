<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 勃
 * Date: 13-7-15
 * Time: 下午1:49
 * To change this template use File | Settings | File Templates.
 */
class AccountController extends Controller
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
                'actions' => array('signup', 'login', 'activate', 'resetPassword', 'retrievePassword', 'block', 'choose', 'resendActivateCode', 'blocked', 'unblock')
            ),
            array('allow',
                'actions' => array('index', 'view', 'logout'),
                'users' => array('@')
            ),
            array(
                'deny',
                'users' => array('*')
            )
        );
    }

    public function actionSignup()
    {
        $user = new User();
        $errors = array();
        $password = '';
        try {
            if (Yii::app()->request->isPostRequest && $this->requireValues($_POST, 'User')) {
                $password = $_POST['User']['password'];
                $user->attributes = $_POST['User'];
                if (!Yii::app()->captcha->validate($_POST['captcha'])) {
                    $this->throwMessage('captcha', '验证码错误');
                }

                if (strlen($_POST['User']['password']) < 6) {
                    $this->throwMessage('password', '密码长度应大于等于六个字符');
                }
                $transaction = Yii::app()->db->beginTransaction();
                if ($user->save()) {
                    $activate_code = $user->generateOperationKey('activate');
                    $transaction->commit();

                    Yii::app()->mailer->send(
                        $user->email,
                        '账户完成注册，请激活',
                        $this->smarty->fetchString('signup_email',
                            array('user' => $user, 'activate_code' => $activate_code)));
                    $this->smarty->renderAll('signup_success', array('user' => $user));
                } else {
                    throw new Exception("用户创建失败");
                }
            }
        } catch (CDbException $e) {
            $transaction->rollback();
            if ($e->getCode() == 23000) {
                if (preg_match("/key 'email'/", $e->getMessage())) {
                    $errors['email'] = "该电子邮箱已被注册";
                } elseif (preg_match("/key 'username'/", $e->getMessage())) {
                    $errors['username'] = "该用户名已被注册";
                }
            }
        } catch (Exception $e) {
            $errors = $this->getThrownMessage();
        }

        $this->smarty->renderAll('signup', array('user' => $user, 'password' => $password, 'errors' => $errors));
    }

    public function actionLogin()
    {
        $model = new LoginForm;
        $errors = array();
        $show_captcha = false;

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            //查询这个用户名在一段时间内登陆错误的次数
            $res = Yii::app()->db->createCommand("SELECT count(*) AS count FROM user_login_log WHERE user_login_id=:login_id AND timestamp>:timestamp AND success='no'")->query(array('login_id' => $model->username, 'timestamp' => strftime("%Y-%m-%d %H:%M:%S", time() - 30)))->read();
            if ($res['count'] > 3) {
                $show_captcha = true;
            }
            if ($show_captcha && (!isset($_POST['captcha']) || $_POST['captcha'] != Yii::app()->captcha->text())) {
                $errors['captcha'] = 1;
            } // validate user input and redirect to the previous page if valid
            elseif ($model->validate() && $model->login()) {
                $this->redirect(isset($_REQUEST['return_url']) && !preg_match('/signup|login/', $_REQUEST['return_url']) ? $_REQUEST['return_url'] : "/");
            } else {
                $errors['username'] = 1;
            }
        }
        // display the login form
        $return_url = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '/';
        // don't return to temp url
        if (strpos($return_url, 'user_id') !== false && strpos($return_url, 'key')) {
            $return_url = "/";
        }
        $this->smarty->renderAll('login', array('model' => $model, 'show_captcha' => $show_captcha, 'errors' => $errors, 'return_url' => $return_url));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionResendActivateCode()
    {
        $errors = array();
        $email = '';
        try {
            if (Yii::app()->request->isPostRequest && $this->requireValues($_POST, 'captcha', 'email')) {
                $email = $_POST['email'];
                if ($_REQUEST['captcha'] != Yii::app()->captcha->text()) {
                    $this->throwMessage('captcha', "验证码错误");
                }
                $user = User::model()->findByAttributes(array('email' => $email));
                if (empty($user) || $user->has_been_activated == 'yes') {
                    $this->throwMessage('user', "该邮箱用户不存在或者已激活");
                }
                $activate_code = $user->generateOperationKey('activate');

                Yii::app()->mailer->send(
                    $user->email,
                    '账户完成注册，请激活',
                    $this->smarty->fetchString('signup_email',
                        array('user' => $user, 'activate_code' => $activate_code)));
                $this->smarty->renderAll("activate_code_email_success");
                Yii::app()->end();
            }
        } catch (Exception $e) {
            $errors = $this->getThrownMessage();
        }

        $this->smarty->renderAll('resend_activate_code', array(
            'errors' => $errors, 'email' => $email
        ));

    }

    public function actionActivate($user_id, $key)
    {
        try {
            $user = User::model()->findByPk($user_id);
            if (empty($user)) {
                $this->throwMessage('param', '不是有效的激活链接');
            }
            if ($user->has_been_activated == 'yes') {
                $this->smarty->renderAll('activate_success', array('message' => '账户已激活，您不需要重复激活账户'));
                Yii::app()->end();
            }
            $activate_code = $user->getOperationKey('activate');
            if (empty($activate_code) || $activate_code->key != $key) {
                $this->throwMessage('param', '不是有效的激活链接');
            }
            $user->has_been_activated = 'yes';
            if ($user->save()) {
                $activate_code->delete();
                Yii::app()->mailer->send($user->email, "账户 $user->email 激活成功", $this->smarty->fetchString('activate_success_email', array('user' => $user)));
                $this->smarty->renderAll('activate_success', array('message' => '账户激活成功'));
            }

        } catch (Exception $e) {
            $errors = $this->getThrownMessage();
            $this->smarty->renderAll('activate_fail', array('error' => $errors['param']));
        }

    }

    public function actionResetPassword($user_id, $key)
    {
        $transaction = Yii::app()->db->beginTransaction();
        $user = User::model()->findByPk($user_id);
        try {
            if (empty($user)) {
                $this->throwMessage('message', '不合法或者链接已过期');
            }
            $change_password_key = $user->getOperationKey('change_password');
            if (empty($change_password_key)) {
                $this->throwMessage('message', '不合法或者链接已过期');
            }
            if (Yii::app()->request->isPostRequest && $this->requireValues($_POST, 'password')) {
                $password = $_POST['password'];
                $user->changePassword($password);
                if ($user->save()) {
                    $block_key = $user->generateOperationKey('block');
                    $change_password_key->delete();
                    $transaction->commit();
                    Yii::app()->mailer->send($user->email, '修改密码成功', $this->smarty->fetchString('reset_password_success_email', array('user' => $user, 'key' => $block_key)));
                    $this->smarty->renderAll('reset_password_success', array('user' => $user));
                    Yii::app()->end();
                }
            } else {
                $this->smarty->renderAll('reset_password_form');
            }
        } catch (CDbException $e) {
            $transaction->rollback();
        } catch (Exception $e) {
            $errors = $this->getThrownMessage();
            $this->smarty->renderAll('reset_password_form_fail', array('message' => $errors['message']));
        }
    }

    public function actionRetrievePassword()
    {
        $errors = array();
        $email = '';
        try {
            if (Yii::app()->request->isPostRequest && $this->requireValues($_POST, 'email', 'captcha')) {
                $email = $_POST['email'];

                if ($_POST['captcha'] != Yii::app()->captcha->text()) {
                    $this->throwMessage('captcha', '验证码输入错误');
                }
                $user = User::model()->find("email=:email", array('email' => $email,));
                if (empty($user)) {
                    $this->throwMessage('user', '用户不存在，请确认邮箱没有拼写错误');
                }
                $change_password_key = $user->generateOperationKey('change_password');

                Yii::app()->mailer->send($user->email, '修改密码链接',
                    $this->smarty->fetchString('retrieve_password_email',
                        array(
                            'user' => $user,
                            'key' => $change_password_key
                        )));
                $this->smarty->renderAll('retrieve_password_success', array());
                Yii::app()->end();
            }
        } catch (Exception $e) {
            $errors = $this->getThrownMessage();
        }

        $this->smarty->renderAll('retrieve_password_form', array('errors' => $errors, 'email' => $email));

    }

    public function actionBlock($user_id, $key)
    {
        try {
            $user = User::model()->findByPk($user_id);
            if (empty($user)) {
                $this->throwMessage('user', '冻结账户的链接已过期或不可用');
            }
            $block_key = $user->getOperationKey('block');
            if (empty($block_key) || $block_key->key != $key) {
                $this->throwMessage('user', '冻结账户的链接已过期或不可用');
            }
            $user->blocked = 'yes';
            if ($user->save()) {
                $block_key->delete();
                $unblock_key = $user->generateOperationKey('unblock');
                Yii::app()->mailer->send($user->email, '账户被限制',
                    $this->smarty->fetchString('block_mail',
                        array(
                            'user' => $user,
                            'key' => $unblock_key
                        )));
                $this->smarty->renderAll('block_success', array('user' => $user));
                Yii::app()->end();
            }

        } catch (Exception $e) {
            $errors = $this->getThrownMessage();
            $this->smarty->renderAll('block_fail', array('errors' => $errors));
        }
    }

    public function actionUnBlock($user_id, $key)
    {
        try {
            $user = User::model()->findByPk($user_id);
            if (empty($user)) {
                $this->throwMessage('user', '用户不存在');
            }
            $unlock_key = $user->getOperationKey("unblock");
            if (empty($unlock_key) || $key != $unlock_key->key) {
                $this->throwMessage('user', '用户不存在或链接已过期');
            }
            $user->blocked = 'no';
            if ($user->save()) {
                $unlock_key->delete();
                $this->smarty->renderAll('unblock_success', array('user' => $user));
                Yii::app()->end();
            }
        } catch (Exception $e) {
            $errors = $this->getThrownMessage();
            $this->smarty->renderAll('unblock_fail', array('errors' => $errors));
        }
    }

    public function actionBlocked()
    {
        $this->smarty->renderAll("blocked");
    }

    public function actionChoose()
    {
        $this->smarty->render('choose');
    }

    public function actionIndex()
    {
        $this->smarty->renderAll('index', array());
    }

    public function actionView($id)
    {
        $this->smarty->renderAll('view', array('user' => User::model()->findByPk($id)));
    }
}
