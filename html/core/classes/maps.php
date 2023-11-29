<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| Google Mpas
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	class Maps {

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Static Map
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function image($center, $zoom, $width, $height, $markers = array(), $type = 'roadmap', $usecache = false){
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Google Static Map URLs are restricted to 2048 characters in size.
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$googlemax			= 2047;
			$url  				= 'https://maps.googleapis.com/maps/api/staticmap?';
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Static Map
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$par				= array();
			$par['center'] 		= ($center == '') ? '20.229669,-24.30' : $center;
			$par['zoom'] 		= $zoom;
			$par['size']		= $width . 'x' . $height;
			$par['maptype']		= $type;
			$par['sensor']		= 'false';
			$par['key']			= Core::common('APPID_GMAPS');
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Markers
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$remaining			= $googlemax - strlen(http_build_query($par)) - strlen($url);
			$markhtml 			= '';
			if (count($markers) > 0) foreach($markers as $marker) {
				if ($marker['latitude'] != '' && $marker['longitude'] != '') {
					$mark			= array();
					if (isset($marker['color'])) $mark[] = 'color:' . $marker['color'];
					if (isset($marker['label'])) $mark[] = 'label:' . $marker['label'];
					$mark[] 		= $marker['latitude'].','.$marker['longitude'];
					$qsm			= '&markers='.implode('%7C',$mark);
					if ($remaining - strlen($qsm) > 0) {
						$markhtml  .= $qsm;
						$remaining  = $remaining - strlen($qsm);
					}
				}
			}
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Static Map
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			return $url . http_build_query($par) . $markhtml;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Function Latitude Longitude
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function latlon($address, $city, $state, $zip, $country) {
			$location = $address . $city . $state . $zip;
			$url = "https://maps.google.com/maps/api/geocode/json?address=".urlencode($location)."&sensor=false&region=" . urlencode($country);
			$response = Internet::curl(get,$url);
			$json = json_decode($response);
			if (!$json) return false;
			if (!isset($json->results[0])) return false;
			return(array('latitude' => $json->results[0]->geometry->location->lat, 'longitude' => $json->results[0]->geometry->location->lng));
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Function Place :: Returns URL of Place
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function place($address = '', $city = '', $state = '', $zip = '', $country = '', $latitude = '', $longitude = '') {
			if ($latitude != '' && $longitude != '' && $latitude != $longitude && $latitude != 0 && $longitude != 0) return ('https://maps.google.com/?ll='.$latitude.','.$longitude);
			$csz  = '';
			$csz .= $address;
			$csz .= (($address != '') && ($city != '' || $state != '' || $zip != '')) ? ' ' : '';
			$csz .= ($city != '') ? $city : '';
			$csz .= ($city != '' && ($state != '' || $zip != '')) ? ', ' : '';
			$csz .= $state;
			$csz .= ($state != '' && $zip != '') ? ' ' : '';
			$csz .= $zip;
			$csz .= ($country != '') ? ' ' . GeoData::country($country, 'short') : '';
			return('https://maps.google.com/?q=' . urlencode($csz));
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Get Directions Link
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function directions($address, $city, $state, $zip, $country) {
			$location = str_replace('  ', ' ', $address . ' ' . $city . ' '. $state .' '. $zip . ' ' . GeoData::country($country, 'short'));
			return('https://maps.google.com?saddr=Current+Location&daddr='.urlencode($location));
		}


		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| EOC
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	}
