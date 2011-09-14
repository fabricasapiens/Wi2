<?php

    class Wi3_routing {
        
        public $host;  //the host of the site, including http(s)://
        
        public $completeurl;     //complete URL, including host, path and query string
        public $url;     //almost complete URL, with host and path but without query string
        public $querystring;    //query string. $url + $querystring would give $complete_url
        
        public $controller;  //current controller
        public $action;  //current action that is executed
        
        public $segments;    //array of all segments, including controller, action, and args
        
        public $allargs;     //all arguments that are 'extra' on top of the /controller/action segments
        public $actionargs = array();  //arguments that belong to the called action (is: the usual arguments in the function that is called in the controller)
        public $args;   //arguments that are not for the called action but are extra args (/actionarg/extraarg1/extraarg2), to be used by ie the pagefiller 
        
        public function __construct() {
            $querystringpos = strpos(urldecode($_SERVER["REQUEST_URI"]), "?");
            $this->host = "http://" . $_SERVER["HTTP_HOST"];
            $this->url = "http://" . $_SERVER["HTTP_HOST"] . ($querystringpos > 0 ? substr(urldecode($_SERVER["REQUEST_URI"]), 0, $querystringpos) : urldecode($_SERVER["REQUEST_URI"]));
            $this->completeurl = "http://" . $_SERVER["HTTP_HOST"] . urldecode($_SERVER["REQUEST_URI"]);
            $this->querystring =  Router::$query_string;
            
            $this->controller = Router::$controller;
            $this->action = Router::$method;
            
            $this->segments = Router::$segments;
            
            $this->allargs = $this->args = Router::$arguments;
            //now check with reflection the amount of arguments that the action function expects
            if (method_exists($this->controller . "_Controller", $this->action)) {
                $reflect = new ReflectionClass($this->controller . "_Controller");
                $num = $reflect->getMethod($this->action)->getNumberOfParameters();
                for($i = 0; $i < $num; $i++) {
                    //set actionargs and 'normal' args
                    $this->actionargs[] = array_shift($this->args);
                }
            }
        }
        
    }

?>