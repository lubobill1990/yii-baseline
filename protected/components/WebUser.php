<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 勃
 * Date: 12-12-22
 * Time: 下午2:52
 * To change this template use File | Settings | File Templates.
 */
class WebUser extends CWebUser
{
    protected $user = NULL;

    public function getUser()
    {
        if ($this->user === 0) {
            return NULL;
        } elseif ($this->user === NULL) {
            $this->user = User::model()->findByPk($this->id);
            if (!empty($this->user) && $this->user->blocked == 'yes') {
                $this->logout();
                Yii::app()->controller->redirect("/account/blocked");
            }
            if (empty($this->user)) {
                $this->user = 0;
            }
        }
        return $this->user;
    }

    public function login(UserIdentity $identity, $duration)
    {
        parent::login($identity, $duration);
    }
}
