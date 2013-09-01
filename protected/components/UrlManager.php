<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 勃
 * Date: 13-9-1
 * Time: 下午9:40
 * To change this template use File | Settings | File Templates.
 */
class UrlManager extends CUrlManager
{
    public function parseUrl($request)
    {
        $ret_val = str_replace("-", "", parent::parseUrl($request));
        return $ret_val;
    }
}
