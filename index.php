<?php
/**
 * Simple Configuration
 * 
 * Heroku uses PosgreSQL with PDO url, we use SQLite for local compatibility
 * On Heroku we must reparse the connection string
 */
$config['db']['url'] = 'sqlite://' . realpath('data/my.db');
$config['db']['driver'] = 'sqlite';

$config['app']['home'] = 'https://herolinks.heroku.com';

/**
 * Facebook app details
 */
$config['facebook']['appId'] = 'FACEBOOK_APP_ID';
$config['facebook']['secret'] = 'FACEBOOK_APP_SECRET';
$config['facebook']['canvas'] = 'herolinks';
$config['facebook']['canvas_url'] = 'https://apps.facebook.com/herolinks/';


// if $_ENV['DATABASE_URL'] the app is running on Heroku
if (!empty($_ENV['DATABASE_URL'])) {
	
	// Our app receives a string like this: postgres://username:password@host/database
	// Which must be converted to: pgsql:user=username;password=password;host=host;dbname=database
	$dburl = parse_url($_ENV["DATABASE_URL"]);
	$config['db']['url'] = sprintf("pgsql:user=%s;password=%s;host=%s;dbname=%s", $dburl['user'], $dburl['pass'], $dburl['host'], trim($dburl['path'], '/'));
	$config['db']['driver'] = $dburl['schema'];
	
} // end if

/**
 * Step 1: Require the Slim PHP 5 Framework...
 */
require_once 'lib/Slim/Slim.php';

// ...add other accessory libraries
require_once 'lib/db/db.class.php';
require_once 'lib/cake/sanitize.php';
require_once 'lib/cake/validation.php';

// and then the Facebook SDK
require 'lib/facebook/facebook.php';

/**
 * Step 2: Instantiate the Slim application
 *
 * Here we instantiate the Slim application with its default settings.
 * However, we could also pass a key-value array of settings.
 * Refer to the online documentation for available settings.
 * 
 * Default templates directory is './templates'.
 */
$app = new Slim();

// Get the Facebook connector
$app->Facebook = new Facebook($config['facebook']);

// Set some global facebook-related variables
$app->facebookCanvas = FALSE;
$app->facebookUserProfile = null;

// Check if running inside a facebook canvas 
// It's valid only for the home page which is called wit POST in the iFrame
if (ImRunningInsideFacebook()) $app->facebookCanvas = TRUE;

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function. If you are using PHP < 5.3, the
 * second argument should be any variable that returns `true` for
 * `is_callable()`. An example GET route for PHP < 5.3 is:
 *
 * $app = new Slim();
 * $app->get('/hello/:name', 'myFunction');
 * function myFunction($name) { echo "Hello, $name"; }
 *
 * The routes below work with PHP >= 5.3.
 */

/**
 * Default GET route
 * 
 * Lists the latest N links
 */
$app->map('/', 'FacebookInit', function () use ($app, $config) {

	$pageTitle = 'Latest Links';
	
	$action = 'home';

	$links = array();
	
	if ($db = Db::getConnection()) {

		$query = "SELECT * FROM links ORDER BY created DESC LIMIT 10";

		try {

			foreach ($db->query($query) as $link) {
				$links[] = $link;
			} // end foreach

		} catch (PDOException $e) {
			$app->flashNow('error', $e->getMessage());
		} // end if

	} else {
		$app->flashNow('error', "Unable to open DB");
	} // end if

	$app->render('home.php', array(
		'pageTitle' => $pageTitle,
		'action' => $action,
		'facebookCanvas' => $app->facebookCanvas,
		'fbUserProfile' => $app->facebookUserProfile,
		'links' => $links,
		));

})->via('GET', 'POST');

/**
 * Default POST route
 *
 * Validates input and inserts URL in database, redirects to index
 */
$app->map('/new', 'FacebookInit', function () use ($app, $config) {
	
	$pageTitle = 'Add new link';
	
	$action = 'new';
	
	$data = array();
	$errors = array();
	
	if ($app->request()->isPost()) {
		
		// Sanitize
		$data = $app->request()->post();
		$data = Sanitize::clean($data, array('escape' => FALSE));
		
		// Validate
		
		$valid = Validation::getInstance();

		if (!$valid->email($data['useremail'])) {
			$errors['useremail'] = 'Invalid email address';
		} // end if

		if (!$valid->notEmpty($data['username'])) {
			$errors['username'] = 'Please insert your name';
		} // end if

		if (!$valid->notEmpty($data['title'])) {
			$errors['title'] = 'Please insert a title';
		} // end if

		if (!$valid->url($data['url'])) {
			$errors['url'] = 'Invalid or empty URL';
		} // end if

		// Insert
		if (empty($errors)) {
			
			if ($db = Db::getConnection()) {

				$query = "INSERT INTO links (url, title, description, username, useremail) VALUES(:url, :title, :description, :username, :useremail)";

				try {
					$stmt = $db->prepare($query);
					
					$stmt->bindParam(':url',         $data['url']);
					$stmt->bindParam(':title',       $data['title']);
					$stmt->bindParam(':description', $data['description']);
					$stmt->bindParam(':username',    $data['username']);
					$stmt->bindParam(':useremail',   $data['useremail']);

					$stmt->execute();
					$app->flash('info', "Link added successfully!");
					$app->redirect('/');
					
				} catch (PDOException $e) {
					// Both the database and the directory must have write permissions
					$app->flashNow('error', "Unable to save your URL: " . $e->getMessage());
				} // end try

			} else {
				$app->flashNow('error', "Unable to open DB");
			} // end if

		} // end if

	} // end if
	
	$app->render('new.php', array(
		'pageTitle' => $pageTitle,
		'action' => $action,
		'data' => $data,
		'errors' => $errors,
		'facebookCanvas' => $app->facebookCanvas,
		'fbUserProfile' => $app->facebookUserProfile,
		));

})->via('GET', 'POST');

/**
 * Search GET route
 * 
 * Search the term, if empty display the searchbox
 */
$app->get('/search(/:key)', 'FacebookInit', function($key = null) use ($app, $config) {
	
	$pageTitle = 'Link Search';

	$action = 'search';
	
	$links = array();
	
	if ($app->request()->isGet()) {
		
		if (empty($key)) $key = $app->request()->get('key');
		$key = Sanitize::clean($key, array('escape' => FALSE));

	} // end if

	if ($db = Db::getConnection()) {

		$query = "SELECT * FROM links WHERE (title LIKE :key OR url LIKE :key) ORDER BY created DESC";

		try {
			
			$stmt = $db->prepare($query);
			$needle = '%' . $key . '%';
			$stmt->bindParam(':key', $needle, PDO::PARAM_STR);

			$stmt->execute();

			while ($link = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$links[] = $link;
			} // end while

		} catch (PDOException $e) {
			$app->flashNow('error', "Unable to execut search: " . $e->getMessage());
		} // end try

	} else {
		$app->flashNow('error', "Unable to open DB");
	} // end if

	$app->render('search.php', array(
		'pageTitle' => $pageTitle,
		'action' => $action,
		'key' => $key,
		'links' => $links,
		'facebookCanvas' => $app->facebookCanvas,
		'fbUserProfile' => $app->facebookUserProfile,
		));

});

/**
 * Apply Install Hook (SQLite only!)
 */
$app->get('/install', function() use ($app) {

	global $config;

	// Check driver and perform install only for SQLite/local
	if ($config['db']['driver'] == 'sqlite') {

		if ($db = Db::getConnection()) {

			$query = "CREATE TABLE IF NOT EXISTS links (
				id INTEGER PRIMARY KEY, 
				url VARCHAR (255), 
				title VARCHAR (100), 
				description VARCHAR (512), 
				username VARCHAR (50), 
				useremail VARCHAR (100), 
				created DATE DEFAULT (datetime('now','localtime'))
				);";

			try {
				$smtm = $db->prepare($query);
				$smtm->execute();
				$app->flash('info', "Application installed successfully!");
				$app->redirect('/');

			} catch (PDOException $e) {
				$app->flashNow('error', "Unable to install application: " . $e->getMessage());
			} // end try

		} else {
			$app->flashNow('error', "Unable to open DB");
		} // end if

	} else {
		
		// Display a waring and a suggested action
		$app->flashNow('info', "Install command is for local/SQLite only, try to run <code>heroku db:push sqlite://data/my.db</code> instead!");
	} // end if
	
	$app->render('default.php', array(
		'action' => 'install',
		));
	
});


/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This is responsible for executing
 * the Slim application using the settings and routes defined above.
 */
$app->run();


/// FUNCTIONS
/**
 * Checks if we are running inside a Facebook canvas
 */
function ImRunningInsideFacebook() {
	
	if (!empty($_REQUEST['signed_request'])) {
		return TRUE;
	} // end if

	// Optional condition
	// && preg_match('/^(http|https):\/\/apps\.facebook\.com.*$/i', $_SERVER['HTTP_REFERER'])
	
	return FALSE;
	
} // end function

/**
 * Performs Facebook data binding and authorization
 */
function FacebookInit() {

	global $app;
	global $config;

	// Get and parse Facebook signed request
	if ($fbData = $app->Facebook->getSignedRequest()) {

		// Redirect to Facebook Authorization if the User ID is not in the request
		if (empty($fbData["user_id"])) {
			$auth_url = "https://www.facebook.com/dialog/oauth?client_id=" 
			            . $config['facebook']['appId'] . "&redirect_uri=" . urlencode($config['app']['home'])
			 			. '&scope=email';
			echo("<script> top.location.href='" . $auth_url . "'</script>");exit;
		} // end if
		
	} // end if
	
	// Result of an authorized Facebook access
	if ($fbAccessToken = $app->Facebook->getAccessToken()) {

		// If code is present it was sent by an auth request, so we redirect back to Facebook App Page
		if ($code = $app->request()->get('code')) {
			$app->redirect($config['facebook']['canvas_url']);
		} // end if
		
		$user = $app->Facebook->getUser();
		if ($user) {
		  try {

		    // Proceed knowing you have a logged in user who's authenticated.
		    $app->facebookUserProfile = $app->Facebook->api('/me');
			$app->facebookUserProfile['logout'] = $app->Facebook->getLogoutUrl();
			
		  } catch (FacebookApiException $e) {
			$app->flashNow('error', $e->getMessage());
		    $user = null;
		  } // end try
		} // end if

	} // end if
	
} // end function
