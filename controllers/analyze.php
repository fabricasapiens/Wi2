<?php

    //-------------------------------------------------------------
    // This controller contains a few functions to benchmark/analyze Wi3
    //-------------------------------------------------------------
    
    //you need to be logged in to view any of this controller's pages,
    //so extend with Login_Controller
    class Analyze_Controller extends Login_Controller {
      
        public $template = "wi3/workplace";
        
        public function __construct() {
            parent::__construct();
            
            //now run the event that page and site are loaded
            //Wi3_pathof and Wi3_urlof hook into this to fetch the path of the current site and of the pagefiller
            Event::run("wi3.siteandpageloaded");
        }

        public function index() {
            return $this->controlpanel();
        }
        
        public function analyze() {
            $this->template = View::factory("templates/ajax");
            
            $amount = $_GET["amount"];
            $url = $_GET["url"];
            
            $time = time() + microtime();
            for($i=1; $i <= $amount; $i++) {
                $get = file_get_contents($url);
                echo strlen($get) . " ";
                //if (strlen($get) < 1000) { echo $get; }
            }
            
            $endtime = time() + microtime();
            echo "<h1>time: " . ($endtime - $time) . " seconden.</h1>";
            echo "<h2>gem.: " . (($endtime - $time) / $amount) . " secs/verzoek. </h2>";
            echo "<h2>gem.: " . ($amount / ($endtime - $time) ) . " verzoeken/sec. </h2>";
            
            //VOOR CACHING
            //0.068 / stuk op http://localhost/w/kohana/oud/users/zuiderzeehaven/
            //0.0719343650341 / stuk op http://localhost/w/kohana/oud/users/zuiderzeehaven/79 (13.9015615072 / sec)
            //0.0776992201805 / stuk op http://localhost/w/kohana/oud/users/zuiderzeehaven/79 (12.8701420385 / sec)
            //0.0709004998207 / stuk op http://localhost/w/kohana/oud/users/zuiderzeehaven/79 (14.1042729251 / sec)
            
            //http://vps2041.xlshosting.net/Aanbod
            //voor: 28.3 verzoeken/sec   (0.035 sec/verzoek)
            //na: 48.2 verzoeken/sec
            
        }
        
        public function analyze_multi() {
             
            $this->template = View::factory("templates/ajax");
            
            $amount = $_GET["amount"];
            $url = $_GET["url"];
            
            $time = time() + microtime();
            $urls = array();
            for($i=1; $i <= $amount; $i++) {
                $urls[] = $url . "?test=".$i;
            }
            
            $inhoud = $this->rolling_curl($urls);
            
            $endtime = time() + microtime();
            echo "<h1>time: " . ($endtime - $time) . " seconden.</h1>";
            echo "<h2>gem.: " . (($endtime - $time) / $amount) . " secs/verzoek. </h2>";
            echo "<h2>gem.: " . ($amount / ($endtime - $time) ) . " verzoeken/sec. </h2>";
            
            echo Kohana::debug($inhoud);

        }
        
        private function curl($urls) {
            // for storing cUrl handlers
            $chs = array();
            // for storing the reponses strings
            $contents = array();
            // loop through an array of URLs to initiate
            // one cUrl handler for each URL (request)
            foreach ($urls as $url) {
                $ch = curl_init($url);
                // tell cUrl option to return the response
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
                $chs[] = $ch;
            }
            // initiate a multi handler
            $mh = curl_multi_init();
            // add all the single handler to a multi handler
            foreach($chs as $key => $ch){
                curl_multi_add_handle($mh,$ch);
            }
           // execute the multi cUrl handler
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM  || $active);
            // retrieve the reponse from each single handler
            foreach($chs as $key => $ch){
                if(curl_errno($ch) == CURLE_OK){
                    $contents[] = curl_multi_getcontent($ch);
                } else{
                    echo "Err>>> ".curl_error($ch)."\n";
                }
            }
            curl_multi_close($mh);
            return $contents;
        }
        
        private function rolling_curl($urls, $custom_options = null) {
           
           $return = array();
           
            // make sure the rolling window isn't greater than the # of urls
            $rolling_window = 5;
            $rolling_window = (sizeof($urls) < $rolling_window) ? sizeof($urls) : $rolling_window;

            $master = curl_multi_init();
            $curl_arr = array();

            // add additional curl options here
            $std_options = array(CURLOPT_RETURNTRANSFER => true,
                                 CURLOPT_FOLLOWLOCATION => true,
                                 CURLOPT_MAXREDIRS => 5);
            $options = ($custom_options) ? ($std_options + $custom_options) : $std_options;

            // start the first batch of requests
            for ($i = 0; $i < $rolling_window; $i++) {
                $ch = curl_init();
                $options[CURLOPT_URL] = $urls[$i];
                curl_setopt_array($ch,$options);
                curl_multi_add_handle($master, $ch);
            }

            do {
                while(($execrun = curl_multi_exec($master, $running)) == CURLM_CALL_MULTI_PERFORM);
                if($execrun != CURLM_OK)
                    break;
                // a request was just completed -- find out which one
                while($done = curl_multi_info_read($master)) {

                    $info = curl_getinfo($done['handle']);
                    if ($info['http_code'] == 200)  {
                        $output = curl_multi_getcontent($done['handle']);

                        // request successful.  process output using the callback function.
                       $return[] = strlen($output);

                        // start a new request (it's important to do this before removing the old one)
                        if ($i+1 < sizeof($urls)) {
                            $ch = curl_init();
                            $options[CURLOPT_URL] = $urls[$i++];  // increment i
                            curl_setopt_array($ch,$options);
                            curl_multi_add_handle($master, $ch);
                        }

                        // remove the curl handle that just completed
                        curl_multi_remove_handle($master, $done['handle']);
                    } else {

                        // request failed.  add error handling.
                        echo "fout!";

                    }
                }
            } while ($running);
            curl_multi_close($master);
            return $return;
        }
        
        //this functions makes sure that any views are loaded in the Wi3::$template namespace
        //so the $this in these views refers to Wi3::$template
        public function _kohana_load_view($kohana_view_filename, $kohana_input_data)
        {
            //we want the pagetemplate (and other templates) to be available through the Wi3_template namespace
            return Wi3::$template->_kohana_load_view($kohana_view_filename, $kohana_input_data);
        }
        
    }

?>
