<?php

/**
 * Hello Everyone, using the Speed Framework, There some ideas for you to use the Speed framework easily
 * this index.php file are an example for using the Speed framework. wish to help you.
 *
 * First: ```Speed``` PHP framework made from Composer and use the composer to manage it.
 *
 * So everyone who want to use the Speed and want to have a try, run the command: [Suppose you have installed the
 * composer in you computer, like Linux|Unix|Mac OS X|Windows ]:
 *
 * ```composer require supjos/speed```
 *
 * After the composer installed the Speed framework, what the thing you want to to is only require the composer
 * autoload
 * file like below:
 * ```` require('vendor/autoload.php')````
 *
 * After doing the require job. You can enjoy you Speed programming experience.
 *
 * In each class other than App, You can use the \App to access the Global class \App or simply use the ```use App```
 * command
 *
 * In the template file, or in the rendering view file, You can use App directly.
 *
 *
 * An simple example for Speed framework like below:
 *
 * $app = new App();
 *
 * // The first parameter means the routes, which the closure will deal with the request, support the PCRE-Regex, like
 *    $app->get('/a[cde]', function($request, $response){ });
 *    Or $app->get('/a[cde|cd]|ver*', function($request, $response){ });
 * $app->get('/', function($request, $response){
 *
 *      // $request ===> The instance of the class ```supjos\net\Request```
 *      $pathInfo = $request->getPathInfo();
 *
 *      // $response ===> The instance of the class ````supjos\net\Response```
 *      if ($pathInfo == '/version') {
 *          $response->end('1.1.2');                // Return the string to the browser or client directly
 *      } else if($pathInfo == '/goods') {
 *          $response->asJson(['Apple', 'Pear']);   // Return the JSON string to the browser or client directly
 *      } else if($pathInfo == '/cloth') {
 *          $response->asXml(['Shorts', 'T-Shirts]);// Return the XML data to the client who request
 *      }
 *
 *      As you see before, In this closure, you can handle any routes, which were ```GET``` request
 *      There are other method, such as:
 *          $app->post('/update', function($req, $res){});
 *          $app->delete('/delete/1', function($req, $res){});
 *          $app->options('/options', function($req, $res){});
 *          and so on...
 * });
 *
 * Now the Speed supported much features, such as Zip|Reflex(IOC)|Excel|Query(PDO), below is the summary of these
 * components:
 *
 *  supjos\database\Query
 *          $query = \supjos\database\Query::getQuery();
 *          $query->update('UPDATE {{%product}} SET [[o]]=:o WHERE [[id]]=:id', [':o'=>15, ':id'=>1]);
 *          $query->delete( 'DELETE FROM {{%product}} WHERE [[id]] = :id', [ ':id' => 1 ] );
 *          $query->insert( 'ord', ['oid', 'gid', 'much'], [[3, 33, 333], [4, 55, 555], [5, 77, 777], [6, 99, 999]] );
 *          $query->select('*')->from( '{{%product}}' )->orderBy( ['id' => SORT_DESC] )->limit( 5 )->all();
 *
 * supjos\tool\Zip
 *          $zip = App::createObject(Zip::getClass());  // Use the universal object-create method to create instance
 *          $zip->addFile(['b.txt', 'c.xlxs', 'd.php', 'e.java']);
 *          $zip->createZip('all.zip');
 *
 * supjos\tool\Excel
 *          $excel = Excel::getInstance();
 *          $excel->setHeader(['name', 'age', 'score']);
 *          $excel->setBody([['Josin', 25, 99],['Liming', '23', '88]]);
 *          $excel->exportFile('score');
 *
 * More features waiting you to join us.
 *
 * This index.php are the test example for the Speed framework.
 *
 * License: MIT
 * Author: Josin 774542602@qq.com
 * Copyright 2017-2020 www.supjos.cn All Rights Reserved.
 */
require( 'vendor/autoload.php' );

$app = new App();

use supjos\cache\sRedis;
use supjos\database\Query;
use supjos\net\Request;
use supjos\net\Response;
use web\ReController;

/**
 * Handle the Re-request, the handle URL like [[ /re/xxx ]], not match the url like [[ /re/xxx/xxx ]]
 *
 * Here we use the RegularExpression to match the URL, and let the class [[ ReController ]] to handle the absolute
 * request, Here are some options:
 *
 * If the class [[ ReController ]] derived from the [[ supjos\base\ServerController ]], and let the class to handle the
 * request like [[ /re/[^/]+$ ]], then :
 *
 * [[ /re/price-list ]] will let the [[ ReController ]] 's [[ priceList ]] method to handle the request
 * [[ /re/update ]] wil let the [[ ReController ]] 's [[ update ]] method to handle the request
 * and so on.
 */
$app->get( '/re/[^/]+$', ReController::getClass() );


// Handle the /list/23 or /list/88 and so on end with two digits
$app->get( '/list/[\w+]{1,2}$', function ( Request $req, Response $res ) {
    
    // Before using the redis extension, you must install redis and config the redis in config.php file default
    // or other file, if you change the config file
    $redis = sRedis::getRedis();
    
    for ( $rIndex = 1; $rIndex <= 200; $rIndex++ ) {
        if ( $redis->get( "request" ) ) {
            $redis->incr( 'request' );
        } else {
            $redis->set( 'request', 1 );
        }
    }
    
    $res->end( "<h1>Welcome to use the Speed framework， This is the GET routes handler.</h1>" );
} );

/**
 * The SQL using the prepare statements to avoid SQL attack.
 * // Use the update command to update the data int database's table named xx_product
 * echo $query->update('UPDATE {{%product}} SET [[o]]=:o WHERE [[id]]=:id', [':o'=>15, ':id'=>1]);
 *
 * // To delete the data-row-set where id = 1
 * echo $query->delete( 'DELETE FROM {{%product}} WHERE [[id]] = :id', [ ':id' => 1 ] );
 *
 * $query->beginTransaction();
 * echo $query->insert( 'ord', ['oid', 'gid', 'much'], [[3, 33, 333], [4, 55, 555], [5, 77, 777], [6, 99, 999]] );
 * $query->commit();
 *
 */

// To handle the /verxx starting with string [[ /ver ]] url with the delete request method
$app->delete( '/ver(.*)', function ( Request $request, Response $response ) {
    $query = Query::getQuery();
    
    $data = $query->select( '*' )
                  ->from( '{{%product}}' )
                  ->orderBy( ['id' => SORT_DESC] )
                  ->limit( 5 )
                  ->all();
    $userData = $query->select( ['id', 'username', 'email', 'updated_at'] )
                      ->from( 'user' )
                      ->all();
    $response->asJson( $userData );
} );


// To handle the Post request with the URL path-info equal to '/name' or start with '/name'
$app->post( '/name', function ( Request $request, Response $response ) {
    
    // Get the PDO Object
    $query = Query::getQuery();
    
    // Get the row-sets which you want to search from the database
    $data = $query->select( '*' )
                  ->from( '{{%product}}' )
                  ->orderBy( ['id' => SORT_DESC] )
                  ->limit( 3 )
                  ->all();
    
    // Return the JSON format for the client
    $response->asJson( $data );
} );

/**
 * The default method to add 1000 records to database for each request
 * Let the left request to the default request
 */
$app->get( '/', ReController::getClass() );

/**
 * The default OPTIONS request
 */
$app->options('/', function(Request $req, Response $res){
    $res->end('Hello OPTIONS method.');
});

/**
 * The default DELETE request
 */
$app->delete('/', function(Request $req, Response $res){
    $res->end('Hello DELETE method.');
});

/**
 * The default PATCH request
 */
$app->patch('/', function(Request $req, Response $res){
    $res->end('Hello PATCH method.');
});

/**
 * The default POST request
 */
$app->post('/', function(Request $req, Response $res){
    $res->end('Hello POST method.');
});

/**
 * The default PUT request
 */
$app->put('/', function(Request $req, Response $res){
    $res->end('Hello PUT method.');
});