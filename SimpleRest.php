<?php

/*
  A simple RESTful webservices base class
  Use this as a template and build upon it
 */

class SimpleRest {

    private $httpVersion = "HTTP/1.1";
    protected $username;
    protected $password;
    protected $authorized;

    function __construct() {
        $this->username = "masam_api_dev";
        $this->password = "masam%ap!#dev@super_tech";
    }

    public function setHttpHeaders($contentType, $statusCode) {

        $statusMessage = $this->getHttpStatusMessage($statusCode);

        header($this->httpVersion . " " . $statusCode . " " . $statusMessage);
        header("Content-Type:" . $contentType);
    }

    public function authorize() {
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {

            if (strpos(strtolower($_SERVER['REDIRECT_HTTP_AUTHORIZATION']), 'basic') === 0) {
                list($username, $password) = explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
                if ($username == $this->username && $password == $this->password)
                    return true;
                else
                    return false;
            }
        }
    }

    public function sendErrorAuth() {
        $statusCode = 203;
        $rawData = array('info' => 'You are not authorized to get this data!');
        $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
        $this->setHttpHeaders($requestContentType, $statusCode);

        $result["result"] = $rawData;

        if (strpos($requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($result);
            echo $response;
        }
    }
    
    public function getHttpStatusMessage($statusCode) {
        $httpStatus = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported');
        return ($httpStatus[$statusCode]) ? $httpStatus[$statusCode] : $status[500];
    }

    public function uploadImage() {
        $uploaddir = realpath('../uploads') . '/';
        $uploadfile = $uploaddir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
            return 'uploads/' . basename($_FILES['image']['name']);
        }
        return false;
    }
}

?>