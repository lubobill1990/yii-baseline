<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'client of real-time server',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'pass',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','*'),
		),

	),

	// application components
	'components'=>array(
		'user'=>array(
            'class' => 'application.components.WebUser',
            'loginUrl' => array('/login'),
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
        'smarty'=>array(
            'class'=>'ext.Smarty.CSmarty',
            'templateDirs'=>array(
                'ext.feedback'=>'ext.feedback.templates',
            ),
        ),
        'request' => array(
            'baseUrl' => '',
        ),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
                'signup'=>'User/signup',
                'login'=>'User/login',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                '<a:\w+>'=>'Site/error'
			),
		),

		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=rts-client',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'pass',
			'charset' => 'utf8',
		),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
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
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'lubobill1990@163.com',
        'page_title'=>array(
            'default'=>'请在配置文件里配置默认标题'
        )
	),
);