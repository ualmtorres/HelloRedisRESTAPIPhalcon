<?php

// Instantiate the class responsible for implementing a micro application
$app = new \Phalcon\Mvc\Micro();

// Routes
$app->get('/', 'home');
$app->get('/api', 'home');	
$app->get('/api/{key}', 'findByKey'); // curl -i -X GET http://localhost/HelloRedisRESTAPIPhalcon/api/foo
$app->post('/api', 'addKeyValue'); // curl -i -X POST -d '{"key":"foo", "value":"bar"}' http://localhost/HelloRedisRESTAPIPhalcon/api
$app->put('/api/{key}', 'updateValue'); // curl -i -X PUT -d '{"value":"asimo"}' http://localhost/HelloRedisRESTAPIPhalcon/api/foo
$app->delete('/api/{key}', 'deleteKey'); // curl -i -X DELETE http://localhost/HelloRedisRESTAPIPhalcon/api/foo
$app->notFound('notFound');

// Handlers

// Show the use of the API
function home() {

	// Describe the use of this API

	echo "<h1>Use of the API</h1>";

	echo '<table border = "3">';
	echo '<tr><td>Method</td><td>URL</td><td>Description</td><td>Use</td></tr>';
	echo '<tr><td>GET</td><td>/api/{key}</td><td>Devuelve el valor JSON a partir de la clave proporcionada</td><td>curl -i -X GET http://appLocation/api/foo</td></tr>';
	echo '<tr><td>POST</td><td>api/</td><td>Crea el nuevo par clave-valor proporcionado</td><td>curl -i -X POST -d \'{"key":"foo", "value":"bar"}\' http://appLocation/api</td></tr>';
	echo '<tr><td>PUT</td><td>api/{key}</td><td>Modifica la clave especificada con el valor proporcionado|</td><td>curl -i -X PUT -d \'{"value":"bar2"}\' http://appLocation/api/foo</td></tr>';
	echo '<tr><td>DELETE</td><td>api/{key}</td><td>Elimina la clave especificada</td><td>curl -i -X DELETE http://appLocation/api/foo</td></tr>';
	echo '</table>';
}

//Searches for data with $key in their key
function findByKey ($key) {

	// Create Redis connection
	$redis = new Redis();
	$redis->connect("localhost");

	// Prepare and send the data in JSON
	$response = array($key => $redis->get($key));
	echo json_encode($response);

	// Close the Redis connection
	$redis->close();
}

//Adds a new pair key-value
function addKeyValue() {

	// Access to the global var $app
	global $app;

	// Create Redis connection
	$redis = new Redis();
	$redis->connect("localhost");

	// Obtain the data of the request
	$requestData = json_decode($app->request->getRawBody());

	// Insert in Redis the "key" and "value" components of the request
	$redis->set($requestData->key, $requestData->value);

	// Close the Redis connection
	$redis->close();
}

//Updates values from its key
function updateValue($key) {

	// Access to the global var $app
	global $app;

	// Create Redis connection
	$redis = new Redis();
	$redis->connect("localhost");

	// Obtain the data of the request
	$requestData = json_decode($app->request->getRawBody());

	// Update in Redis the "key" of the URL with the "value" component of the request
	$redis->set($key, $requestData->value);

	// Close the Redis connection
	$redis->close();
}

//Deletes value of a key
function deleteKey($key) {

	// Create Redis connection
	$redis = new Redis();
	$redis->connect("localhost");

	// Delete in Redis the "key" of the URL
	$redis->del($key);

	// Close the Redis connection
	$redis->close();
}

function notFound() {
	home();
}

// Handle the request
$app->handle();
?>
