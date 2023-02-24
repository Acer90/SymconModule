<?

class symconpvForecast extends IPSModule
{

	private $fc;
	private $force;
	public function Create()
	{
		//Never delete this line!
		parent::Create();
        $this->RegisterPropertyString('dwd_station', '');
        $this->RegisterPropertyBoolean('kwh', false);
        $this->RegisterPropertyBoolean('toArchiv', false);
        $this->RegisterPropertyInteger('forecastVariables', 3);
        $this->RegisterPropertyString('solarData', '[]');
        $this->RegisterPropertyString('location', '');

		/*$this->RegisterPropertyInteger('kwp', 1000);
		$this->RegisterPropertyInteger('azimuth', 0);
		$this->RegisterPropertyInteger('tilt', 30);
		$this->RegisterPropertyInteger('type', 0);
		$this->RegisterPropertyInteger('efficiency', 95);
		$this->RegisterPropertyInteger('cloudeffect', 65);
		$this->RegisterPropertyFloat('tempkoeff', 0.65);
		$this->RegisterPropertyInteger('horizon', 0);


		$this->RegisterPropertyBoolean('obj', false);
		$this->RegisterPropertyInteger('obj_direction', 0);
		$this->RegisterPropertyInteger('obj_distance', 0);
		$this->RegisterPropertyInteger('obj_height', 10);
		$this->RegisterPropertyInteger('obj_size', 50);
		$this->RegisterPropertyInteger('obj_effect', 50);*/


		$this->RegisterTimer('Update', 1000*60*60, 'pvFC_Update($_IPS[\'TARGET\']);');

	}

	public function ApplyChanges() {
        //Never delete this line!
		parent::ApplyChanges();
        $this->SetStatus(102);
		$name = IPS_GetName($_IPS["TARGET"]);
		if(strpos($name,"nnamed") === false){
			if(empty($this->ReadPropertyString("dwd_station")) ||
				empty($this->ReadPropertyString("location"))){
				echo "Achtung: Location und DWD Station eintragen!";
				return;
			}
		}
	    
		//$this->Update(true);
        $this->UpdateForce();

    }

	private function initfc(){
		$station =  $this->ReadPropertyString("dwd_station");
		if(!empty($this->ReadPropertyString("location"))){
			$latlon  = json_decode($this->ReadPropertyString("location"),true);
		} else {
			$latlon["longitude"] = 0;
			$latlon["latitude"] = 0;
		}

        $pvitem = array();
        $peekAll = 0;
        if(!empty($this->ReadPropertyString("solarData")) && $this->ReadPropertyString("solarData") != "[]"){
            $solardata  = json_decode($this->ReadPropertyString("solarData"),true);

            foreach ($solardata as $solaritem){
                $pvtype = ( $solaritem['kwp'] == 1 )? "D" : "";
                $peekAll += $solaritem['kwp'];

                $pvitem[] = [   "activ"       =>  $solaritem['activ'],
                                "kwp"         =>  $solaritem['kwp'],
                                "azimuth"     =>  $solaritem['azimuth'],      // eigentlich sind es 30 aber soll ist vergleich zeigt leichte verschiebung vom abend - also mehr nach rechts drehen
                                "tilt"        =>  $solaritem['tilt'],
                                "pvtype"      => $pvtype,                                         // D= Dachform geständert, Ausrichtung der offenen seite = azimuth
                                "efficiency"  =>  $solaritem['efficiency'],
                                "cloudeffect" =>  $solaritem['cloudeffect'],  // Effekt für Leistungsreduktion bei Bewölkung in Prozent
                                "tempkoeff"   => $solaritem['tempkoeff'],       // Temperaturkoeffizient lt. Datenblatt
                                "horizon"     => $solaritem['horizon'],       // Horizont für Einfallswinkel Sonne

                                // Beschattungsobjekt
								"obj" => 		   $solaritem['obj'], 			  //beschattungsobjeckt an
                                "obj_direction" => $solaritem['obj_direction'],   // Himmelsrichtung des Beschattungsobjektes Grad von Norden =0
                                "obj_distance"  => $solaritem['obj_distance'],      // Abstand des Objektes in Meter
                                "obj_height"    => $solaritem['obj_height'],      // Höhe des Objektes in Meter
                                "obj_size"      => $solaritem['obj_size'],    // Prozentualer Abdeckungsanteil der PV Anlage durch Objekt (Mittelwert)
                                "obj_effect"    => $solaritem['obj_effect']/100    // Azuswirkung der Beschattung auf Ertrag (kwp mit gemessenem Ertrag bei Beschattung (grob))
                            ];
            }
        }

		$PV      =  [   "lon"         => $latlon["longitude"],
                        "lat"         => $latlon["latitude"],
                        "kwh"         => $this->ReadPropertyBoolean('kwh'),         // ausgabe in KWH statt wh
                        "kwp"         => $peekAll,
                        "toArchiv"    => $this->ReadPropertyBoolean("toArchiv"),
                        "forecast"    => $this->ReadPropertyInteger("forecastVariables"),
                        "data"        => $pvitem
                    ];
		$this->fc = false;
		if(!empty($station))$this->fc = new PVForecastcls($station, $PV, $this->InstanceID);
	}

	public function UpdateForce(){
		$this->force = true;
		$this->Update();
		$this->force = false;
	}

	public function Update(){
		$this->initfc();
        //print_r($this);
		if($this->fc){
			if($this->force)$this->fc->loadForecast(true);
			$this->fc->CreateFCVariables($this->ReadPropertyInteger("forecastVariables"));
			$this->fc->forecastChart();
            $this->fc->UpdateArchiv();
		}else{
			return false;
		}

	}

    public function updateArchivData(){
        $this->initfc();
        $this->fc->UpdateArchiv();
    }

	public function getDayForecast($ts){
		$this->initfc();
		if($this->fc){
			return $this->fc->getDayForecast($ts);
		}else{
			return false;
		}
	}

	public function getHourForecast($ts){
		$this->initfc();
		if($this->fc){
			return $this->fc->getHourForecast($ts);
		}else{
			return false;
		}			
	}
	public function getForecast(){
		$this->initfc();
		if($this->fc){
			return $this->fc->getForecast();
		}else{
			return false;
		}			
	}

	
}

class PVForecastcls{
	private $fc;
	private $PV;
	private $dwd_fca;
	private $dwd_station;
	private $instance;
	const TYPE_D_CORRECTION = 1.15; // Faktor for increasing / decreasing pv estimate for Type D Positioning

	const CACHE_AGE = 3600*3;       // Maximales ALter der Berechung und Wettervorhersage

	function __construct($station, $PV, $instance){
		$this->instance = $instance;
		// Wetterdaten laden (DWD)
		$this->PV = $PV;
		$this->dwd_station = $station;
		$this->loadForecast();
		#$this->checkEvent(); // Event über Moduleklasse
	}

	#### Tageswert Zurckgeben #####################################################
	public function getDayForecast($ts){
	
		foreach($this->fc["daily"] as $fc){
			if(date("Ymd", $fc["ts"]) == date("Ymd", $ts)){
				return $fc["pv_estimate"];
			}
		}
		return false;
	}

	#### Stundenwert Zurckgeben #####################################################
	public function getHourForecast($ts){
	
		foreach($this->fc["hourly"] as $fc){
			if(date("YmdH", $fc["ts"]) == date("YmdH", $ts)){
				return $fc["pv_estimate"];
			}
		}
		return false;
	}

	#### Rueckgabe der Forecast Daten ################################################
	public function getForecast(){
		return $this->fc;
	}

	#### CreateFCVariables ##########################################################
	public function CreateFCVariables($days){

		// Variablenprofile anlegen, wenn nicht vorhanden
		if(!IPS_VariableProfileExists("pvFC_kwh")){
			IPS_CreateVariableProfile("pvFC_kwh", 2);
			IPS_SetVariableProfileDigits("pvFC_kwh", 1); 
			IPS_SetVariableProfileText("pvFC_kwh", "", " kWh");

		  }
		  if(!IPS_VariableProfileExists("pvFC_wh")){
			IPS_CreateVariableProfile("pvFC_wh", 2);
			IPS_SetVariableProfileText("pvFC_wh", "", " Wh");
		  }      


		$cnt = 0;
		$varprof = ($this->PV["kwh"])? "pvFC_kwh" : "pvFC_wh";
		foreach($this->fc["daily"] as $fc){
			$varName = ($cnt == 0)? 'Vorhersage Heute' : "Vorhersage Heute + $cnt";
			$id = $this->CreateVariableByName($this->instance,$varName,2, $varprof);
			IPS_SetVariableCustomProfile($id, $varprof);
			
			if($cnt == 0 && date("G")<10 || $cnt > 0) setValue($id, $fc["pv_estimate"]);

            if($cnt == 0 && $this->PV["toArchiv"]){
                $archivID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
                AC_SetLoggingStatus($archivID, $id, true);
            }

			if($cnt >= $days)break;
			$cnt++;
		}

	}

	#### Ausgabe als Chart #######################################################
	function forecastChart(){
		$id = $this->CreateVariableByName($this->instance,"PV Forecast Chart",3,"~HTMLBox");
		$pv_max = ($this->PV["kwh"])? $this->PV["kwp"]/1000 : $this->PV["kwp"];
        $html = "<html>
                  <head>";
 		$html .= "<style>
					.pv{
						background-color: yellow;
						margin-left: 1px;
						width: 30px;
						display:inline-block;
						font-size:9px;
						color: black;
						text-align:center;
						vertical-align: bottom;
					}
					.pv_txt{
						background-color: transparent;
						padding-left: 1px;
						width: 30px;
						display:inline-block;
						font-size:9px;
						color: white;
						text-align:center;
						vertical-align: bottom;
						border-top: solid 1px white;
					}
					.pv_day{
						position: absolute;
						top: 10px;
						font-size: 14px;
						font-weight: bold;
						color: white;
					}

				</style>";
        $html .= "</head><body style='display: inline-block; width:100%; height:200px;'>";
        $html .= "<div style='width:100%; height:200px;overflow:hidden;'>";
			$cnt=0;
			$html .= "<div class='pv' style='height: 200px; width:1px;background-color:transparent;'></div>";
			foreach ($this->fc["hourly"] as $fc){
				
				if($fc["hour"] > 6 && $fc["hour"] < 22 ){
					$cnt++;
					$height = round($fc["pv_estimate"]/$pv_max*100*2,0);
					$html .= "<div class='pv' style='height: $height"."px'>".$fc["pv_estimate"]."</div>";
				}					
				if($cnt >96)break;
			}
		$html .="</div>";					
		$html .= "<div style='width:100%; height:20px;overflow:hidden;'>";
			$html .= "<div class='pv' style='height: 20px; width:1px;background-color:transparent;'></div>";
			$cnt = 0;
			foreach ($this->fc["hourly"] as $fc){
				if($fc["hour"] > 6 && $fc["hour"] < 22 ){
					$cnt++;
					$height = round($fc["pv_estimate"]/$pv_max*100*2,0);
					$html .= "<div class='pv_txt' style='height:20px'>".($fc["hour"]+1)."h</div>";
					if($fc["hour"]==12){
						$dayfc = $this->getDayForecast($fc["ts"]);
						$dayfc = ($this->PV["kwh"])? $dayfc : $dayfc/1000;
						$dayfc = round($dayfc,1);
						$html.="<div class='pv_day' style='left:". (31 * $cnt )."px'>$dayfc kWh</div>";
					}
				}
				if($cnt >96)break;
			}
		$html .="</div>";
        $html .= "</body></html>";
		setvalue($id,$html);

	}

    public function UpdateArchiv(){
        $archivID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        $varName = 'Vorhersage Heute';
        $id = $this->CreateVariableByName($this->instance,$varName,2);

        $now = new DateTime('NOW');
        //echo "JETZT=> ". $now->format('Y-m-d H:i:s') . "\n\r";

        $start = new DateTime($this->fc["hourly"][0]["tiso"]);
        //$start->setTimestamp($this->fc["hourly"][0]["ts"]);
        //$start->setTimezone(new DateTimeZone('Europe/Berlin'));

        $start->modify( '-'.$this->PV["forecast"].' day' );
        //echo "START=> ". $start->format('Y-m-d H:i:s')."\n\r";
        AC_DeleteVariableData($archivID, $id, $start->getTimestamp(), 0);
        foreach ($this->fc["hourly"] as $fitem){
            //print_r($fitem);
            $d = new DateTime($fitem["tiso"]); //, new DateTimeZone('UTC')
            //$d->setTimestamp($fitem["ts"]);
            //$d->setTimezone(new DateTimeZone('Europe/Berlin'));
            $d->modify( '-'.$this->PV["forecast"].' day' );

            if($d->getTimestamp() > $now->getTimestamp())break;

            //echo $d->format('Y-m-d H:i:s') . " => " . $fitem["pv_estimate"] . "\n\r";
            AC_AddLoggedValues($archivID, $id, [
                [
                    'TimeStamp' => $d->getTimestamp(),
                    'Value' => $fitem["pv_estimate"]
                ]]);
        }

        AC_ReAggregateVariable ($archivID, $id);
    }

	### LoadForecast from Variable or do calculation ###############################
	public function LoadForecast($force=false){
		$id = $this->CreateVariableByName($this->instance, "PVForecast",3);
		$vdata = IPS_GetVariable($id);
		if($vdata["VariableChanged"] + PVForecastcls::CACHE_AGE < time() || $force){            
			$this->getWeatherForecast($this->dwd_station);
			$this->pvForecast($this->PV);
			$this->SaveToCache();
		}else{
			$this->fc = json_decode(getValue($id),true);
		}
	}

	#### Create Cache Variable #####################################################
	function SaveToCache(){
		$id = $this->CreateVariableByName($this->instance, "PVForecast",3);
		setValue($id, json_encode($this->fc));
	}


	#### Wetterdaten laden  ######################################################
	private function getWeatherForecast($station){
		$this->dwd_loadXML($station);
	}

	#### PV Berechnen ############################################################
	private function pvForecast($PV){
		$lat      = $PV["lat"];
		$lon      = $PV["lon"];
		$this->fc = $this->dwd_fca;

        //durchlauf für jede ausrichtung
        foreach ($PV["data"] as $solarItem) {

            foreach ($this->fc["hourly"] as $k => $fc) {
                //estemite setzen auf 0 wenn noch nicht angelegt

                if(!isset($this->fc["hourly"][$k]["pv_estimate"])){
                    $this->fc["hourly"][$k]["pv_estimate"] = 0;
                }

                if($solarItem["activ"] == false) continue;

                $ts = $fc["ts"];
                $clouds = $fc["clouds"];
                $temp = $fc["temp"];

                // SOnnenposition ermitteln
                $ret = $this->calcSun($ts, $lon, $lat);
                $sun_azimuth = $ret["azimuth"];
                $sun_elevation = $ret["elevation"];

                // Leistungsfaktor festlegen
                $lf = 100;

                // Beschattung berechnen
				//print_r($solarItem);
                if (isset($solarItem["obj_direction"]) && $solarItem["obj"]) {
                    // 2x berechnen und mittelwert bilden für volle stunde und 30min
                    for ($x = 0; $x <= 1; $x++) {
                        $time = $ts + $x * 1800;

                        $sunpos = $this->calcSun($time, $lon, $lat);
                        $alpha = deg2rad($sunpos["elevation"]);
                        $azimuth = $sunpos["azimuth"];
                        $shadeAngle = $azimuth - $solarItem["obj_direction"] - 90;
                        $shadeLength = round($solarItem["obj_height"] / (tan($alpha)), 2);

						//print_r($sunpos);
						//echo date("ymd-H:i", $time) . "=>". $shadeAngle."###". $shadeLength."\r\n";

                        if ($shadeLength < $solarItem["obj_distance"] || $shadeAngle > 0) {
                            $lf_minusShadeA[$x] = 100;
                        } else {
                            $lf_minusShadeA[$x] = $lf * ($solarItem["obj_size"] / 100) * $solarItem["obj_effect"] + $lf * (1 - ($solarItem["obj_size"] / 100));
                        }
                    }
                    $lf_minusShade = ($lf_minusShadeA[0] + $lf_minusShadeA[1]) / 2;
                    $lf = $lf_minusShade;
                } // Objekt

                /* ------------ Kalkulation des Ertrages
                Daten von https://echtsolar.de/wp-content/uploads/2021/02/Ausrichtungstabelle-Photovoltaik-in-Deutschland-768x705.png
                Formel über Newton Polynom: https://de.planetcalc.com/9023/
                */
                if ($solarItem["pvtype"] == "D") {
                    // Dachständerung /\/\/\
                    /* 1/2 der Leistung muss um 90 grad nach ost und 90 grad nach west gedreht werden*/
                    $lfA = $lf_minusShade;
                    $lfB = $lf_minusShade;

                    $az_abwA = round(abs($sun_azimuth - 180 + 90 - $solarItem["azimuth"]), 1);
                    $az_abwB = round(abs($sun_azimuth - 180 - 90 - $solarItem["azimuth"]), 1);
                    if ($az_abwA > 180) {
                        $lf_minusAA = 90;
                    } else {
                        $lf_minusAA = sin((1 / 58) * $az_abwA + 155.5) * 26 + 26;
                    }
                    if ($az_abwB > 180) {
                        $lf_minusBA = 90;
                    } else {
                        $lf_minusBA = sin((1 / 58) * $az_abwB + 155.5) * 26 + 26; // Mit Daten aus Tabelle und näherung über probieren der Sinus-Funktion
                    }

                    $lfA = $lfA - $lf_minusAA * 0.90; // fällt schwächer ins gewicht
                    $lfB = $lfB - $lf_minusBA * 0.90; // fällt schwächer ins gewicht

                    // Neigungswikel und Sonne
                    $elev_abw = 90 - $sun_elevation - $solarItem["tilt"];
                    $x = $elev_abw;
                    $lf_minusE = (1 / 100) * pow($x, 2); // Neue Formel anhand Tabellen und Werten

                    if ($sun_elevation < @$solarItem["horizon"] + 5) $lf_minusE = 50; // Sonnenuntergang
                    if ($sun_elevation < @$solarItem["horizon"]) $lf_minusE = 100; // Sonnenuntergang
                    $lf_minusE = round($lf_minusE, 1);
                    $lfA = $lfA - $lf_minusE; // fällt schwächer ins gewicht;
                    $lfB = $lfB - $lf_minusE; // fällt schwächer ins gewicht;

                    // Bewölkung hängt von der Wolkendicke und von dem Einstrahlungswinkel ab.
                    $lf_cloud_dec = (100 - (pow($clouds, 2.4) / 600) * $solarItem["cloudeffect"] / 100) / 100;

                    $lfA = $lfA * $lf_cloud_dec;
                    $lfB = $lfB * $lf_cloud_dec;
                    $lfA = ($lfA < 0) ? 0 : $lfA;
                    $lfB = ($lfB < 0) ? 0 : $lfB;

                    $pv_estimate = ($solarItem["kwp"] / 2) * ($lfA / 100) * $solarItem["efficiency"] / 100;
                    $pv_estimate += ($solarItem["kwp"] / 2) * ($lfB / 100) * $solarItem["efficiency"] / 100;

                    $pv_estimate = $pv_estimate * PVForecastcls::TYPE_D_CORRECTION;

                } else { // NOrmale Anlage keine /\ Ständerung

                    $az_abw = round(abs($sun_azimuth - 180 - $solarItem["azimuth"]), 1);
                    if ($az_abw > 180) {
                        $lf_minusA = 100;
                    } else {
                        $lf_minusA = (-0.00000000147) * pow($az_abw, 5) + (0.00000057668) * pow($az_abw, 4) + (-0.00008107497) * pow($az_abw, 3) + (0.00559429998 * pow($az_abw, 2)) + (0.05160253657 * $az_abw);
                    }
                    $lf = $lf - $lf_minusA;

                    // Neigungswikel und Sonne
                    $elev_abw = 90 - $sun_elevation - $solarItem["tilt"];
                    $x = $elev_abw;
                    $lf_minusE = (1 / 100) * pow($x, 2); // Neue Formel anhand Tabellen und Werten

                    if ($sun_elevation < @$solarItem["horizon"] + 5) $lf_minusE = 50; // Sonnenuntergang
                    if ($sun_elevation < @$solarItem["horizon"]) $lf_minusE = 100; // Sonnenuntergang
                    $lf_minusE = round($lf_minusE, 1);
                    $lf = $lf - $lf_minusE;


                    // Bewölkung hängt von der Wolkendicke und von dem Einstrahlungswinkel ab.
                    $lf_cloud_dec = (100 - (pow($clouds, 2.4) / 600) * $solarItem["cloudeffect"] / 100) / 100;

                    $lf = $lf * $lf_cloud_dec;
                    $lf = ($lf < 0) ? 0 : $lf;
                    $pv_estimate = $solarItem["kwp"] * ($lf / 100) * $solarItem["efficiency"] / 100;
                } // Staenderung

                // Temperaturverlust
                if ($temp > 18) {
                    $tempDelta = $temp - 18;
                    // Annahme MaxTemp = 50 bei 5 Grad Unterschied und keine Wolken
                    $tempMinus = $solarItem["tempkoeff"] * (((2.917 * $temp) - 27.5) - 25) * (100 - $clouds) / 100;
                    $pv_estimate = $pv_estimate * (100 - $tempMinus) / 100;
                }
                if ($PV["kwh"]) {
                    $pv_estimate = round($pv_estimate / 1000, 1);
                } else {
                    $pv_estimate = round($pv_estimate / 10) * 10;
                }

                //wert zu pv_estimate hinzufügen
                $this->fc["hourly"][$k]["pv_estimate"] += $pv_estimate;
            }
        }

		// Tagesforecast
		$day_fc = 0;
		$d_o = 0;
		$k_o = 0;
		foreach($this->fc["daily"] as $k => $fc){
			
			$d = date("Ymd", $fc["ts"]);
			
			if($d_o != $d && $d_o != 0){
					$this->fc["daily"][$k_o]["pv_estimate"] = $day_fc;
					$day_fc = 0;
			}

			foreach($this->fc["hourly"] as $kh => $fch){
				if($d == date("Ymd", $fch["ts"])){
					$day_fc+= $fch["pv_estimate"];
				}
			}

			$k_o = $k;
			$d_o = $d;
		}
	} // function Forecast

	#### checkEvent #####################################################################
	private function checkEvent(){
		global $_IPS;
		$subIDs = IPS_GetChildrenIDs($this->instance);
		$evt = false;
		foreach($subIDs as $id){
			$obj = IPS_GetObject($id);
			if($obj["ObjectType"] == 4)$evt = true;
		}
		if(!$evt){
			$eid = IPS_CreateEvent(1);
			IPS_SetName($eid,"Stündliche Aktualisierung");
			IPS_SetParent($eid, $this->instance);
			IPS_SetEventCyclicTimeFrom($eid, 0, 10, 0);
			IPS_SetEventCyclic($eid,0,0,0,0,3,1);
			IPS_SetEventActive($eid, true);    
		}        
	} // function Event


	#### Sonnnenstandsberechnung  #################################################
	private function calcSun($ts, $dLongitude, $dLatitude){
		// Correction Timezone
		$now = new DateTime('now', new DateTimeZone('Europe/Berlin'));
	    if($now->format('I') == 1){
			//sommerzeit
			//$ts = $ts - 2*3600;
			$ts = $ts;
		}else{
			$ts = $ts;
		}
		

		$iYear = date("Y", $ts);
		$iMonth = date("m", $ts);
		$iDay = date("d", $ts);
		$dHours = date("H", $ts);
		$dMinutes = date("i", $ts);
		$dSeconds = date("s", $ts);

		$pi = 3.14159265358979323846;
		$twopi = (2*$pi);
		$rad = ($pi/180);
		$dEarthMeanRadius = 6371.01;	// In km
		$dAstronomicalUnit = 149597890;	// In km

		// Calculate difference in days between the current Julian Day
		// and JD 2451545.0, which is noon 1 January 2000 Universal Time

		// Calculate time of the day in UT decimal hours
		$dDecimalHours = floatval($dHours) + (floatval($dMinutes) + floatval($dSeconds) / 60.0 ) / 60.0;
		

		// Calculate current Julian Day

		$iYfrom2000 = $iYear;//expects now as YY ;
		$iA= (14 - ($iMonth)) / 12;
		$iM= ($iMonth) + 12 * $iA -3;
		$liAux3=(153 * $iM + 2)/5;
		$liAux4= 365 * ($iYfrom2000 - $iA);
		$liAux5= ( $iYfrom2000 - $iA)/4;
		$dElapsedJulianDays= floatval(($iDay + $liAux3 + $liAux4 + $liAux5 + 59)+ -0.5 + $dDecimalHours/24.0);

		// Calculate ecliptic coordinates (ecliptic longitude and obliquity of the
		// ecliptic in radians but without limiting the angle to be less than 2*Pi
		// (i.e., the result may be greater than 2*Pi)

		$dOmega= 2.1429 - 0.0010394594 * $dElapsedJulianDays;
		$dMeanLongitude = 4.8950630 + 0.017202791698 * $dElapsedJulianDays; // Radians
		$dMeanAnomaly = 6.2400600 + 0.0172019699 * $dElapsedJulianDays;
		$dEclipticLongitude = $dMeanLongitude + 0.03341607 * sin( $dMeanAnomaly ) + 0.00034894 * sin( 2 * $dMeanAnomaly ) -0.0001134 -0.0000203 * sin($dOmega);
		$dEclipticObliquity = 0.4090928 - 6.2140e-9 * $dElapsedJulianDays +0.0000396 * cos($dOmega);

		// Calculate celestial coordinates ( right ascension and declination ) in radians
		// but without limiting the angle to be less than 2*Pi (i.e., the result may be
		// greater than 2*Pi)

		$dSin_EclipticLongitude = sin( $dEclipticLongitude );
		$dY1 = cos( $dEclipticObliquity ) * $dSin_EclipticLongitude;
		$dX1 = cos( $dEclipticLongitude );
		$dRightAscension = atan2( $dY1,$dX1 );
		if( $dRightAscension < 0.0 ) $dRightAscension = $dRightAscension + $twopi;
		$dDeclination = asin( sin( $dEclipticObliquity )* $dSin_EclipticLongitude );

		// Calculate local coordinates ( azimuth and zenith angle ) in degrees

		$dGreenwichMeanSiderealTime = 6.6974243242 +	0.0657098283 * $dElapsedJulianDays + $dDecimalHours;

		$dLocalMeanSiderealTime = ($dGreenwichMeanSiderealTime*15 + $dLongitude)* $rad;
		$dHourAngle = $dLocalMeanSiderealTime - $dRightAscension;
		$dLatitudeInRadians = $dLatitude * $rad;
		$dCos_Latitude = cos( $dLatitudeInRadians );
		$dSin_Latitude = sin( $dLatitudeInRadians );
		$dCos_HourAngle= cos( $dHourAngle );
		$dZenithAngle = (acos( $dCos_Latitude * $dCos_HourAngle * cos($dDeclination) + sin( $dDeclination )* $dSin_Latitude));
		$dY = -sin( $dHourAngle );
		$dX = tan( $dDeclination )* $dCos_Latitude - $dSin_Latitude * $dCos_HourAngle;
		$dAzimuth = atan2( $dY, $dX );
		if ( $dAzimuth < 0.0 )
			$dAzimuth = $dAzimuth + $twopi;
		$dAzimuth = $dAzimuth / $rad;
		// Parallax Correction
		$dParallax = ($dEarthMeanRadius / $dAstronomicalUnit) * sin( $dZenithAngle);
		$dZenithAngle = ($dZenithAngle + $dParallax) / $rad;
		$dElevation = 90 - $dZenithAngle;
			
		return Array("azimuth" => $dAzimuth, "elevation" => $dElevation);
	}

	#### Variable Erzeugen  #######################################################
	private function CreateVariableByName($id, $name, $type, $profile = ""){
		# type: 0=boolean, 1 = integer, 2 = float, 3 = string;
		global $_IPS;
		$vid = @IPS_GetVariableIDByName($name, $id);
		if($vid === false)
		{
			$vid = IPS_CreateVariable($type);
			IPS_SetParent($vid, $id);
			IPS_SetName($vid, $name);
			IPS_SetInfo($vid, "this variable was created by script #".$this->instance);
			if($profile !== "") { IPS_SetVariableCustomProfile($vid, $profile); }
		}
		return $vid;
	}

	#################################################################################
	#### DWD WETTERDATEN ############################################################
	#################################################################################


	private function dwd_loadXML($station){
		//cache verzeichnis anlegen, wenn noch nicht da:
		if(! is_dir(dirname(__FILE__) . "/cache")){
			mkdir(dirname(__FILE__) . "/cache");    
		}
		$url       = "http://opendata.dwd.de/weather/local_forecasts/mos/MOSMIX_L/single_stations/" . $station . "/kml/MOSMIX_L_LATEST_" . $station . ".kmz";
		$fn_cache  = dirname(__FILE__) . "/cache/".$this->instance."-".$station.".cache";
		$fn_xml    = dirname(__FILE__) . "/cache/".$this->instance."-".$station.".xml";

		date_default_timezone_set("Europe/Berlin");
		$response = file_get_contents($url);
		file_put_contents($fn_cache, $response); 
		$zip = new ZipArchive;
		$res = $zip->open($fn_cache);
		if ($res === TRUE) {
			$zc = $zip->statIndex(0);
			$zf = $zc["name"];
			$zip->extractTo(dirname(__FILE__) . "/cache/", $zf);
			$zip->close();
			copy(dirname(__FILE__) . "/cache/".$zf, $fn_xml);
			unlink(dirname(__FILE__) . "/cache/".$zf);
		} else {
			echo 'Fehler, Code:' . $res;
		}

		$xmlstr = file_get_contents($fn_xml);

		// Namespace aufräumen
		$xmlstr = str_replace("<kml:", "<", $xmlstr);
		$xmlstr = str_replace("</kml:", "</", $xmlstr);

		$xmlstr = str_replace("<dwd:", "<", $xmlstr);
		$xmlstr = str_replace("</dwd:", "</", $xmlstr);

		$xmlstr = str_replace(" dwd:", " ", $xmlstr);

		$xml = simplexml_load_string($xmlstr);
		$ts = $xml->Document->ExtendedData->ProductDefinition->ForecastTimeSteps->TimeStep;

		$this->dwd_fca["info"]["model"]=$xml->Document->ExtendedData->ProductDefinition->ProductID->__toString();
		$this->dwd_fca["info"]["generation_time"]=$xml->Document->ExtendedData->ProductDefinition->IssueTime->__toString();


		foreach ($ts as $t) {
			$fc = ["ts" => strtotime($t), 
				"tiso" => $t->__toString(), 
				"day"  =>  date("z", strtotime($t)) - date("z"),
				"hour"  => date("G", strtotime($t))];
			$this->dwd_fca["hourly"][] = $fc;
		}
		//################## DATEN AUS XML EXTRAHIEREN / FLEXIBEL ERWEITERBAR ###########################################
		// Daten aus XML lesen (siehe oben verlinktes excel), 1 Parameter Wertekennung von DWD, 2. Parameter name im Json.
		$this->dwd_getData("Rad1h", "radiation", $xml);
		$this->dwd_getData("RRad1", "radiation_intensity", $xml);
		$this->dwd_getData("Neff", "clouds", $xml);
		$this->dwd_getData("RRL1c", "prec", $xml);
		$this->dwd_getData("RRS1c", "snow", $xml);
		$this->dwd_getData("SunD1", "sun", $xml);
		$this->dwd_getData("T5cm", "temp", $xml);        

		// Tageswerte berechnen
		$dayOld = 0;
		$t_min = 999;
		$t_max = -999;
		$t_avg  = 0;
		$cnt = 0;
		$cloud_avg = 0;
		$prec = 0;
		$snow = 0;
		$sun = 0;
		foreach($this->dwd_fca["hourly"] as $fc){
			$day = date("z", $fc["ts"]) - date("z");
			
			if($day != $dayOld){
			$this->dwd_fca["daily"][$dayOld]["ts"] = $ts_old;
			$this->dwd_fca["daily"][$dayOld]["txtx"] = date("D d.m.Y", $this->dwd_fca["daily"][$dayOld]["ts"]);

			$this->dwd_fca["daily"][$dayOld]["temp_max"] = $t_max;
			$t_max = -999;

			$this->dwd_fca["daily"][$dayOld]["temp_min"] = $t_min;
			$t_min = 999;
			
			$this->dwd_fca["daily"][$dayOld]["temp_avg"] = round($t_avg / $cnt,1);
			$t_avg = 0;

			$this->dwd_fca["daily"][$dayOld]["cloud_avg"] = round($cloud_avg / $cnt);
			$cloud_avg = 0;

			$this->dwd_fca["daily"][$dayOld]["prec"] = $prec;
			$prec=0;

			$this->dwd_fca["daily"][$dayOld]["snow"] = $snow;
			$snow=0;

			$this->dwd_fca["daily"][$dayOld]["sun"] = round($sun / 60);
			$sun=0;

			$cnt = 0;      
			}
			
			$t_min = ($fc["temp"] < $t_min)? $fc["temp"] : $t_min;
			$t_max = ($fc["temp"] > $t_max)? $fc["temp"] : $t_max;
			$cnt++;
			$t_avg     += $fc["temp"];
			$cloud_avg += $fc["clouds"];
			$prec      += $fc["prec"];
			$snow      += $fc["snow"];
			$sun       += $fc["sun"];

			$dayOld = $day;
			$ts_old = mktime(0,0,0, date("m",$fc["ts"]), date("d",$fc["ts"]), date("Y",$fc["ts"]) );
			$tiso   = $fc["tiso"];
		}

	} // DWD loadXML

	#### DWD XML Extraktion
	private function dwd_getData($idstr, $idtxt, &$xml){
		
		$gs = $xml->Document->Placemark->ExtendedData;
		foreach ($gs->Forecast as $g) {
			$id = $g->attributes()["elementName"][0]->__toString();
			if ($id == $idstr) {
			$val = $g->value->__toString();
			$valA = str_split($val, 11);
			}
		}
		foreach($this->dwd_fca["hourly"] as $k => $fc){
			$setval = trim(@$valA[$k]);
			
			// ############ Aufbereitung der Daten je nach Daten aus XML ################
			$setval = (trim($setval == '-'))? 0 : $setval;
			if($idstr == "SunD1") $setval = $setval / 60;
			if($idstr == "RRad1") $setval = floatval($setval);
			if($idstr == "T5cm")  $setval = $setval - 273;

			$this->dwd_fca["hourly"][$k][$idtxt] = $setval;
			
		}
	}// dwd_getData;

}


