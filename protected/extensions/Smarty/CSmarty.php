<?php
/**
 *Author:Elite
 */
require_once (Yii::getPathOfAlias('ext.Smarty') . DIRECTORY_SEPARATOR . 'Smarty.class.php');

class CSmarty extends Smarty
{
    const DIR_SEP = DIRECTORY_SEPARATOR;
    private $module_template_dir_key = NULL;
    public $templateDirs;

    function __construct()
    {
        parent::__construct();

        $this->addTemplateDir(Yii::getPathOfAlias('application.templates'), '0');
        $this->setCompileDir(Yii::getPathOfAlias('application.runtime.smarty_templates_c'));
        $this->setConfigDir(SMARTY_DIR . 'configs');
        $this->setCacheDir(Yii::getPathOfAlias('application.runtime.smarty_cache'));
        $this->caching = false;

        $this->left_delimiter = '{';
        $this->right_delimiter = '}';
        $this->cache_lifetime = 3600;
        $this->debugging = false;
    }

    function init()
    {
        foreach ($this->templateDirs as $key => $val) {
            $this->addTemplateDir(Yii::getPathOfAlias($val), $key);
        }
        if (!empty(Yii::app()->controller->module)) {
            $this->module_template_dir_key = Yii::app()->controller->module->id;
            $this->addTemplateDir(Yii::app()->controller->module->basePath . '/templates', $this->module_template_dir_key);
        } else {
            $this->module_template_dir_key = 0;
        }
    }

    public function assignValue($array)
    {
        if ($array === null) {
            return;
        }
        if (!is_array($array)) {
            throw new Exception("template value should be an array", 1);
        }
        foreach ($array as $key => $val) {
            $this->assign($key, $val);
        }
    }

    /**
     * 规范化template变量，是指符合条件
     * @param $template 模板名或者路径
     * @param $use_module_template_dir 是否使用模块中的templates目录
     * @return string 规范化后的template变量
     * @throws Exception 如果从绝对路径开始，但是没有提供template变量，则抛出异常
     */
    private function normalizeTemplate($template, $use_module_template_dir)
    {
        if ($template == NULL) {
            $template = Yii::app()->controller->id . '/' . Yii::app()->controller->action->id . '.tpl';
        } else {
            //if param template is not end with .tpl, then append $template with it
            $template = Common::endsWith($template, '.tpl') ? $template : $template . '.tpl';
            //if param template start with '/', then use absolute path
            if(Common::startsWith($template,'/')){
                $template=ltrim($template,'/');
            }else{
                $template = Yii::app()->controller->id . '/' . $template;
            }
        }

        //如果使用模块内的templates目录，则需要指明templateDir的key
        if ($use_module_template_dir) {
            $template_path = 'file:[' . $this->module_template_dir_key . ']' . $template;
        } else {
            $template_path = 'file:[0]' . $template;
        }
        return $template_path;
    }

    /**
     * 获取通过Common::register()和Common::registerOutController()注册的静态文件的html代码
     * @return string css/js等静态文件的html代码
     */
    protected function getScripts()
    {
        $scripts = '<head><title></title></head>';
        Yii::app()->clientScript->render($scripts);
        $scripts = substr($scripts, 6, -22);
        return $scripts;
    }

    /**
     * smarty根据注册的变量渲染相应的模板
     * @param null $template 模板路径
     * @param array $array   注册变量的数组
     * @param bool $use_module_template_dir 是否使用模块中的templates目录
     */
    function render($template = NULL, $array = array(), $use_module_template_dir = true)
    {
        $this->assignValue($array);
        $template = $this->normalizeTemplate($template, $use_module_template_dir);

        $this->display($template);
    }


    /**
     * render template and put the content into layout
     * @param null $template
     * @param array $array
     * @param string $layout
     * @param bool $use_module_template_dir
     */
    function renderAll($template = NULL, $array = array(), $layout = 'file:[0]layouts/main.tpl', $use_module_template_dir = true)
    {
        $this->assignValue($array);
        $template = $this->normalizeTemplate($template, $use_module_template_dir);
        $this->assign('script_tpl', $this->getScripts());
        $this->assign('content_tpl', parent::fetch($template));
        $this->display($layout);
    }

    function fetchString($template = NULL, $array = array(), $use_module_template_dir = true)
    {
        $this->assignValue($array);
        $template = $this->normalizeTemplate($template, $use_module_template_dir);
        return parent::fetch($template);
    }

}

