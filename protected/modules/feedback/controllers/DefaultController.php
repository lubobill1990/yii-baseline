<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
		$this->smarty->renderAll('index');
	}
}