<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 勃
 * Date: 13-5-19
 * Time: 下午4:34
 * To change this template use File | Settings | File Templates.
 */
class CPhpCaptcha
{
    public $session_var = 'captcha';
    private $captchaObject;

    public function init()
    {
        Yii::import('ext.php-captcha.SimpleCaptcha');
        $this->captchaObject = new SimpleCaptcha();
        $this->captchaObject->resourcesPath = Yii::getPathOfAlias('ext.php-captcha.resources');
    }

    public function createImage()
    {
        $this->captchaObject->CreateImage();
    }

    public function text()
    {
        return $_SESSION[$this->session_var];
    }

    public function validate($input,$caseSensitive=false){
        $current_captcha=$this->text();
        if(!$caseSensitive){
            $input=strtolower($input);
            $current_captcha=strtolower($current_captcha);
        }
        return $input==$current_captcha;
    }
}
