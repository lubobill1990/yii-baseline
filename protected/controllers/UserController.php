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
    public function actionSignup()
    {
        $user = new User();

        if (Yii::app()->request->isPostRequest) {
            $user->attributes = $_POST['User'];

            $transaction = Yii::app()->db->beginTransaction();
            try {
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
            } catch (Exception $e) {
                $transaction->rollback();
                $this->smarty->renderAll('signup', array('user' => $user, 'error' => $user->errors));
            }
        } else {
            $this->smarty->renderAll('signup', array('user' => $user));
        }
    }

    public function actionLogin()
    {
        $model = new LoginForm;

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->smarty->renderAll('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionActivate()
    {
        if (isset($_REQUEST['code']) && isset($_REQUEST['user_id'])) {
            $user = User::model()->findByPk($_REQUEST['user_id']);
            if (empty($user)) {
                $this->smarty->renderAll('activate_fail', array('error' => '不存在该用户'));
            } else {
                if ($user->has_been_activated == 'yes') {
                    $this->smarty->renderAll('activate_success', array('message' => '账户已激活'));
                } else {
                    $activate_code = $user->getOperationKey('activate');
                    if (!empty($activate_code)) {
                        if ($activate_code->key == $_REQUEST['code']) {
                            $user->has_been_activated = 'yes';
                            if ($user->save()) {
                                $activate_code->delete();
                                Yii::app()->mailer->send($user->email, "账户 $user->email 激活成功", '账户激活成功');
                                $this->smarty->renderAll('activate_success', array('message' => '账户激活成功'));
                            }
                        } else {
                            $this->smarty->renderAll('activate_fail', array('error' => '激活码错误，请确定您的激活链接正确'));
                        }
                    }
                }
            }
        } else {
            $this->smarty->renderAll('activate_fail', array('error' => '不是有效的激活链接'));
        }
    }

    public function actionResetPassword()
    {
        if (isset($_REQUEST['user_id']) && isset($_REQUEST['key'])) {
            $user = User::model()->findByPk($_REQUEST['user_id']);

            if (empty($user)) {
                $this->smarty->renderAll('reset_password_form_fail', array('message' => '不是一个合法的修改密码链接'));
            } else {
                $change_password_key = $user->getOperationKey('change_password');
                if (!empty($change_password_key)) {
                    if (Yii::app()->request->isPostRequest) {
                        if (isset($_REQUEST['password'])) {
                            $user->changePassword($_REQUEST['password']);
                            if ($user->save()) {
                                $block_key = $user->generateOperationKey('block');
                                $change_password_key->delete();
                                Yii::app()->mailer->send($user->email, '修改密码成功', $this->smarty->fetchString('reset_password_success_email', array('user' => $user, 'key' => $block_key)));
                                $this->smarty->renderAll('reset_password_success', array('user' => $user));
                            } else {
                                $this->smarty->renderAll('reset_password_form_fail', array('message' => '保存用户密码时出错'));
                            }
                        } else {
                            $this->smarty->renderAll('reset_password_form_fail', array('message' => '没有指定用户密码'));
                        }
                    } else {
                        $this->smarty->renderAll('reset_password_form', array('key' => $change_password_key));
                    }
                } else {
                    $this->smarty->renderAll('reset_password_form_fail', array('message' => '未获得修改密码的合法权限或者连接过期'));
                }
            }

        } else {
            $this->smarty->renderAll('reset_password_form_fail', array('message' => '未获得修改密码的权限'));
        }
    }

    public function actionRetrievePassword()
    {
        $messages = array();
        $email = '';
        if (Yii::app()->request->isPostRequest && isset($_POST['email'])) {
            $email = $_POST['email'];
            $user = User::model()->find("email=:email", array('email' => $_POST['email'],));
            if (!empty($user)) {
                $change_password_key = $user->generateOperationKey('change_password');

                Yii::app()->mailer->send($user->email, '修改密码链接',
                    $this->smarty->fetchString('retrieve_password_email',
                        array(
                            'user' => $user,
                            'key' => $change_password_key
                        )));
                $messages[] = '重置密码的链接已经发送到您的邮箱，请打开邮箱查看';

            } else {
                $messages[] = '用户不存在，请确认邮箱没有拼写错误';
            }
        }
        $this->smarty->renderAll('retrieve_password_form', array('messages' => $messages, 'email' => $email));

    }

    public function actionBlock($user_id, $key)
    {
        $user = User::model()->findByPk($user_id);
        if (!empty($user)) {
            $block_key = $user->getOperationKey('block');
            if (!empty($block_key) && $block_key->key == $key) {
                $user->blocked = 'yes';
                if ($user->save()) {
                    $block_key->delete();
                    $this->smarty->renderAll('block_success', array('user' => $user));
                }
            } else {
                $this->smarty->renderAll('block_fail', array('message' => '冻结账户的链接已过期或不可用'));
            }
        } else {
            $this->smarty->renderAll('block_fail', array('message' => '冻结账户的链接已过期或不可用'));
        }
    }
}
