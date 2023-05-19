<?

class symconpvForecast extends IPSModule
{

	private $fc;
	private $force;
	public function Create()
	{
		//Never delete this line!
		parent::Create();
		$this->RegisterPropertyString('location', '');
        $this->RegisterPropertyString('dwd_station', '');
        $this->RegisterPropertyBoolean('toArchiv', false);
        $this->RegisterPropertyString('solarData', '[]');

		$this->RegisterPropertyBoolean('enable_archiv', false);
		$this->RegisterPropertyInteger('wr_kwp_max', 5000);

		/*$this->RegisterPropertyInteger('kwp', 1000);
		$this->RegisterPropertyInteger('azimuth', 0);
		$this->RegisterPropertyInteger('tilt', 30);
		$this->RegisterPropertyInteger('type', 0);*/
		$this->RegisterPropertyInteger('efficiency', 95);
		//$this->RegisterPropertyInteger('cloudeffect', 65);
		$this->RegisterPropertyFloat('tempkoeff', 0.65);
		$this->RegisterPropertyInteger('horizon', 0);
		
		$this->RegisterPropertyBoolean('autotune', false);
		$this->RegisterPropertyInteger('pv_id', 0);
		$this->RegisterPropertyBoolean('kwh', false);
		$this->RegisterPropertyString('fc_type', 'dwd-icon');

		$this->RegisterPropertyBoolean('obj', false);
		$this->RegisterPropertyInteger('obj_direction', 0);
		$this->RegisterPropertyInteger('obj_distance', 0);
		$this->RegisterPropertyInteger('obj_height', 10);
		$this->RegisterPropertyInteger('obj_size', 50);
		$this->RegisterPropertyInteger('obj_effect', 50);
		$this->RegisterPropertyInteger('forecastVariables', 3);
		$this->RegisterPropertyString('objData', '[]');


		$this->RegisterTimer('Update', 1000*60*15, 'SpvFC_Update($_IPS[\'TARGET\']);');

	}

	public function ApplyChanges() {
        //Never delete this line!
		parent::ApplyChanges();
        $name = IPS_GetName(@$_IPS["TARGET"]);
		if(strpos($name,"nnamed") === false && !empty($name)){
			if((empty($this->ReadPropertyString("dwd_station")) && $this->ReadPropertyString("fc_type") == "dwd-mosmix")||
				empty($this->ReadPropertyString("location"))){
				echo "Achtung: Location und DWD Station eintragen!";
				return;
			}
		}
		
	    $this->SetTimerInterval("Update", 1000*10*15); // Alle 15 Minuten.
		$this->UpdateForce(true);
    }

	private function initfc(){
		
		if(!empty($this->ReadPropertyString("location"))){
			$latlon  = json_decode($this->ReadPropertyString("location"),true);
		} else {
			$latlon["longitude"] = 0;
			$latlon["latitude"] = 0;
		}


		$model =    $this->ReadPropertyString('fc_type');
		$station =  $this->ReadPropertyString("dwd_station");
		if($model == "dwd-icon"){
			$station = $latlon;
		}

		//$pvtype = ( $this->ReadPropertyInteger('type') == 1 )? "D" : "";
		$solardata = json_decode($this->ReadPropertyString('solarData'), true);
		$objdata = json_decode($this->ReadPropertyString('objData'), true);
		$kwp_max = 0;
		foreach($solardata as $solaritem){
			if(!$solaritem["activ"]) continue;
			$kwp_max += $solaritem["kwp"];
		}

		$PV      =[     #"kwp"         =>  $this->ReadPropertyInteger('kwp'), 
						"kwp"         =>  $kwp_max, 								//Summe aller aktiven Solaranalgen
						"wr_kwp_max"  =>  $this->ReadPropertyInteger('wr_kwp_max'), //Maximaler Imput Weselrichter
						#"azimuth"     =>  $this->ReadPropertyInteger('azimuth'),   	// eigentlich sind es 30 aber soll ist vergleich zeigt leichte verschiebung vom abend - also mehr nach rechts drehen
						#"tilt"        =>  $this->ReadPropertyInteger('tilt'),
                        #"pvtype"      => $pvtype,                                         // D= Dachform geständert, Ausrichtung der offenen seite = azimuth
						"solarData"   => $solardata,
						"efficiency"  =>  $this->ReadPropertyInteger('efficiency'),
						#"cloudeffect" =>  $this->ReadPropertyInteger('cloudeffect'),  // Effekt für Leistungsreduktion bei Bewölkung in Prozent
						"lon"         => $latlon["longitude"],
						"lat"         => $latlon["latitude"],
						"tempkoeff"   => $this->ReadPropertyFLoat('tempkoeff'),    // Temperaturkoeffizient lt. Datenblatt
						"horizon"     => $this->ReadPropertyInteger('horizon'),    // Horizont für Einfallswinkel Sonne
						"autotune"    => $this->ReadPropertyBoolean('autotune'),   // Automatisch optimieren.
						"pv_id"       => $this->ReadPropertyInteger('pv_id'),      // Automatisch optimieren id Tagesaktuellen Bedarf
						"kwh"         => $this->ReadPropertyBoolean('kwh'),        // ausgabe in KWH statt wh

                        // Beschattungsobjekt
						#"obj" 			=> $this->ReadPropertyBoolean('obj'),
						#"obj_direction" => $this->ReadPropertyInteger('obj_direction'),   // Himmelsrichtung des Beschattungsobjektes Grad von Norden =0
						#"obj_distance"  => $this->ReadPropertyInteger('obj_distance'),      // Abstand des Objektes in Meter
						#"obj_height"    => $this->ReadPropertyInteger('obj_height'),      // Höhe des Objektes in Meter
						#"obj_size"      => $this->ReadPropertyInteger('obj_size'),    // Prozentualer Abdeckungsanteil der PV Anlage durch Objekt (Mittelwert)
						#"obj_effect"    => $this->ReadPropertyInteger('obj_effect')/100,    // Azuswirkung der Beschattung auf Ertrag (kwp mit gemessenem Ertrag bei Beschattung (grob))
						"objData"   => $objdata,

						"forecast"    	=> $this->ReadPropertyInteger("forecastVariables"),
						"enable_archiv" => $this->ReadPropertyBoolean("enable_archiv"),
					];
		$this->fc = false;
		if(!empty($station))$this->fc = new PVForecastcls($model ,$station, $PV, $this->InstanceID);
	}

	public function UpdateForce(){
		$this->force = true;
		$this->Update();
		$this->force = false;
	}

	public function Update(){
		$this->initfc();
		if($this->fc){
			if($this->force)$this->fc->loadForecast(true);
			$this->fc->CreateFCVariables($this->ReadPropertyInteger("forecastVariables"));
			$this->fc->forecastChart();
			$this->fc->autotune();
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

	/* Abweichungen des Forecasts ermitteln */
	public function autotuneGetDeviationHour($h, $radi=0){
		$this->initfc();
		return $this->fc->getDeviationHour($h, $radi);
	}

	public function autotunePrintInfo(){
		$this->initfc();
		$this->fc->autotunePrint();
	}


	
}

class PVForecastcls{
	private $fc;
	private $fc_info;
	private $fc_model;

	private $PV;
	private $dwd_fca;
	private $dwd_station;
	private $instance;
	const TYPE_D_CORRECTION = 1.15; // Faktor for increasing / decreasing pv estimate for Type D Positioning

	const CACHE_AGE = 3600*3;       // Maximales ALter der Berechung und Wettervorhersage

	/* model = dwd-mosmix, dwd-icon
	   model_info: Array ( lat / lon ) oder StationID
	   PV : PV-Anlagen INfo
	   $instance = ID der Instanz 
	*/
	function __construct($model, $model_info, $PV, $instance ){
		$this->instance = $instance;
		// Wetterdaten laden (DWD)
		$this->PV = $PV;
		$this->fc_info = $model_info;
		$this->fc_model = $model;
		
		$this->loadForecast();
	}

	#### Tageswert Zurckgeben #####################################################
	public function getDayForecast($ts){
	
		foreach($this->fc["daily"] as $fc){
			if(date("Ymd", $fc["ts"]) == date("Ymd", $ts)){
				return @$fc["pv_estimate"];
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
		if(!isset($this->fc["daily"])){
			return false;
		}
		foreach($this->fc["daily"] as $fc){
			$varName = ($cnt == 0)? 'Vorhersage Heute' : "Vorhersage Heute + $cnt";
			$id = $this->CreateVariableByName($this->instance,$varName,2, $varprof);
			IPS_SetVariableCustomProfile($id, $varprof);
			
			#if($cnt == 0 && date("G")<10 || $cnt > 0) setValue($id, $fc["pv_estimate"]); // Früher nur vor 10 Uhr - heute aktualsiieren wir alles, da alle Daten im FC drin sind.
			
			if(array_key_exists("pv_estimate", $fc)){
				setValue($id, $fc["pv_estimate"]);
			}
			
			if($cnt >= $days)break;
			$cnt++;
		}

	}

	#### Ausgabe als Chart #######################################################
	function forecastChart(){
		$id = $this->CreateVariableByName($this->instance,"PV Forecast Chart",3,"~HTMLBox");
		$pv_max = ($this->PV["kwh"])? $this->PV["kwp"]/1000 : $this->PV["kwp"];
		$pv_max = $pv_max*1.4;
		$html = "<style>
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
        $html .= "<div style='width:100%; height:200px;overflow:hidden;'>";
			$cnt=0;
			$html .= "<div class='pv' style='height: 200px; width:1px;background-color:transparent;'></div>";
			if(!isset($this->fc["hourly"])){
				return false;
			}
			foreach ($this->fc["hourly"] as $fc){
				
				if($fc["hour"] > 6 && $fc["hour"] < 22 ){
					$cnt++;
					if(array_key_exists("pv_estimate", $fc)){
						$height = round($fc["pv_estimate"]/$pv_max*100*2,0);
						$html .= "<div class='pv' style='height: $height"."px'>".$fc["pv_estimate"]."</div>";
					}else{
						$html .= "<div class='pv' style='height: 0"."px'>0</div>";
					}
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
					if(array_key_exists("pv_estimate", $fc)){
						$height = round($fc["pv_estimate"]/$pv_max*100*2,0);
						$html .= "<div class='pv_txt' style='height:20px'>".($fc["hour"])."h</div>";
						if($fc["hour"]==12){
							$dayfc = $this->getDayForecast($fc["ts"]);
							$dayfc = ($this->PV["kwh"])? $dayfc : $dayfc/1000;
							$dayfc = round($dayfc,1);
							$html.="<div class='pv_day' style='left:". (31 * $cnt )."px'>$dayfc kWh</div>";
						}
					}
				}
				if($cnt >96)break;
			}
		$html .="</div>";
		setvalue($id,$html);

	}

    public function UpdateArchiv(){
		if(!$this->PV["enable_archiv"]) return; //function nur ausführen wenn werte geloggt werden sollen

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

		$dateTimeZone = new DateTimeZone("Europe/Berlin");
		$dateTime = new DateTime("now", $dateTimeZone);
		$timeOffset = $dateTimeZone->getOffset($dateTime);

        foreach ($this->fc["hourly"] as $fitem){
            //print_r($fitem);
			if(!array_key_exists("pv_estimate", $fitem)) continue;
			
            $d = new DateTime(); //, new DateTimeZone('UTC')
            $d->setTimestamp($fitem["ts"]);
            $d->modify( '-'.$this->PV["forecast"].' day' );

            if($d->getTimestamp() > $now->getTimestamp())break;

            //echo "Uhrzeit=".gmdate("d.m.Y H:i", $d->getTimestamp())." Value=" .$fitem["pv_estimate"]. "Watt\r\n";
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

			// Prüfen welches Wettermodell verwendet wird und dann Forecast laden.
			switch($this->fc_model){
				case "dwd-mosmix":
									$this->dwd_loadXML($this->fc_info);
									break;
				
				case "dwd-icon":	$this->open_meteo_icon($this->fc_info);
									break;
			}
			
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

	#### PV Berechnen ############################################################
	private function pvForecast($PV){
		$lat      = $PV["lat"];
		$lon      = $PV["lon"];

		if(!isset($this->fc["hourly"])){
			return false;
		}

		$notSet = true;

		foreach($PV["solarData"] as $solarItem){
			//durchlauf für jede ausrichtung
			if(!$solarItem["activ"]) continue; //alle deaktivierten überspringen
			foreach($this->fc["hourly"] as $k => $fc){         
				$ts       = $fc["ts"];
				$clouds   = $fc["clouds"];
				$temp     =  $fc["temp"];

				// Sonnenposition ermitteln
				$ret            = $this->calcSun($ts, $lon, $lat);
				$sun_azimuth    = $ret["azimuth"];
				$sun_elevation  = $ret["elevation"];            
	
				// Leistungsfaktor festlegen
				$lf = 100;

				// Beschattung berechnen
				foreach($PV["objData"] as $objitem){
					if(!$objitem["activ"]) continue;

					// 2x berechnen und mittelwert bilden für volle stunde und 30min
					for($x=0; $x <=3; $x++){
						$time = $ts + $x * 900;
	
						$sunpos = $this->calcSun($time, $lon, $lat);
						$alpha   = deg2rad($sunpos["elevation"]);
						$azimuth = $sunpos["azimuth"];

						if($azimuth >= $objitem["obj_direction_start"] && $objitem["obj_direction_End"] >= $azimuth){
							//$shadeAngle  = $azimuth - $objitem["obj_direction"] - 90;
							//echo "Uhrzeit=".gmdate("d.m.Y H:i", $time)."\r\n";
							$shadeLength = round($objitem["obj_height"] / (tan($alpha) ) ,2);
							//echo "Schattenlaenge=".$shadeLength."m\r\n";
							if($shadeLength < $objitem["obj_distance"]){
								$lf_minusShadeA[$x] = 100;
							}else{
								$verschattungswert = $objitem["obj_shading"];
								$verschattungseffect = $objitem["obj_effect"];
								//echo "Uhrzeit=".gmdate("d.m.Y H:i", $time)."\r\n";
								$lf_minusShadeA[$x] = ($lf-$verschattungswert) + ($verschattungswert * ($verschattungseffect/100));
							}
						}else{
							$lf_minusShadeA[$x] = 100;
						}
					}
					$lf_minusShade = ($lf_minusShadeA[0] + $lf_minusShadeA[1]+$lf_minusShadeA[2] + $lf_minusShadeA[3]) / 4;
		
					if($lf > $lf_minusShade) $lf = $lf_minusShade;
					//echo "Abzug=".$lf."\r\n";
				}
	
				/* ------------ Kalkulation des Ertrages
				Daten von https://echtsolar.de/wp-content/uploads/2021/02/Ausrichtungstabelle-Photovoltaik-in-Deutschland-768x705.png
				Formel über Newton Polynom: https://de.planetcalc.com/9023/
				*/
				$az_abw = round(abs($sun_azimuth - 180 - $solarItem["azimuth"]),1);
				if($az_abw > 180){
						$lf_minusA = 100;
				}else{
					$lf_minusA = (-0.00000000147) * pow( $az_abw, 5) + (0.00000057668) * pow ($az_abw, 4) + (-0.00008107497) * pow ($az_abw, 3) + (0.00559429998 * pow ($az_abw, 2)) + (0.05160253657 * $az_abw);
					}
					$lf = $lf - $lf_minusA;

				// Neigungswikel und Sonne
				$elev_abw = 90 - $sun_elevation  - $solarItem["tilt"];
				$x = $elev_abw;
				$lf_minusE   = (1/100) * pow ($x, 2); // Neue Formel anhand Tabellen und Werten 

				if($sun_elevation < @$PV["horizon"] + 5) $lf_minusE = 50; // Sonnenuntergang            
				if($sun_elevation < @$PV["horizon"] ) $lf_minusE = 100; // Sonnenuntergang
				$lf_minusE = round($lf_minusE,1);
				$lf = $lf - $lf_minusE;


				// Bewölkung hängt von der Wolkendicke und von dem Einstrahlungswinkel ab.
				/* Wir rechnen ab sofort mit Globalstrahlung
					$lf_cloud_dec = (100 - (pow($clouds,2.4)/600) * $PV["cloudeffect"]/100) /100;
					$lf = $lf * $lf_cloud_dec;
				*/

				$lf = ($lf < 0 )? 0 : $lf;     
				$radiation  = $fc["rad_total"];
				$base_Watts = $radiation * $solarItem["kwp"]/1000 * $PV["efficiency"]/100;    
				
				$pv_estimate = $base_Watts * ($lf / 100);			
	
				// Temperaturverlust
				if($temp > 18){
					$tempDelta = $temp - 18;
					// Annahme MaxTemp = 50 bei 5 Grad Unterschied und keine Wolken
					$tempMinus = $PV["tempkoeff"] * (( (2.917 * $temp) - 27.5) - 25 ) * (100 - $clouds) / 100;               
					$pv_estimate = $pv_estimate * (100-$tempMinus)/100;
				}
		

				$pv_estimate_orig = $pv_estimate;

				if($PV["kwh"]){
					$pv_estimate = round($pv_estimate/1000,1);
					$pv_estimate_orig = round($pv_estimate_orig/1000,1);
				}else{
					$pv_estimate = round($pv_estimate/10)*10;
					$pv_estimate_orig = round($pv_estimate_orig/10)*10;
				}

				if(!array_key_exists("pv_estimate", $this->fc["hourly"][$k])){
					$this->fc["hourly"][$k]["pv_estimate"] = $pv_estimate;
				}else{
					$this->fc["hourly"][$k]["pv_estimate"] += $pv_estimate;
				}

				$pv_estimate_orig = $pv_estimate;
				if(!array_key_exists("pv_estimate_orig", $this->fc["hourly"][$k])){
					$this->fc["hourly"][$k]["pv_estimate_orig"] = $pv_estimate_orig;
				}else{
					$this->fc["hourly"][$k]["pv_estimate_orig"] += $pv_estimate_orig;
				}

				if($this->fc["hourly"][$k]["pv_estimate"] > $PV["wr_kwp_max"])$this->fc["hourly"][$k]["pv_estimate"] = $PV["wr_kwp_max"];

				$notSet = False;
			} // foreach FC
		}

		if($PV["autotune"]){
			foreach($this->fc["hourly"] as $k => $fc){  
				$radiation  = $fc["rad_total"];
				$ts       	= $fc["ts"];

				$pv_estimate = $this->fc["hourly"][$k]["pv_estimate"];
				$pv_estimate = $pv_estimate * $this->getDeviationHour(date("G",$ts), $radiation);
				if($pv_estimate > $PV["kwp"]*1.2) $pv_estimate= $PV["kwp"]*1.2;

				$this->fc["hourly"][$k]["pv_estimate"] = $pv_estimate;
			}
		}

		if($notSet) return false;
		
		$id_prev = false;
		foreach($this->fc["hourly"] as $id => $fcE){
			if($id_prev){
				$this->fc["hourly"][$id_prev]["pv_estimate"] = $fcE["pv_estimate"];
				$this->fc["hourly"][$id_prev]["pv_estimate_orig"] = $fcE["pv_estimate_orig"];
				if(isset($fcE["rad"])) $this->fc["hourly"][$id_prev]["rad"] = $fcE["rad"];
				if(isset($fcE["rad_intensity"])) $this->fc["hourly"][$id_prev]["rad_intensity"] = $fcE["rad_intensity"];
				$this->fc["hourly"][$id_prev]["rad_total"] = $fcE["rad_total"];
			}
			$id_prev = $id;
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

		return true;
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
		$ts = $ts - 2*3600;

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
	### open-meteo abfrage - API - DWD ICON-D2  #####################################
	#################################################################################
	private function open_meteo_icon($latlon){
		$lat = trim($latlon["latitude"]);
		$lon = trim($latlon["longitude"]);
		$lat = round($lat, 3);
		$lon = round($lon, 3);
		$url = "https://api.open-meteo.com/v1/dwd-icon?latitude=$lat&longitude=$lon&hourly=temperature_2m,precipitation,rain,showers,snowfall,snow_depth,weathercode,cloudcover,windspeed_10m,diffuse_radiation,direct_normal_irradiance&daily=weathercode,temperature_2m_max,temperature_2m_min,precipitation_sum,rain_sum,showers_sum,snowfall_sum,windspeed_10m_max&timeformat=unixtime&timezone=Europe%2FBerlin";

		if(! is_dir(dirname(__FILE__) . "/cache")){
			mkdir(dirname(__FILE__) . "/cache");    
		}

		date_default_timezone_set("Europe/Berlin");
		$ret = @file_get_contents($url);
		
		if(empty($ret)){
			echo "Fehler Wetterdaten können nciht geladen werden!";
			return false;
		}
		$ret = json_decode($ret, true);
		$fca["info"]["model"]="open-meteo:iconD2";
		$fca["info"]["generation_time"]=time();

		foreach($ret["hourly"]["time"] as $id => $val){
			$fca["hourly"][$id]["ts"]    = $val;
			$fca["hourly"][$id]["tiso"]  = gmdate('Y-m-d\TH:i:s',$val);
			$fca["hourly"][$id]["day"]   = date("z") - date("z", $val);
			$fca["hourly"][$id]["hour"]  = date("G", $val);
			$fca["hourly"][$id]["wmo_code"]  =  $ret["hourly"]["weathercode"][$id];
			$fca["hourly"][$id]["clouds"]    =  $ret["hourly"]["cloudcover"][$id];
			$fca["hourly"][$id]["prec"]      =  $ret["hourly"]["precipitation"][$id];
			$fca["hourly"][$id]["rain"]      =  $ret["hourly"]["rain"][$id];
			$fca["hourly"][$id]["snow"]      =  $ret["hourly"]["snowfall"][$id];
			$fca["hourly"][$id]["snowdepth"] =  $ret["hourly"]["snow_depth"][$id];
			$fca["hourly"][$id]["temp"]      =  $ret["hourly"]["temperature_2m"][$id];
			$fca["hourly"][$id]["wind"]      =  $ret["hourly"]["windspeed_10m"][$id];
			$fca["hourly"][$id]["rad_diff"]  =  $ret["hourly"]["diffuse_radiation"][$id];
			$fca["hourly"][$id]["rad_dni"]   =  $ret["hourly"]["direct_normal_irradiance"][$id];
			$fca["hourly"][$id]["rad_total"] =  intval($ret["hourly"]["direct_normal_irradiance"][$id])+intval($ret["hourly"]["diffuse_radiation"][$id]);
		}

		/* FORECAST FÜLLEN
		   manchmal liefert der Forecast nur die Daten ab aktueller Uhrzeit - für dei Forecast Charts / Ausgaben, sollten jedoch alle
		   werte berücksichtigt werden. Deshalb holen wir aus dem alten Forecast, bei fehlenden Daten diese zurück*/
		for($ih=6;$ih<16;$ih++){
		#	echo "\n".$ih."->";
			$found = false;
			foreach($fca["hourly"] as $id => $fc){
				if($fc["day"] == 0 && $fc["hour"] == $ih) $found = true;
			}
		#	echo $found;
			if(!$found){
				foreach($this->fc["hourly"] as $fo){
					if($fo["day"]== 0 && $fo["hour"] == $ih){
						$fca["hourly"][] = $fo;
					}
				}
			}
		}
		usort($fca["hourly"], function($a, $b) {
			return strcmp($a['ts'], $b['ts']);
		});

		foreach($ret["daily"]["time"] as $id => $val){
			$fca["daily"][$id]["ts"] = $val;
			$fca["daily"][$id]["tiso"] = gmdate('Y-m-d\TH:i:s',$val);
			$fca["daily"][$id]["day"] = date("z") - date("z", $val);
			$fca["daily"][$id]["wmo_code"] =  $ret["daily"]["weathercode"][$id];
			$fca["daily"][$id]["temp_max"] =  $ret["daily"]["temperature_2m_max"][$id];
			$fca["daily"][$id]["temp_min"] =  $ret["daily"]["temperature_2m_min"][$id];
			$fca["daily"][$id]["prec"]   =  $ret["daily"]["precipitation_sum"][$id];
			$fca["daily"][$id]["rain"]   =  $ret["daily"]["rain_sum"][$id];
			$fca["daily"][$id]["snow"]   =  $ret["daily"]["snowfall_sum"][$id];
		}
		
		$this->fc = $fca;
	}

	#################################################################################
	#### DWD WETTERDATEN ############################################################
	#################################################################################
	private function dwd_loadXML($station){
		//cache verzeichnis anlegen, wenn noch nicht da:
		if(! is_dir(dirname(__FILE__) . "/cache")){
			mkdir(dirname(__FILE__) . "/cache");    
		}
		$station = trim($station);
		$url       = "http://opendata.dwd.de/weather/local_forecasts/mos/MOSMIX_L/single_stations/" . $station . "/kml/MOSMIX_L_LATEST_" . $station . ".kmz";
		$fn_cache  = dirname(__FILE__) . "/cache/".$this->instance."-".$station.".cache";
		$fn_xml    = dirname(__FILE__) . "/cache/".$this->instance."-".$station.".xml";


		date_default_timezone_set("Europe/Berlin");
		$response = @file_get_contents($url);
		if(empty($response)){
			echo "Wetterstation ungültig. kann MOSMIX KML nicht laden ($url)";
			return false;
		}
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

		unset($this->fc);
		$this->fc["info"]["model"]=$xml->Document->ExtendedData->ProductDefinition->ProductID->__toString();
		$this->fc["info"]["generation_time"]=$xml->Document->ExtendedData->ProductDefinition->IssueTime->__toString();


		foreach ($ts as $t) {
			$fc = ["ts" => strtotime($t), 
				"tiso" => $t->__toString(), 
				"day"  =>  date("z", strtotime($t)) - date("z"),
				"hour"  => date("G", strtotime($t))];
			$this->fc["hourly"][] = $fc;
		}
		//################## DATEN AUS XML EXTRAHIEREN / FLEXIBEL ERWEITERBAR ###########################################
		// Daten aus XML lesen (siehe oben verlinktes excel), 1 Parameter Wertekennung von DWD, 2. Parameter name im Json.
		
		$this->dwd_getData("Rad1h", "rad", $xml);
		$this->dwd_getData("RRad1", "rad_intensity", $xml);
		$this->dwd_getData("Neff", "clouds", $xml);
		$this->dwd_getData("RRL1c", "prec", $xml);
		$this->dwd_getData("RRS1c", "snow", $xml);
		$this->dwd_getData("SunD1", "sun", $xml);
		$this->dwd_getData("T5cm", "temp", $xml);        

		foreach($this->fc["hourly"] as $id => $e){
			$this->fc["hourly"][$id]["rad_total"] = round($e["rad"] * $e["rad_intensity"]/100);
		}

	
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
		foreach($this->fc["hourly"] as $fc){
			$day = date("z", $fc["ts"]) - date("z");
			
			if($day != $dayOld){
			$this->fc["daily"][$dayOld]["ts"] = $ts_old;
			$this->fc["daily"][$dayOld]["txtx"] = date("D d.m.Y", $this->fc["daily"][$dayOld]["ts"]);

			$this->fc["daily"][$dayOld]["temp_max"] = $t_max;
			$t_max = -999;

			$this->fc["daily"][$dayOld]["temp_min"] = $t_min;
			$t_min = 999;
			
			$this->fc["daily"][$dayOld]["temp_avg"] = round($t_avg / $cnt,1);
			$t_avg = 0;

			$this->fc["daily"][$dayOld]["cloud_avg"] = round($cloud_avg / $cnt);
			$cloud_avg = 0;

			$this->fc["daily"][$dayOld]["prec"] = $prec;
			$prec=0;

			$this->fc["daily"][$dayOld]["snow"] = $snow;
			$snow=0;

			$this->fc["daily"][$dayOld]["sun"] = round($sun / 60);
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
		$found = false;
		foreach ($gs->Forecast as $g) {
			$id = $g->attributes()["elementName"][0]->__toString();
			if ($id == $idstr) {
			$val = $g->value->__toString();
			$valA = str_split($val, 11);
				$found = true;
			}
		}
		if(!$found){
			echo "Attribute $idstr not found in Forecast XML";
			return false;
		}
		//prüfen ob nur - im Array ist.
		$valChk = implode("",$valA);
		if (preg_match('/^[ -]*$/', $valChk)){
			echo "$idstr not available in forecast Data.\n";
			return;
		}

		foreach($this->fc["hourly"] as $k => $fc){
			$setval = trim(@$valA[$k]);
			// ############ Aufbereitung der Daten je nach Daten aus XML ################
			$setval = (trim($setval == '-'))? 0 : $setval;
			if($idstr == "SunD1") $setval = $setval / 60;
			if($idstr == "RRad1") $setval = floatval($setval);
			if($idstr == "T5cm")  $setval = $setval - 273;

			$this->fc["hourly"][$k][$idtxt] = $setval;
			
		}
	}// dwd_getData;


    #### AUTOTUNE ###### ##########################################################
	public function autotune(){
		if($this->PV["autotune"]){
			$fn = dirname(__FILE__)."/".$this->instance.".autotune.json";
			$hist = json_decode(@file_get_contents($fn),true);
			$hour = date("G");
			$day  = date("j");
			$fc = $this->fc;
			
			//Aktuellen Tag leeren
			if($hour > 1 && $hour < 4){
				unset ($hist["data"][$day]);
			}

			// Forecast am Morgen befüllen, wenn noch nicht geschehen.
			if(!isset($hist["data"][$day]) && $hour > 6){
				foreach($fc["hourly"] as $f){
					if($f["day"] == 0 && $f["hour"] > 5 && $f["hour"] < 22){                
						$hist["data"][$day][$f["hour"]]["fc"] =   $f["pv_estimate"];
						$hist["data"][$day][$f["hour"]]["fc_orig"] =   $f["pv_estimate_orig"];
						$hist["data"][$day][$f["hour"]]["radi"] = $f["rad_total"];
					}
				}
			}	 // forecast füllen.

			// Ist-Daten dazu laden 
			if($hour > 5 && $hour < 23){
				// Stundendaten erfassen
				$ACID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
				$ts 					= mktime($hour - 1,  0,  0, date("m"), date("d"), date("Y"));
				$te					    = mktime($hour - 1 , 59, 59, date("m"), date("d"), date("Y"));

				$aDat = AC_GetLoggedValues ($ACID, $this->PV["pv_id"], $ts, $te, 60);
				if(isset($aDat[0])){
					$setHour = $hour -1; // Werte der letzten Stunde protokollieren.
					$hist["data"][$day][$setHour]["ist_sum"] = round($aDat[0]["Value"],2);
					$hist["data"][$day][$setHour]["ist"]     = round($aDat[0]["Value"] - @$hist["data"][$day][$setHour-1]["ist_sum"],2);            
				}
			}
			file_put_contents($fn,json_encode($hist));			
		}
	}

	#### AUTOTUNE - PRINT #######################################################
	public function autotunePrint(){
		$fn = dirname(__FILE__)."/".$this->instance.".autotune.json";
        $hist = json_decode(@file_get_contents($fn),true);
        $dat = $hist["data"];
        krsort($dat);
        echo "<pre>";
        echo "DAY    HOUR   FC_ORIG   FC   IST     ABW   ZÄHLER  RADIATION\n";
        echo "------------------------------------------------------------\n";
        
        $dayIST = 0;
        $dayFC  = 0;
		$dayFC_orig = 0;
        $dayold = 0;
        $day = 0;
        foreach($dat as $d => $hs){
            ksort($hs);
            $dayIST = 0;
            $dayFC = 0; 
			$dayFC_orig = 0;           
            foreach($hs as $h => $e){
                if($day != $dayold){
                    echo "-------------------------------------------------------------\n";
                    echo str_pad(round($dayFC_orig,1),18, " ", STR_PAD_LEFT);
					echo str_pad(round($dayFC,1),7, " ", STR_PAD_LEFT);
                    echo str_pad(round($dayIST,1),7, " ", STR_PAD_LEFT);
                    $fce = ($dayFC == 0)? 0.1: $dayFC;
                    echo str_pad(round( ($dayIST - $fce) / $fce * 100 ), 7, " ", STR_PAD_LEFT)."%";                    
                    echo "\n";
                    echo "=============================================================\n";
                    $dayIST = 0;
                    $dayFC = 0;
					$dayFC_orig = 0;
                }

                echo str_pad($d, 3, " ", STR_PAD_LEFT); 
                echo "    ".str_pad($h,4, " ", STR_PAD_LEFT);    
				echo str_pad(round(@$e["fc_orig"],1),7, " ", STR_PAD_LEFT);            
				echo str_pad(round(@$e["fc"],1),7, " ", STR_PAD_LEFT);
                echo str_pad(round(@$e["ist"],1),7, " ", STR_PAD_LEFT);
                
                if(@$e["fc"] > 0){
                    $fcab = (@$e["ist"] - $e["fc"]) / $e["fc"] * 100;
                    echo str_pad(round($fcab), 7, " ", STR_PAD_LEFT)."%";
                }elseif(@round($e["ist"],1) > 0){
                    echo str_pad("100", 7, " ", STR_PAD_LEFT)."%";
                }else{
                    echo str_pad("-  ", 7, " ", STR_PAD_LEFT)." ";
                }
                
                echo str_pad(round(@$e["ist_sum"],1), 8, " ", STR_PAD_LEFT);
                echo str_pad(round(@$e["radi"]),10, " ", STR_PAD_LEFT);
                echo "\n";
                $dayIST += @$e["ist"];
                $dayFC  += @$e["fc"];
				$dayFC_orig += @$e["fc_orig"];
                $day_old = $day;
            }

            echo "---------------------------------------------------------------\n";
            
			echo str_pad(round($dayFC_orig,1),18, " ", STR_PAD_LEFT);
			echo str_pad(round($dayFC,1),7, " ", STR_PAD_LEFT);
            echo str_pad(round($dayIST,1),7, " ", STR_PAD_LEFT);
            $fce = ($dayFC == 0)? 0.1: $dayFC;
            echo str_pad(round( ($dayIST - $fce) / $fce * 100 ), 7, " ", STR_PAD_LEFT)."%";                                
            echo "\n";
            echo "===============================================================\n";
        }
        echo "</pre>\n";
		echo "FN: $fn";
	}
	

	#### AUTOTUNE - getHour ########################################################
	public function getDeviationHour($hour, $radi){
		static $abw;
		if(empty($abw))$abw = $this->autotuneCalc();

		$h = intval($hour);
		if($radi < 300){
			$dev = @$abw[$h]["low"]["deviation"];
		}elseif($radi >= 300 && $radi < 800){
			$dev = @$abw[$h]["medium"]["deviation"];
		}elseif($radi > 800){
			$dev = @$abw[$h]["high"]["deviation"];
		}
		$dev = ($dev == 0)? 1 : $dev;		
		return $dev;
	}

	#### AUTOTUNE - calc ##########################################################
	private function autotuneCalc(){
		$fn = dirname(__FILE__)."/".$this->instance.".autotune.json";
		$hist = json_decode(@file_get_contents($fn),true);
		$dat = $hist["data"];
		$istK = 0;
		$fcK  = 0;
		ksort($dat);
	
		// Matrix für Abweichungen aufbauen ---------------------------------------------------------------------
		$low    = 300;
		$high   = 800;
		foreach($dat as $d => $de){
			foreach($de as $h => $e){
				if(@$e["fc_orig"] > 0 && @$e["ist"] > 0){
					if($e["radi"] < $low){
						$key = "low";
					}
					if($e["radi"] >= $low && $e["radi"] < $high){
						$key = "medium";
					}
					if($e["radi"] > $high){
						$key = "high";
					}        
					#echo  $h.": ".  $e["ist_sum"]. "  -->  ".$e["fc_orig"]."\n";
					@$cArr[$h][$key]["cnt"]++;
					@$cArr[$h][$key]["ist"] += $e["ist"];
					@$cArr[$h][$key]["fc_orig"] += $e["fc_orig"];
				} // if wert vorhanden
			}
		}
		ksort($cArr);
		// deviation berechnen  -----------------------------------------------------------------------------
		foreach($cArr as $h => $e){
			if(isset($cArr[$h]["low"]["ist"])){
				$cArr[$h]["low"]["deviation"]       = round($e["low"]["ist"] / $e["low"]["fc_orig"],2);
			}else{
				$cArr[$h]["low"]["deviation"] = 1;
			}
			
			if(isset($cArr[$h]["medium"]["ist"])){
				$cArr[$h]["medium"]["deviation"] = round($e["medium"]["ist"] / $e["medium"]["fc_orig"],2);
			}else{
				$cArr[$h]["medium"]["deviation"] = 1;
			}
			
			if(isset($cArr[$h]["high"]["ist"])){
				$cArr[$h]["high"]["deviation"]     = round($e["high"]["ist"] / $e["high"]["fc_orig"],2);
			}else{
				$cArr[$h]["high"]["deviation"] = 1;
			} 
		}	
		return $cArr;		
	}


}


