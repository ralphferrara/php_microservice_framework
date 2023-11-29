/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| Maps
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Create Object
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	var GoogleMaps = {};
	
	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Variables
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.DivName           = 'googleMaps';
	GoogleMaps.LoadCallBack     = '';
	GoogleMaps.Loaded           = false;
	GoogleMaps.Initialized      = false;
	GoogleMaps.Markers          = [];
	GoogleMaps.InfoBoxes        = [];
	GoogleMaps.PlaceMarker      = false;
	GoogleMaps.PlaceMarkerCall  = '';
	GoogleMaps.GotLocationCall  = '';
	GoogleMaps.InitialArea      = 'user';
	GoogleMaps.Options          = {
		zoom: 8,
		zoomControl: false,
		scaleControl: false,
		scrollwheel: false,
		disableDoubleClickZoom: true,
		zoomControl: true
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Set Center
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.CountryCode = function(short) {
		var c = []; c["US"]=226;c["AX"]=1;c["AF"]=2;c["AL"]=3;c["DZ"]=4;c["AS"]=5;c["AD"]=6;c["AO"]=7;c["AI"]=8;c["AQ"]=9;c["AG"]=10;c["AR"]=11;c["AM"]=12;c["AW"]=13;c["AU"]=14;c["AT"]=15;c["AZ"]=16;c["BS"]=17;c["BH"]=18;c["BD"]=19;c["BB"]=20;c["BY"]=21;c["BE"]=22;c["BZ"]=23;c["BJ"]=24;c["BM"]=25;c["BT"]=26;c["BO"]=27;c["BA"]=28;c["BW"]=29;c["BV"]=30;c["BR"]=31;c["IO"]=32;c["BN"]=33;c["BG"]=34;c["BF"]=35;c["BI"]=36;c["KH"]=37;c["CM"]=38;c["CA"]=39;c["CV"]=40;c["KY"]=41;c["CF"]=42;c["TD"]=43;c["CL"]=44;c["CN"]=45;c["CX"]=46;c["CC"]=47;c["CO"]=48;c["KM"]=49;c["CD"]=50;c["CK"]=52;c["CR"]=53;c["HR"]=55;c["CU"]=56;c["CY"]=57;c["CZ"]=58;c["DK"]=59;c["DJ"]=60;c["DM"]=61;c["DO"]=62;c["EC"]=63;c["EG"]=64;c["SV"]=65;c["GQ"]=66;c["ER"]=67;c["EE"]=68;c["ET"]=69;c["FK"]=70;c["FO"]=71;c["FJ"]=72;c["FI"]=73;c["FR"]=74;c["GF"]=75;c["PF"]=76;c["TF"]=77;c["GA"]=78;c["GM"]=79;c["GE"]=80;c["DE"]=81;c["GH"]=82;c["GI"]=83;c["GR"]=84;c["GL"]=85;c["GD"]=86;c["GP"]=87;c["GU"]=88;c["GT"]=89;c["GN"]=90;c["GW"]=91;c["GY"]=92;c["HT"]=93;c["HM"]=94;c["HN"]=95;c["HK"]=96;c["HU"]=97;c["IS"]=98;c["IN"]=99;c["ID"]=100;c["IR"]=101;c["IQ"]=102;c["IE"]=103;c["IL"]=104;c["IT"]=105;c["JM"]=106;c["JP"]=107;c["JO"]=108;c["KZ"]=109;c["KE"]=110;c["KI"]=111;c["KR"]=113;c["KW"]=114;c["KG"]=115;c["LV"]=117;c["LB"]=118;c["LS"]=119;c["LR"]=120;c["LY"]=121;c["LI"]=122;c["LT"]=123;c["LU"]=124;c["MO"]=125;c["MK"]=126;c["MG"]=127;c["MW"]=128;c["MY"]=129;c["MV"]=130;c["ML"]=131;c["MT"]=132;c["MH"]=133;c["MQ"]=134;c["MR"]=135;c["MU"]=136;c["YT"]=137;c["MX"]=138;c["FM"]=139;c["MD"]=140;c["MC"]=141;c["MN"]=142;c["MS"]=143;c["MA"]=144;c["MZ"]=145;c["MM"]=146;c["NA"]=147;c["NR"]=148;c["NP"]=149;c["NL"]=150;c["AN"]=151;c["NC"]=152;c["NZ"]=153;c["NI"]=154;c["NE"]=155;c["NG"]=156;c["NU"]=157;c["NF"]=158;c["MP"]=159;c["NO"]=160;c["OM"]=161;c["PK"]=162;c["PW"]=163;c["PS"]=164;c["PA"]=165;c["PG"]=166;c["PY"]=167;c["PE"]=168;c["PH"]=169;c["PN"]=170;c["PL"]=171;c["PT"]=172;c["PR"]=173;c["QA"]=174;c["RE"]=175;c["RO"]=176;c["RU"]=177;c["RW"]=178;c["SH"]=179;c["KN"]=180;c["LC"]=181;c["PM"]=182;c["VC"]=183;c["WS"]=184;c["SM"]=185;c["ST"]=186;c["SA"]=187;c["SN"]=188;c["RS"]=189;c["SC"]=190;c["SL"]=191;c["SG"]=192;c["SK"]=193;c["SI"]=194;c["SB"]=195;c["SO"]=196;c["ZA"]=197;c["GS"]=198;c["ES"]=199;c["LK"]=200;c["SD"]=201;c["SR"]=202;c["SJ"]=203;c["SZ"]=204;c["SE"]=205;c["CH"]=206;c["SY"]=207;c["TW"]=208;c["TJ"]=209;c["TZ"]=210;c["TH"]=211;c["TL"]=212;c["TG"]=213;c["TK"]=214;c["TO"]=215;c["TT"]=216;c["TN"]=217;c["TR"]=218;c["TM"]=219;c["TC"]=220;c["TV"]=221;c["UG"]=222;c["UA"]=223;c["AE"]=224;c["GB"]=225;c["UM"]=227;c["UY"]=228;c["UZ"]=229;c["VU"]=230;c["VE"]=232;c["VN"]=233;c["VG"]=234;c["VI"]=235;c["WF"]=236;c["EH"]=237;c["YE"]=238;c["ZM"]=239;c["ZW"]=240;
		if (typeof(c[short.toUpperCase()]) == 'undefined') return '';
		return c[short.toUpperCase()];
	};
	
	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Set Center
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.GetLocation = function() {
		if (navigator.geolocation) {navigator.geolocation.getCurrentPosition(function(position) {
			var latitude  = position.coords.latitude;
			var longitude = position.coords.longitude;
			var response = GoogleMaps.GeoCode(latitude, longitude, GoogleMaps.GotLocation);
		},GoogleMaps.NoLocation,{maximumAge: Infinity,enableHighAccuracy:true, timeout:15000}); } else {
			GoogleMaps.NoLocation();
		}
	};
	
	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Set Center
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.GotLocation = function(respData) {
		if (typeof(GoogleMaps.GotLocationCall) == 'function') GoogleMaps.GotLocationCall(respData);
	};
	
	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Set Center
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.GeoCode			= function(lat,lng, passFunction) {
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(lat, lng);
		var resp   = {};
		resp.city         = '';
		resp.state        = '';
		resp.country      = '';
		resp.countryCode  = '';
		resp.zip          = '';
		resp.latitude     = lat;
		resp.longitude    = lng;
		geocoder.geocode({'latLng': latlng}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				if (results[1]) {
					for (var i=0; i<results[0].address_components.length; i++) {
						for (var b=0;b<results[0].address_components[i].types.length;b++) {
							if (results[0].address_components[i].types[b] == "locality") {resp.city = results[0].address_components[i].long_name;break;}
							if (results[0].address_components[i].types[b] == "administrative_area_level_1") {resp.state = results[0].address_components[i].long_name;break;}
							if (results[0].address_components[i].types[b] == "postal_code") {resp.zip = results[0].address_components[i].long_name;break;}
							if (results[0].address_components[i].types[b] == "country") {
								resp.country        = results[0].address_components[i].short_name;
								resp.countryCode    = GoogleMaps.CountryCode(resp.country);
								break;
							}
						}
					}
					if (typeof(passFunction) == 'function') passFunction(resp);
				}
			}
		});
	};
	
	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Set Center
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.SetCenter		= function(myLat, myLong, myZoom) {
		if (GoogleMaps.Loaded == false) return false;
		if (myLat != '' && myLong != '') GoogleMaps.Map.setCenter(new google.maps.LatLng(myLat, myLong));
		GoogleMaps.Map.setZoom(parseInt(myZoom));
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Clear Markers
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.ClearMarkers = function () {
		for (var i = 0; i < GoogleMaps.Markers.length; i++) {
			GoogleMaps.Markers[i].setMap(null);
		}
	};
	
	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Clear Markers
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.CloseInfo = function () {
		for (var i = 0; i < GoogleMaps.InfoBoxes.length; i++) {
			GoogleMaps.InfoBoxes[i].close();
		}
	};
	
	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Mark Location
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.MarkLocation		= function(myMarker) {
		if (!Validation.isFloat(myMarker.latitude) || !Validation.isFloat(myMarker.longitude)) return false;
		var marker = new google.maps.Marker({
			map						: GoogleMaps.Map,
			draggable				: false,
			position				: new google.maps.LatLng(myMarker.latitude, myMarker.longitude),
			visible					: true
		});
		var infoContent  = '<div class="infoBox">';
		infoContent     +=  (typeof(myMarker.url) != 'undefined') ? '<a href="'+myMarker.url+'">'  : '';
		infoContent     +=  (typeof(myMarker.name) != 'undefined') ? myMarker.name : '';
		infoContent     +=  (typeof(myMarker.url) != 'undefined') ? '</a>'  : '';
		infoContent     +=  (typeof(myMarker.more) != 'undefined') ? '<br>' + myMarker.more : '';
		var infowindow = new google.maps.InfoWindow({ content: infoContent });
		google.maps.event.addListener(marker, "click", function (e) {
			GoogleMaps.CloseInfo();
			infowindow.open(GoogleMaps.Map, marker);
		});
		GoogleMaps.Markers.push(marker);
		GoogleMaps.InfoBoxes.push(infowindow);
	};
	
	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Load Markers
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.LoadMarkers		= function() {
		var bounds = new google.maps.LatLngBounds();
		if (typeof(mapMarkers) == 'object') {
			$.each(mapMarkers, function(index, value){
				GoogleMaps.MarkLocation(value);
			});
			/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Fit all Markers to Zoom
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
			if (GoogleMaps.InitialArea == 'fit') {
				for (var i=0; i < GoogleMaps.Markers.length; i++) bounds.extend(GoogleMaps.Markers[i].position);
				GoogleMaps.Map.fitBounds(bounds);
			}
			/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| User Area
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
			if (GoogleMaps.InitialArea == 'user') {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function(position) {
						var pos = {
							lat: position.coords.latitude,
							lng: position.coords.longitude
						};
						GoogleMaps.Map.setCenter(pos);
					}, function() {
						GoogleMaps.InitialArea = 'usa';
						GoogleMaps.USA();
						Growl.Open('Could not access your location. Probably because this site is not on SSL');
					});
				} else {
					GoogleMaps.InitialArea = 'usa';
					GoogleMaps.InitialArea = 'usa';
					GoogleMaps.USA();
				}
			}
			/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| USA
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
			if (GoogleMaps.InitialArea == 'usa')  GoogleMaps.USA();
		}
	};


	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Zoom to USA
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	GoogleMaps.USA = function() {
		console.log('Zooming to USA');
		GoogleMaps.Map.setCenter(new google.maps.LatLng(41.850033, -87.6500523));
		GoogleMaps.Map.setZoom(3);
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Create Object
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.Init = function () {
		GoogleMaps.Map = new google.maps.Map(document.getElementById(GoogleMaps.DivName), GoogleMaps.Options);
		GoogleMaps.LoadMarkers();
	};
	
	
	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Assign Actions to Comment TextAreas
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	GoogleMaps.Load = function(myAPIKey, myZoom, callBack) {
		if (GoogleMaps.Loaded) return;
		GoogleMaps.Loaded = true;
		GoogleMaps.Zoom = (typeof(myZoom) != 'undefined') ? myZoom : 8;
		if (GoogleMaps.Loaded && typeof(callBack) == 'function') return callBack();
		$.getScript('https://maps.googleapis.com/maps/api/js?key='+myAPIKey+'&callback=GoogleMaps.Init', function() {
			console.log('Fetched Google Maps API');
			if (typeof(callBack) == 'function') callBack();
		});
	};
	
	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Assign Actions to Comment TextAreas
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
	
	console.log('Loading Google Maps');

	
