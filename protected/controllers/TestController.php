<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bolu
 * Date: 13-5-14
 * Time: PM12:40
 * To change this template use File | Settings | File Templates.
 */
class TestController extends Controller
{
    function actionIndex(){
        $user=User::model()->findByPk(2);
        var_dump( $user->getOperationKey('block'));
    }
}
