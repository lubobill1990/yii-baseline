<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 勃
 * Date: 13-7-15
 * Time: 下午12:38
 * To change this template use File | Settings | File Templates.
 */
class DoubanCaptchaAction extends CCaptchaAction
{
    public function run()
    {
        Yii::app()->captcha->createImage();
    }

    public function validate($input, $caseSensitive)
    {
       return Yii::app()->captcha->validate($input,$caseSensitive);
    }
}
