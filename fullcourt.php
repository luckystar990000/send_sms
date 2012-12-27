<?php
require_once 'HTTP/Request2.php';


class FullcourtError extends Exception { }


function validate_signature($uri, $post_params=array(), $signature, $auth_token) {
    ksort($post_params);
    foreach($post_params as $key => $value) {
        $uri .= "$key$value";
    }
    $generated_signature = base64_encode(hash_hmac("sha1",$uri, $auth_token, true));
    return $generated_signature == $signature;
}


class RestAPI {
    private $api;

    private $auth_id;

    private $auth_token;

    function __construct($auth_id, $auth_token, $url="https://api.fullcourt.co", $version="v0.1") {
        if ((!isset($auth_id)) || (!$auth_token)) {
            throw new FullcourtError("no auth_id");
        }
        if ((!isset($auth_token)) || (!$auth_token)) {
            throw new FullcourtError("no auth_token");
        }
        $this->version = $version;
        $this->api = $url."/".$this->version."/Accounts/".$auth_id;
        $this->auth_id = $auth_id;
        $this->auth_token = $auth_token;
    }

    private function request($method, $path, $params=array()) {
        $url = $this->api.rtrim($path, '/');
        if (!strcmp($method, "POST")) {
            $req = new HTTP_Request2($url, HTTP_Request2::METHOD_POST);
            $req->setHeader('Content-type: application/x-www-form-urlencoded');
            if ($params) {
                $req->setBody(http_build_query($params));
            }
        } else if (!strcmp($method, "GET")) {
            $req = new HTTP_Request2($url, HTTP_Request2::METHOD_GET);
            $url = $req->getUrl();
            $url->setQueryVariables($params);
        } else if (!strcmp($method, "DELETE")) {
            $req = new HTTP_Request2($url, HTTP_Request2::METHOD_DELETE);
            $url = $req->getUrl();
            $url->setQueryVariables($params);
        }
        $req->setAdapter('curl');
        $req->setConfig(array('timeout' => 30));
        $req->setAuth($this->auth_id, $this->auth_token, HTTP_Request2::AUTH_BASIC);
        $req->setHeader(array(
            'Connection' => 'close',
            'User-Agent' => 'PHPfullcourt',
        ));
        $r = $req->send();
        $status = $r->getStatus();
        $body = $r->getbody();
        $response = json_decode($body, true);
        return array("status" => $status, "response" => $response);
    }

    private function pop($params, $key) {
        $val = $params[$key];
        if (!$val) {
            throw new FullcourtError($key." parameter not found");
        }
        unset($params[$key]);
        return $val;
    }

    ## Calls ##
    public function make_call($params=array()) {
        return $this->request('POST', '/Call', $params);
    }

    ## Messages ##
    public function send_message($params=array()) {
        return $this->request('POST', '/Message', $params);
    }


}







?>