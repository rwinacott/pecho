<?php
/*
 * To Run: php -S localhost:8888 echo.php
 *
 * to call directly for testing: curl -i \
  -H "Content-type: application/json" \
  -H "Accept: application/json" \
  -X POST \
  -d '{
      "username":"xyz",
      "password":"xyz"
  }' \
  localhost:8080/api/v1/echo
  * 
  * You can run this as a downstream system. It will echo back the 
  * requesting data with a small change to the data structure values
  * to test the ability to change the ID data and it will return a
  * larger JSON structure to be passed back to the DAC application
  * to be consumed. 
  * 
  * Call Path: DAC application -calls-> matching service -calls-> downstream
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
// Show what is happing as debug output from this server.
logMsg("Requested: ".trim($_SERVER['REQUEST_METHOD']).":".trim($_SERVER['REQUEST_URI'])." ");
// Internal header not used at this time.
header("X-SK-CODE: ".$_SERVER['REQUEST_TIME']);
// create reply based on HTTP method
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
        logMsg("Response Code:200 + HTML\n");
        break;

    case 'PUT':
    case 'DELETE':
        logMsg("Response Code:501\n");
        http_response_code(501);
        break;

    case 'POST':
        // close this one message
        logMsg("Response Code:200\n");        
        /*
        * The reply structure is as follows:
        * 
        * requestID:               <Symbol>		# requestID provided to matchIdentities() call
        * requestTS:               <Epoch>			# Timestamp (epoch format) of call to matchIdentities()
        * lineOfBusiness:          <String>
        * applicationID:           <String>		# DI Matcher identifier for application being called
        * leftDigitalIdentity:     <DigitalIdentity>
        * rightDigitalIdentity:    <DigitalIdentity>
        * matchScore:              <Real>
        * matchStatus:             <Boolean>
        * matchThreshold:          <Int>
        * matchResult:             <String>
        * appData: [
        *      applicationID:          <String>					# REQUIRED
        *      applicationPrivateID:   <String>					# OPTIONAL
        *      applicationStatusCode:  <String>					# OPTIONAL
        *      applicationPrivateData: <ArbitraryJSONStructure>	# OPTIONAL
        * ]
        * 
        */  
        $appData = array(
            "applicationID" => "pecho-".$_SERVER['REQUEST_URI'], //$input['applicationID'],
            "applicationPrivateID" => "PEcho",
            "applicationStatusCode" => "200",
            // just put together some junk to return as private data.
            "applicationPrivateData" => array(
                "item1" => "Some Value",
                "item2" => "Some other value",
                "flag" => true,
                "count" => 123,
                "call_path" => $request,
                "Server_path" => trim($_SERVER['PATH_INFO']),
                "Server" => $_SERVER
            )
        );
        // Copy the input over to the reply output
        $payload = $input;
        // Make the small change to the input to become the new output
        if (isset($payload['leftDigitalIdentity']['profile']['confidence'])) {
            $payload['leftDigitalIdentity']['profile']['confidence'] = 99;
        }
        else {
            logMsg("ERROR: The input is missing leftDigitalIdentity->profile->confidence\n");
        }
        // Add our metadata to the payload.
        $payload['appData'] = $appData;
        // Set the returned content type to JSON
        header("Content-type: application/json; charset=UTF-8", true);
        logMsg("Input data:\n".print_r($input, true)."\n");
        logMsg("Output data:\n".print_r($payload, true)."\n");
        print(json_encode($payload, JSON_PRETTY_PRINT)."\n");
        http_response_code(200);
        break;

    default:
        logMsg("Response Code:500\n");
        http_response_code(500);
        break;
}
exit(0);
