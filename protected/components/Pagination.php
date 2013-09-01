<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bolu
 * Date: 13-1-2
 * Time: PM3:55
 * To change this template use File | Settings | File Templates.
 */
class Pagination
{
    public static $items_per_page_map=array(
        'courseResource'=>7,
        'comment'=>4,
        'article_slot'=>5,
        'article_list'=>20,
        'article_slot_index'=>5,
        'article_index'=>5,
        'article_main'=>10,
        'anthology_index'=>10,
        'anthology_user'=>10,
        'feed_index'=>2,
        'feed_main'=>15,
        'article_main'=>5,
        'message_user'=>20
    );
    /**
     * 无论怎样设置items_per_page的值，每页最多能显示多少items
     * @var int
     */
    protected static $max_items_per_page=200;
    protected static function getItemNumberPerPage($subject_type){
        if(array_key_exists($subject_type,self::$items_per_page_map)){
            return self::$items_per_page_map[$subject_type];
        }
        return 20;
    }
    public static function getParamsFromRequest($subject_type,$request){
        $page_no=null;
        if(array_key_exists('page_no',$request)){
            $page_no=$request['page_no'];
        }else{
            $page_no=0;
        }
        if(!is_numeric($page_no)){
            $page_no=0;
        }
        if(array_key_exists('items_per_page',$_REQUEST)){
            $items_per_page=$_REQUEST['items_per_page'];
            if($items_per_page>self::$max_items_per_page){
                $items_per_page=self::$max_items_per_page;
            }
        }else{
            $items_per_page=self::getItemNumberPerPage($subject_type);
        }
        $start=$page_no*$items_per_page;
        return array('page_no'=>$page_no,'start'=>$start,'items_per_page'=>$items_per_page);
    }
}
