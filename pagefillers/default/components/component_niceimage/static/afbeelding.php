<?php

	ini_set("memory_limit","32M");

	$rij = explode("/", $_SERVER["PHP_SELF"]);
	$rij = array_reverse($rij);
	$naamgevonden = false;
	$gevondennaam  = "";
	$gevondenx = "";
	$gevondeny = "";
    $restURL = "";
	foreach($rij as $naam) {
		if ($naamgevonden == false AND preg_match("@[a-zA-Z0-9_\-\.]\.(?:png|jpg|jpeg|bmp|gif)$@i",$naam) ) {
			$naamgevonden = true;
			$gevondennaam = $naam;
		}
		if ($naamgevonden == true AND is_numeric($naam)) {
			//als er al een x gevonden is, en er blijkt nóg een getal te zijn, dat is het gevonden getal dus de x en de oude x de y
			//het formaat is namelijk als x/y/naam.ext
			if (!empty($gevondenx)) {
				$gevondeny = $gevondenx;
				$gevondenx = $naam;
			} else {
				//een getal gevonden direct na de naam
				$gevondenx = $naam;
			}
		} else if ($naamgevonden == true AND !empty($gevondenx)) {
            if ($naam == "afbeelding.php") {
                break; //dan is alle te vinden informatie dus gevonden
            }
            //restURL vóór de gevonden getallen
            $restURL = $naam . "/" . $restURL;
        }
	}
	
	if ( !empty($gevondennaam) AND !empty($gevondenx) ) {
		//kijken of opgevraagde afbeelding bestaat in de hoofdmap
		if (file_exists($restURL . $gevondennaam)) {
			//als alleen de x opgegegen is, moeten we natuurlijk gevondeny niet meenemen in de locatie
				if (empty($gevondeny)) {
					//er is geen max y gevonden
					resize_and_upload_image($restURL . $gevondennaam, Array("maxx"=>$gevondenx, "maxy"=>$gevondeny),$restURL . $gevondenx . "/" . $gevondennaam);
					header('Content-type: image');
					readfile($restURL . $gevondenx . "/" . $gevondennaam);
				} else {
					//er is een max y gevonden
					resize_and_upload_image( $restURL . $gevondennaam, Array("maxx"=>$gevondenx, "maxy"=>$gevondeny),$restURL . $gevondenx . "/" . $gevondeny . "/" . $gevondennaam);
					header('Content-type: image');
					readfile($restURL . $gevondenx . "/" . $gevondeny . "/" . $gevondennaam);
				}
		} else {
			echo "plaatje bestaat niet";
		}
	}
	
	function resize_and_upload_image( $locatieoud, $size = Array("maxx", "maxy", "perc"), $locatie)
	{
		//dmv extensie bepalen...
		//$type = strtolower(substr($locatieoud, strrpos($locatieoud,".")+1 ));
		//if ($type == "jpg") { $type = "jpeg"; }
		
		//dmv MIME bepalen
		$info = getimagesize( $locatieoud );
		$type =  str_replace( 'image/', '', $info["mime"] );
		$createFunc = 'imagecreatefrom' . $type;
	   
		$im = $createFunc( $locatieoud );
	   
		$w = $info[ 0 ];
		$h = $info[ 1 ];
		
		// create thumbnail
		if (!empty($size["perc"])) {
			$percentage = $size["perc"];
			$tw = round($percentage / 100 * $w);
			$th = round($percentage / 100 * $h);
		} else {
			$tw = $size["maxx"];
			if (empty($size["maxy"])) {
				$th = 9999999999; //er is geen limiet op de y
			} else {
				$th = $size["maxy"];
			}
		}
		//$imT = imagecreatetruecolor( $tw, $th );
		
		//als de doelresoluties op x en y groter zijn dan de resoluties van het bronbestand, dan gewoon het bronbestand tonen
		if ($tw >= $w AND $th >= $h) {
			
		}
	   
		if ( $tw/$th < $th/$tw )
		{ // wider
			$tmph = $h*($tw/$w);
			$imT = imagecreatetruecolor( $tw, $tmph );
			imagecopyresampled( $imT, $im, 0, 0, 0, 0, $tw, $tmph, $w, $h ); // resize to width
			//imagecopyresampled( $imT, $temp, 0, 0, 0, $tmph/2-$th/2, $tw, $th, $tw, $th ); // crop
		}else
		{ // taller
			$tmpw = $w*($th/$h );
			$imT = imagecreatetruecolor( $tmpw, $th );
			imagecopyresampled( $imT, $im, 0, 0, 0, 0, $tmpw, $th, $w, $h ); // resize to height
		   // imagecopyresampled( $imT, $temp, 0, 0, $tmpw/2-$tw/2, 0, $tw, $th, $tw, $th ); // crop
		}
	   
		// save the image
		$saveFunc = 'image' . $type;
        if ($type == "png") { $quality = 9; } else { $quality = 100; }
        rmkdir(dirname($locatie)); //create dir
		$saveFunc( $imT, $locatie, $quality );
		/*) {
			return true;
		} else {
			return false;
		}*/
		
		return true;
	}
	
	/**
	 * Makes directory and returns BOOL(TRUE) if exists OR made.
	 *
	 * @param  $path Path name
	 * @return bool
	 */
	function rmkdir($path, $mode = 0755) {
		$path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
		$e = explode("/", ltrim($path, "/"));
		if(substr($path, 0, 1) == "/") {
			$e[0] = "/".$e[0];
		}
		$c = count($e);
		$cp = $e[0];
		for($i = 1; $i < $c; $i++) {
			if(!is_dir($cp) && !@mkdir($cp, $mode)) {
				return false;
			}
			$cp .= "/".$e[$i];
		}
		return @mkdir($path, $mode);
	}

?>