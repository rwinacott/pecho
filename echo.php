<?php
/*
 * To Run: php -S localhost:8080 echo.php
 *
 * to call: curl -i \
  -H "Content-type: application/json" \
  -H "Accept: application/json" \
  -X POST \
  -d '{
      "username":"xyz",
      "password":"xyz"
  }' \
  localhost:8080/api/v1/echo
 */

/**
 * Send a time stamped message to standard out with the local servers IP address. 
 * Only timestamp new lines. If there is a continued line of text, do not add
 * (prepend) the time stamp and IP address information. 
 * 
 * @param string $msg The message to send to stdout. 
 * @return void
 */
function logMsg($msg) {
    $localIP = getHostByName(getHostName());
    static $newLine = true;

    if ($newLine) {
        file_put_contents("php://stdout", date("Y-m-d H:i:s", time())." ".getHostByName(getHostName()).": ");
        $newLine = false;
    }

    if (strstr($msg, "\n")) {
        $newLine = true;
    }
    file_put_contents("php://stdout", $msg);
}

/*
 * ------ Live Code starts here -------
 */

date_default_timezone_set('America/Toronto');

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
$payload = array(
    "request" => $input,
    // Just some junk to return.
    "result" => array(
        "item1" => "Some Value",
        "item2" => "Some other value",
        "flag" => true,
        "count" => 123,
        "call_path" => $request,
        "Server_path" => trim($_SERVER['PATH_INFO']),
        "Server" => $_SERVER
    )
);
// Show what is happing as debug output from this server.
logMsg("Requested: ".trim($_SERVER['REQUEST_METHOD']).":".trim($_SERVER['REQUEST_URI'])." ");
// Internal header not used at this time.
header("X-SK-CODE: ".$_SERVER['REQUEST_TIME']);

// create SQL based on HTTP method
switch ($method) {
    case 'GET':
        // Show a simple page with instructions on it.
        header("Content-type: text/html; charset=UTF-8", true);
        print("<html>\n<body>\n");
        print("<p>This echo service will only respond to <b>POST</b> requests. Try running the following <i>curl</i> call</p>\n");
        print("<pre>".
'curl -i \
	-H "Content-Type: application/json" \
	-H "Accept: application/json" \
	-H "X-HTTP-METHOD-Override: PUT" \
	-X POST \
	-d \'{
		"username":"xyz",
		"password":"xyz"
		}\' \
	localhost:8888/api/v1/echo'.
        "\n</pre>\n");
        print("<p>In a single line:</p>\n");
        print('<pre>curl -i -H "Content-Type: application/json" -H "Accept: application/json" ');
        print('-H "X-HTTP-METHOD-Override: PUT" -X POST -d \'{"username":"xyz","password":"xyz"}\' ');
        print('localhost:8888/api/v1/echo</pre>'."\n");
        print("</body>\n</html>\n");
        break;

    case 'PUT':
    case 'DELETE':
        logMsg("Response Code:501\n");
        http_response_code(501);
        break;

    case 'POST':
        // Set the returned content type to JSON
        header("Content-type: application/json; charset=UTF-8", true);
        logMsg("Response Code:200\n");
        print(json_encode($payload, JSON_PRETTY_PRINT)."\n");
        http_response_code(200);
        break;

    default:
        logMsg("Response Code:500\n");
        http_response_code(500);
        break;
}
exit(0);
