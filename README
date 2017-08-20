#Hello Everyone using the Speed Framework, There some ideas for you to use the Speed framework easily

### First:```Speed``` PHP framework made from Composer and use the composer to manage it.

So everyone who want to use the ```Speed``` and want to have a try, run the command: [Suppose you have installed the
**composer** in you computer, like Linux|Unix|Mac OS X|Windows ]:

```composer require supjos/speed```

After the composer installed the **Speed** framework, what the thing you want to to is only require the composer's autoload
file like below:
```` require('vendor/autoload.php')````

After doing the require job. You can enjoy you Speed programming experience.

In each class other than App, You can use the \App to access the Global class \App or simply use the 
	
	use App
	
command

In the template file, or in the rendering view file, You can use App directly.

An simple example for Speed framework like below:

	$app = new App();

The first parameter means the routes, which the closure will deal with the request, support the PCRE-Regex, like

	$app->get('/a[cde]', function($request, $response){ });
	
	// You can use the following function to replace the before one
	$app->get('/a[cde|cd]|ver*', function($request, $response){ });
	
	// '/' : Means to accept any routes in the current request.
	$app->get('/', function($request, $response){

	// $request ===> The instance of the class [[supjos\net\Request]]
	$pathInfo = $request->getPathInfo();

	// $response ===> The instance of the class [[supjos\net\Response]]
	if ($pathInfo == '/version') {
		$response->end('1.1.2');                // Return the string to the browser or client directly
	} else if($pathInfo == '/goods') {
		$response->asJson(['Apple', 'Pear']);   // Return the JSON string to the browser or client directly
	} else if($pathInfo == '/cloth') {
		$response->asXml(['Shorts', 'T-Shirts]);// Return the XML data to the client who request
	}

As you see before, In this closure, you can handle any routes, which were ```GET``` request
There are other method, such as

         $app->post('/update', function($req, $res){});
         $app->delete('/delete/1', function($req, $res){});
         $app->options('/options', function($req, $res){});
         
 and so on...


Now the **Speed** supported much features, such as **Zip|Reflex(IOC)|Excel|Query(PDO),** below is the summary of these components:

#### supjos\database\Query
 
         $query = \supjos\database\Query::getQuery();
         $query->update('UPDATE {{%product}} SET [[o]]=:o WHERE [[id]]=:id', [':o'=>15, ':id'=>1]);
         $query->delete( 'DELETE FROM {{%product}} WHERE [[id]] = :id', [ ':id' => 1 ] );
         $query->insert( 'ord', ['oid', 'gid', 'much'], [[3, 33, 333], [4, 55, 555], [5, 77, 777], [6, 99, 999]] );
         $query->select('*')->from( '{{%product}}' )->orderBy( ['id' => SORT_DESC] )->limit( 5 )->all();

####supjos\tool\Zip

         $zip = App::createObject(Zip::getClass());  // Use the universal object-create method to create instance
         $zip->addFile(['b.txt', 'c.xlxs', 'd.php', 'e.java']);
         $zip->createZip('all.zip');

####supjos\tool\Excel

         $excel = Excel::getInstance();
         $excel->setHeader(['name', 'age', 'score']);
         $excel->setBody([['Josin', 25, 99],['Liming', '23', '88]]);
         $excel->exportFile('score');

More features waiting you to join us.

**License: MIT
Author: Josin 774542602@qq.com
Copyright 2017-2020 www.supjos.cn All Rights Reserved.**
 
#### Below were some example for the Speed using:
 
	require( 'vendor/autoload.php' );

	$app = new App();

	// Before using the Sql Query Class you must setting the SQL config in the file [[config.php]]
	use supjos\database\Query;

	// Get the Query Object, The inherit object is PDO
	$query = Query::getQuery();

	// print_r($query->createCommand('SELECT * FROM user')->queryAll());

	$data = [];
	$result = [];
	for ( $index = 1; $index <= 1000; $index++ ) {
		// You can use the createCommand to buildQueryRaw Sql and run the queryOne()|queryAll()|execute() and so on.
	    // $row = $query->createCommand( 'INSERT INTO www_product ([[id]], [[t]], [[o]], [[r]]) VALUES (:id, :t, :o, :r)', [
	    //     ':id' => $index,
	    //     ':t'  => 222 + $index,
	    //     ':o'  => 2222 + $index,
	    //     ':r'  => 22222 + $index
	    // ] )->execute();
	    $data[] = [
			':t' => 1 + $index,
			':o' => 2 + $index,
			':r' => 3 + $index
	    ];
	    if ( ( $index % 500 == 0 ) ) {
		$rowCount = $query->insert( '{{%product}}', [
		    't',
		    'o',
		    'r'
		], $data );
		
		$data = [];
		
		$result[ $rowCount ] = $query->getPreviousInsertId();
	    }
	}
	// Use the update command to update the data int database's table named xx_product
	// echo $query->update('UPDATE {{%product}} SET [[o]]=:o WHERE [[id]]=:id', [':o'=>15, ':id'=>1]);

	// To delete the data-row-set where id = 1
	// echo $query->delete( 'DELETE FROM {{%product}} WHERE [[id]] = :id', [ ':id' => 1 ] );

	// $query->beginTransaction();
	// echo $query->insert( 'ord', ['oid', 'gid', 'much'], [[3, 33, 333], [4, 55, 555], [5, 77, 777], [6, 99, 999]] );
	// $query->commit();

	$app->get( '/ver(.*)', function ( $request, $response ) {
	    $query = Query::getQuery();
	    
	    $data = $query->select( '*' )
		          ->from( '{{%product}}' )
		          ->orderBy( ['id' => SORT_DESC] )
		          ->limit( 5 )
		          ->all();
	    $userData = $query->select( [
		                            'id',
		                            'username',
		                            'email',
		                            'updated_at'
		                        ] )
		              ->from( 'user' )
		              ->all();
	    $response->asJson( $userData );
	} );


To handle the **Post** request with the URL** path-info** equal to **'/name'**

	$app->post( '/name', function ( $request, $response ) {
	    
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

