<?php
/**
 * The ReController.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-09-03 19:34:29
 *
 * This is the test experimental controller to handle the RPC request from user.
 *
 * @since 1.0.6
 */

namespace web;

use App;
use supjos\base\ServerController;
use supjos\database\Query;
use supjos\net\Response;

class ReController extends ServerController
{
    
    function insert()
    {
        // Get the Query Object, The inherit object is PDO
        $query = Query::getQuery();
        
        $data = [];
        $result = [];
        for ( $index = 1; $index <= 1000; $index++ ) {
            $data[] = [':t' => 1 + $index, ':o' => 2 + $index, ':r' => 3 + $index];
            if ( ( $index % 1000 == 0 ) ) {
                $rowCount = $query->insert( '{{%product}}', ['t', 'o', 'r'], $data );
                
                $data = [];
                
                $result[ $rowCount ] = $query->getPreviousInsertId();
            }
        }
        App::createObject( Response::getClass() )
           ->end( 'Already to add 1000 records to database.' );
    }
    
    function booklist()
    {
        App::createObject( Response::getClass() )
           ->end( 'Book List' );
    }
    
    function bookPrice()
    {
        App::createObject( Response::getClass() )
           ->end( 'Book Price' );
    }
    
}