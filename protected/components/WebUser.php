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
    protected $user;
    public function getUser(){
        return empty($this->user)?$this->user=User::model()->findByPk($this->id):$this->user;
    }

    public function login(UserIdentity $identity,$duration)
    {
        parent::login($identity,$duration);
    }
}
