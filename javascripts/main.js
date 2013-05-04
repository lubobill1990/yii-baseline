/**
 * Created with JetBrains PhpStorm.
 * User: bolu
 * Date: 13-5-4
 * Time: PM4:20
 * To change this template use File | Settings | File Templates.
 */
requirejs.config({
    //By default load any module IDs from js/lib
    baseUrl:'/javascripts/lib',
    //except, if the module ID starts with "app",
    //load it from the js/app directory. paths
    //config is relative to the baseUrl, and
    //never includes a ".js" extension since
    //the paths config could be for a directory.
    paths:{
        app:'../app',
        jquery:'jquery.1.9.1',
        underscore:'underscore.1.4.4',
        backbone:'backbone.1.0.0'
    },
    shim:{
        backbone:{
            deps:['underscore', 'jquery'],
            exports:'Backbone'
        },
        underscore:{
            exports:'_'
        }
    }
});

//// Start the main app logic.
//requirejs(['jquery', 'app/sub', 'underscore', 'backbone'],
//    function ($, sub) {
//        //jQuery, canvas and the app/sub module are all
//        //loaded and can be used here now.
//        console.log($)
//        console.log(sub)
//        console.log(Backbone)
//    });