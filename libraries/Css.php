<?php defined('SYSPATH') OR die('No direct access allowed.');

class Css
{
	static protected $links = array();
    static protected $will_already_be_auto_rendered = false;
    static protected $do_render_in_head = true;

	static public function add($file, $category)
	{
        if (is_array($file)) {
            foreach($file as $css) {
                self::add($css);
            }
        } else {
            if (!isset(self::$links["wi3"]))
                self::$links["wi3"] = Array();
            if (!in_array($file, self::$links["wi3"]))
                self::$links["wi3"][$file] = $file;
            //make sure the script tags are inserted in the header just before sending the page to the browser
            self::set_auto_render();
        }
	}

	static public function render($print = FALSE)
	{
		$output = '';
		foreach (self::$links as $type => $filenames)
            foreach($filenames as $script)
                $output .= self::link($type, $script);

		if ($print == true)
			echo $output;

		return $output;
	}
    
    static public function set_auto_render() {
        //add a hook to the system.display Event. This event is called just before flushing content to the browser
        //only add the hook if we didn't set the hook already sometime earlier
        if (self::$will_already_be_auto_rendered == false) {
            //add before the page is cached, so that css files are cached as well
            Event::add_before('system.display', array('Pagefiller_default','cache_cache_output'), array('Css','render_in_head'));
            self::$will_already_be_auto_rendered = true;
        }
    }
    
    static public function render_in_head() {
        //only do this if set so
        if (self::$do_render_in_head == true) {
            //insert the script tags just before the </head> tag
            //The to be flushed data is found in Event::$data
            //preferably, the CSS gets before the <script> tags, so it gets loaded first
            $headpos = strpos(Event::$data, "<head>");
            if ($headpos > 0) {
                $scriptpos = strpos(Event::$data, "<script ", $headpos);
                if ($scriptpos > 0) {
                    //$temp = Event::$data;
                    //Event::$data = substr($temp, 0, $scriptpos) . self::render() . substr($temp, $scriptpos);
                    Event::$data = str_replace("</head>", self::render() . " </head>", Event::$data);
                    return Event::$data;
                }
            }
            Event::$data = str_replace("</head>", self::render() . " </head>", Event::$data);
        }
    }
    
    /**
	 * Creates a link tag.
	 *
	 * @param   string|array  filename
	 * @param   string|array  relationship
	 * @param   string|array  mimetype
	 * @param   string        specifies suffix of the file
	 * @param   string|array  specifies on what device the document will be displayed
	 * @param   boolean       include the index_page in the link
	 * @return  string
	 */
	public static function link($type, $href, $index=FALSE)
	{
        $attr = array
        (
            'type' => "text/css",
            'media' => 'all',
            'rel' => "stylesheet",
            'href' => $href,
        );
        
        return '<link '.html::attributes($attr).' />';
	}
    
}