# Speed #
			Most popular and slim PHP framework																						Josin <www.supjos.com|774542602@qq.com>

## Simple Example ##

	use App;
	
	$app = new App();
	
	$app->get('/', function($req, $res){
	
		if ($req->getPathInfo() == '/name') {
			$res->end("Josin");
		} else if ($req->getPathInfo() == '/age') {
			$res->end(29);
		} else {
			$res->end("<div style='width: 280px;margin:0 auto;font-size: 30px;'><strong>Speed</strong> Framework &nbsp; <sub style='font-size: 12px;'>v". App::getVersion() ."</sub></div>");
		}
	});


## Features ##

### IOC ###

The IOC was implemented by a components class named [[ Reflex ]], User can use the IOC class to use the Object-Create process more convinent.

For example:
	
	use supjos\reflection\Reflex;
	
	// Get a IOC containers
	$container = App::createObject(Reflex::getClass());
	
	// The example class named [[ Base ]]
	class Base
	{
		// Has a property named version
		private $version;
	}
	
	// The other class named [[ A ]]
	class A
	{
		// One property
		private $features;
		
		// The Base object injected into A class instance
		private $base;
		
		public function __construct(Base $base)
		{
			$this->base = $base;
		}
	}
		
	// The other class named [[ B ]]
	class B
	{
		// One property
		private $codes;
		
		// The Base object injected into A class instance
		private $a;
		
		public function __construct(A $a)
		{
			$this->a = $a;
		}
	}
	
	// If you want to get the Class B's instance, or A's instance from IOC container, you must set it into the container, using the $container->set() method:
	
	// Set the Class with the properties
	$container->set('Base', ['version'=>'1.0.2']);
	
	$container->set('A', ['features'=>'180/82A']);
	
	$container->set('B', ['codes'=>'C,C++,PHP,Redis,MySQl, Linux'];
	
	After set the class into the container,you can get the each class object which in the container:
	
	$a = $container->get('A')
	
	// or The B's instance
	$b = $container->get('B')
	
	// or the Base class object
	$base = $container->get('Base');
	
### Universal ```Object-Create``` Process ###
	
Each class in the** Speed **Framework can be created by the static method: App::createObject(), for example:

	use App;
	
	$object = App::createObject('Object');
	
	// Or if you want to attach some owner properties on the created object, pass the second paramenter with an array:
	
	$object = App::createOjbect('Object', ['a'=>'aa', 'b'=>'bb']);
	
	// Or if the class has the constructor parameter, change the first parameter into an array, container a ```class``` key, the class key value equal to the Class name you want to create, and the left values, will be passed to the constructor:
	
	class Base
	{
		public $name;
		public $version;
		
		public function __constructor($name, $version)
		{
			$this->name  = $name;
			$this->version = $version;
		}
	}
	
	// if you have the class like the before class named [[ Base ]], you can use the App::createObject to create the object for the Base class:
	
	$base = App::createObject(['class'=>'Base', 'Speed', '1.1.8']);
	
	// Then the created object ```$base``` has two properties and it's value like this:
	
	$base->$name = 'Speed';
	$base->version = '1.1.8';
	
### API ###

The** Speed ** framework was designed to the api projects, also for the WEB applications.

If you want to create a application, which want a better and more easy using, deal the** GET ** and** POST ** request, if you using the** Speed **, you can writing the code like below:

For example:

	use App;
	
	$app = new App();
	
	// The following two method, will catch all the GET & POST  request
	
	// [[ NOTICE]] : The first parameter can accept the PCRE-Regex pattern string, such as below:
	// The method means is accept the method url begin with the string "ver" and end with "dd"
	// $app->get('/ver(.*)dd$', function($req, $res){  });
	
	
	$app->get('/', function($request, $response){
	
		/**
		 * In this method, you can deal with the logic thing as so easy.
		 * As each GET request will be passed into this method
		 */
		 
		 $response->end('Hello, Speed Framework');
	});
	
	$app->post('/', function($request, $response){
	
		/**
		 * Each POST request will be arrived at this method, do the logic thing and return the 
		 * necessary data, with the correct format, such as 'JSON' or 'XML'
		 */
		 
		 // You should know that the three method only the first one will be functioned.
		 
		 // Only return the raw string data
		 $response->end('Hello, Speed Framework");
		 
		 // Return the JSON data
		 $response->asJson(['name'=>'Josin', 'age'=>25]);
		 
		 // Return the XML data
		 $response->asXml(['name'=>'Josin', 'age'=>25]);
		 
	});
	
### WEB Applications ###

Also the** Speed ** framework can be used to develop the WEB applications, as the** Response ** class provide some method to redering the template.

For example:

	use App;
	use supjos\database\Query;
	
	$app = new App();
	
	$app->get('/goods', function($req, $res){
	
		// Before using the Query class, you must config the [[ config.php ]] 
		$query = Query::getQuery();
		
		$goodLists = $query->select(['id', 'good_name', 'good_price'])
					     ->from("{{%good}}")
					     ->where(['uid'=>$req->get('uid')])
					     ->orderBy(['good_price'=>SORT_ASC])
					     ->all();
		// Rendering the index template, and pass the goodsinfo, so you can
		// In the index.php file, do some turning.
		$res->render('@webRoot/goods/index', ['goods'=>$goodLists]);
	});
	
### More features waiting you to join us, So let participating the Speed projects. ###
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	