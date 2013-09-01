<?php
/**
 * 和npeasy连接，发送数据到npeasy
 * User: 勃
 * Date: 13-1-14
 * Time: 上午11:57
 * To change this template use File | Settings | File Templates.
 */
class RTSMessenger
{
    protected static $postSecret = '5199DED1ECBBF664AD4376306FD45F19';
    protected static $postUrl = "http://npeasy.com:3000";

    protected static function postData($path,$array){
        $array['postSecret']=self::$postSecret;
        Common::httpPostAsync(self::$postUrl.$path,$array);
    }
    public static function sendMessage($from_user_id, $to_user_id, $content, $timestamp)
    {
        self::postData('/chat', array(
            'toUserId' => $to_user_id,
            'fromUserId' => $from_user_id,
            'timestamp' => $timestamp,
            'content' => $content,
        ));
    }

    public static function sendNotice($content,$timestamp)
    {
        self::postData('/notice', array(
            'timestamp' => $timestamp,
            'content' => $content,
        ));
    }

    public static function sendRemind($userId)
    {
        if(is_array($userId)){
            self::postData('/remind',array(
                'userIdArray'=>$userId
            ));
        }elseif(is_numeric($userId)){
            self::postData('/remind',array('userId'=>$userId));
        }
    }

    public static function publish()
    {

    }
}
