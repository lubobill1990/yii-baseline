<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

    public function init() {
        $this->smarty->assign('controller',$this);
        $this->smarty->assign('page_title',Yii::app()->params['page_title']['default']);
        Yii::import('application.components.common');

        $this->smarty->assign('login_user',Yii::app()->user->user);
        $this->smarty->assign('YiiApp',Yii::app());
    }
    private $_smarty = null;

    public function getSmarty()
    {
        return empty($this->_smarty) ? $this->_smarty = Yii::app()->smarty : $this->_smarty;
    }
    public function setPageTitle($title_array)
    {
        if (!is_array($title_array)) {
            $arg_num = func_num_args();
            $title_array = array();
            for ($i = 0; $i < $arg_num; ++$i) {
                $title_array[] = func_get_arg($i);
            }
        }

        $page_title = '';
        $coma = '';
        foreach ($title_array as $item) {
            if (empty($item)) {
                continue;
            }
            $page_title .= $coma . $item;
            $coma = ' | ';
        }
        if ($title_array[count($title_array) - 1] != Yii::app()->name) {
            $page_title .= $coma . Yii::app()->name;
        }
        $this->smarty->assign('page_title',$page_title);
        return $page_title;
    }

    protected function requireValues($array,$key_array){
        if(is_array($array)){
            $validate_array=$array;
        }else{
            $validate_array=$_REQUEST;
        }
        if (!is_array($key_array)) {
            $arg_num = func_num_args();
            $key_array = array();
            for ($i = 1; $i < $arg_num; ++$i) {
                $key_array[] = func_get_arg($i);
            }
        }

        foreach($key_array as $key){
            if(!array_key_exists($key,$validate_array)){
                return false;
            }
        }
        return true;
    }
    private $message=array();
    protected function throwMessage($key,$msg,$throw=true){
        $this->message[$key]=$msg;
        if($throw){
            throw new Exception("{$key}:{$msg}");
        }
    }
    protected function getThrownMessage(){
        return $this->message;
    }
}