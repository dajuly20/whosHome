<?php
ini_set("allow_url_fopen", 1);
$DBG = false;
function csvToArray($csvFile){
 
    $file_to_read = fopen($csvFile, 'r');
 
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

$lookup =  csvToArray("voltageToResistors.csv");
array_shift($lookup);

$prevDistance = 100;
echo "Spannung: ". $voltage." V<br>\n";

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

echo "Wir haben Fall ".$case. " (".$lookupVoltage." V)<br><br>	\n\n";
$personenDaheim = "";
for ($i=0; $i < strlen($case); $i++) {
	$caseNr = $case[$i];
	$personenDaheim .= $personen[$caseNr]. " ";
}

echo "Es sing gerade daheim:<br><b> $personenDaheim</b>\n";
?>


