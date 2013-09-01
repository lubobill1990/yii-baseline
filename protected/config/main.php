<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => '文集',
    'homeUrl' => '/',
    // preloading 'log' component
    'preload' => array('log'),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
    ),

    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'pass',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '*'),
        ),

    ),

    // application components
    'components' => array(
        'user' => array(
            'class' => 'application.components.WebUser',
            'loginUrl' => array('/login'),
            // enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        'smarty' => array(
            'class' => 'ext.Smarty.CSmarty',
            'templateDirs' => array( //                'ext.feedback'=>'ext.feedback.templates',
            ),
        ),
        'mailer' => array(
            'class' => 'ext.SwiftMailer.CSwiftMailer',
            'email' => 'npeasy@163.com',
            'password' => 'npeasypass',
            'smtpServer' => 'smtp.163.com',
            'smtpPort' => 25,
            'fromName' => "Yii baseline"
        ),

        'captcha' => array(
            'class' => 'ext.php-captcha.CPhpCaptcha',
            'session_var' => 'captcha',
        ),
        'dom' => array(
            'class' => 'ext.simple-html-dom.CSimpleHtmlDom'
        ),
        'request' => array(
            'baseUrl' => '',
        ),
        'urlManager' => array(
            'class' => 'application.components.UrlManager',
            'urlFormat' => 'path',
            'rules' => array(
                'signup' => 'Account/signup',
                'login' => 'Account/login',
                'logout' => 'Account/logout',
                '<controller:\w+>' => '<controller>/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',

                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',

            ),
        ),

        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=yii_baseline',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => 'pass',
            'charset' => 'utf8',
        ),
        'redis' => array(
            'class' => 'ext.YiiRedis.ARedisConnection',
            'hostname' => 'localhost',
            'port' => 6379,
        ),
        'htmlPurifier' => array(
            'class' => 'system.web.widgets.CHtmlPurifier'
        ),
        'fileUpload' => array(
            'class' => 'ext.jQueryFileUpload.JQueryFileUpload',
            'upload_dir' => realpath(dirname(__FILE__) . '/../../uploads') . '/',
            'script_url' => '/fileUpload/create',
            'delete_url' => '/fileUpload/delete',
            'upload_url' => '/uploads/',
            'mkdir_mode' => 0755,
        ),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                // uncomment the following to show log messages on web pages
                /*
                array(
                    'class'=>'CWebLogRoute',
                ),
                */
            ),
        ),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        'userActivateNeeded' => false,
        // this is used in contact page
        'adminEmail' => 'lubobill1990@163.com',
        'page_title' => array(
            'default' => 'Yii baseline'
        ),
        'image_versions' => array(
            // Uncomment the following version to restrict the size of
            // uploaded images:
            'large' => array(
                'max_width' => 1200,
                'max_height' => 900,
                'jpeg_quality' => 95
            ),
            // Uncomment the following to create medium sized images:
            'medium' => array(
                'max_width' => 400,
                'max_height' => 300,
                'jpeg_quality' => 80
            ),
            'thumbnail' => array(
                'max_width' => 120,
                'max_height' => 120
            )
        )
    ),
);