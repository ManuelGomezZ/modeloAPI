<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"'); 

include_once '../include/Config.php';
include_once '../include/DbHandler.php';

require '../libs/Slim/Slim.php'; 
\Slim\Slim::registerAutoloader(); 
$app = new \Slim\Slim();


$app->post('/auto', 'authenticate', function() use ($app) {
    verifyRequiredParams(array('patente', 'vehiculo', 'estado', 'valor_permiso', 'intereses', 'multa'));

    $response = array();
    $param['patente']  = $app->request->post('patente');
    $param['vehiculo'] = $app->request->post('vehiculo');
    $param['estado']  = $app->request->post('estado');
    $param['valor_permiso']  = $app->request->post('valor_permiso');
    $param['intereses']  = $app->request->post('intereses');
    $param['multa']  = $app->request->post('multa');

    $db = new DbHandler();

    $auto = $db->createAuto($param['patente'],$param['vehiculo'],$param['estado'],$param['valor_permiso'],$param['intereses'],$param['multa']);

    if ( is_array($param) ) {
        $response["error"] = false;
        $response["message"] = "Auto creado satisfactoriamente!";
        $response["auto"] = $param;
    } else {
        $response["error"] = true;
        $response["message"] = "Error al crear auto. Por favor intenta nuevamente.";
    }
    echoResponse(201, $response);
    //return $response;
    //$app->stop();
});

$app->post('/update', 'authenticate', function() use ($app) {
    verifyRequiredParams(array('patente', 'vehiculo', 'valor_permiso', 'vehiculo_id', 'intereses', 'multa'));

    $response = array();
    //capturamos los parametros recibidos y los almacxenamos como un nuevo array
    $param['patente']  = $app->request->post('patente');
    $param['vehiculo'] = $app->request->post('vehiculo');
    $param['vehiculo_id']  = $app->request->post('vehiculo_id');
    $param['valor_permiso']  = $app->request->post('valor_permiso');
    $param['intereses']  = $app->request->post('intereses');
    $param['multa']  = $app->request->post('multa');

    $db = new DbHandler();

    $auto = $db->updateDatos($param['patente'],$param['vehiculo'],$param['valor_permiso'],$param['vehiculo_id'],$param['intereses'],$param['multa']);

    if ( is_array($param) ) {
        $response["error"] = false;
        $response["message"] = "Auto creado satisfactoriamente!";
        $response["auto"] = $param;
    } else {
        $response["error"] = true;
        $response["message"] = "Error al crear auto. Por favor intenta nuevamente.";
    }
   // echoResponse(201, $response);
    return $response;
    //$app->stop();
});


$app->run();

/*********************** USEFULL FUNCTIONS **************************************/


function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
 
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        
        $app->stop();
    }
}
 

function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoResponse(400, $response);
        
        $app->stop();
    }
}
 
/**
 * Mostrando la respuesta en formato json al cliente o navegador
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    $app->status($status_code);
 
    $app->contentType('application/json');
 
    echo json_encode($response);
}


function authenticate(\Slim\Route $route) {
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
 
    if (isset($headers['Authorization'])) {
        $db = new DbHandler();
        $token = $headers['Authorization'];
        
        if (!($token == API_KEY)) {
            
            $response["error"] = true;
            $response["message"] = "Acceso denegado. Token inválido";
            echoResponse(401, $response);
            
            $app->stop();
            
        } else {

        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Falta token de autorización";
        echoResponse(400, $response);
        
        $app->stop();
    }
}
?>