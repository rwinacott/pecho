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
file_put_contents("php://stdout", "Requested: ".trim($_SERVER['REQUEST_METHOD']).":".trim($_SERVER['REQUEST_URI'])." ");
// Internal header not used at this time.
header("X-SK-CODE: ".$_SERVER['REQUEST_TIME']);
// Set the returned content type to JSON
header("Content-type: application/json; charset=UTF-8", true);
// create SQL based on HTTP method
switch ($method) {
    case 'GET':
    case 'PUT':
    case 'DELETE':
        file_put_contents("php://stdout", "Responce Code:501\n");
        http_response_code(501);
        break;

    case 'POST':
        file_put_contents("php://stdout", "Responce Code:200\n");
        print(json_encode($payload, JSON_PRETTY_PRINT)."\n");
        http_response_code(200);
        break;

    default:
        file_put_contents("php://stdout", "Responce Code:500\n");
        http_response_code(500);
        break;
}
exit(0);
print("This is a test\n");
