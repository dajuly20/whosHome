<?php
ini_set("allow_url_fopen", 1);
$DBG = false;

$JSON = strtolower($_REQUEST["format"]) == "json";


function csvToArray($csvFile){
 
    $file_to_read = fopen($csvFile, 'r');
    if(!$file_to_read){ return [];  }
    while (!feof($file_to_read) ) {
        $lines[] = fgetcsv($file_to_read, 1000, ',');
 
    }
 
    fclose($file_to_read);
    return $lines;
}

$personen[0] = "niemand";
$personen[1] = "Johannes";
$personen[2] = "Franzi";
$personen[3] = "Julian";
$personen[4] = "Reserve";
$case = "";
$json = file_get_contents('http://192.168.188.88/status');
$obj = json_decode($json, true);
$offset = 0.3;
$voltage = $obj["adcs"][0]["voltage"];

// Minimal voltage if someone is home shuld be a few volts
if (round($voltage) != 0){
   $voltage = $voltage - $offset;
}



$rollingAvgFilename="rollingAvg.csv";
$rollingAvgArray = csvToArray($rollingAvgFilename)[0];
//var_dump($rollingAvgArray);
// Pushing newest to the que
array_push($rollingAvgArray, $voltage);
//var_dump($rollingAvgArray);
// Taking last one off
array_shift($rollingAvgArray);
//var_dump($rollingAvgArray);

$rollingAvgVoltage = array_sum($rollingAvgArray)/count($rollingAvgArray);
//echo $rollingAvgVoltage; 
//echo "<br><br>";
$csvStringToAvgVoltageFile = implode(",", $rollingAvgArray);
//echo $csvStringToAvgVoltageFile;
file_put_contents($rollingAvgFilename, $csvStringToAvgVoltageFile);


$momentaryVoltage = $voltage;
$voltage = $rollingAvgVoltage;




$lookup =  csvToArray("voltageToResistors.csv");
array_shift($lookup);

$prevDistance = 100;

foreach($lookup as $key => $value){
 	if( $value && is_numeric($value[0])){
        	if($key){
        		$prevDistance = abs($lookup[$key-1][0] - $voltage);
        	}
		$distance =  abs($value[0] - $voltage);
	
		if($DBG) echo "<br>\nCase: ".$value[1]. " \t(".$value[0]."  Distanz: ".$distance.") vorh. ".$prevDistance;
 	      	if($distance < $prevDistance){
			$case = $lookup[$key][1];
			$lookupVoltage = $lookup[$key][0];
			if($DBG) echo " <== chose that one";
		}
	}
}
//echo "<br><br>	\n\n";
if(!isset($lookupVoltage)) $lookupVoltage = 0;
if(!isset($case)) $case = 0;

if($DBG) echo $JSON ? "JSON" : "TEXT"; 
if(!$JSON) echo "Spannung: ".$rollingAvgVoltage."V rollingAvg (mom: ". $momentaryVoltage." V<br>\n";
if(!$JSON) echo "Wir haben Fall ".$case. " (".$lookupVoltage." V <br><br>\n\n";
$personenDaheim = array();

for ($i=0; $i < strlen($case); $i++) {
	$caseNr = $case[$i];
	array_push($personenDaheim,$personen[$caseNr]);
}

if(!$JSON) echo "Es sing gerade daheim:<br><b> ". implode(" ", $personenDaheim) ."</b>\n";

$jsonObj["persons"] = $personenDaheim;
$jsonObj["momvoltage"] = $momentaryVoltage;
$jsonObj["avgvoltage"] = $rollingAvgVoltage;

if($JSON) echo json_encode($jsonObj);
?>
