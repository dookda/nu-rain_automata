<?php
header("content-type: text/html; charset=utf-8");


function xml2array_parse($xml){ 
     foreach ($xml->children() as $parent => $child){ 
         $return["$parent"] = xml2array_parse($child)?xml2array_parse($child):"$child"; 
     } 
     return $return; 
} 

//$arrlength = count($dat);
function add2db($fields,$dat,$sta_val,$dcode){
	foreach($fields as $field){
		//echo $field;	
		foreach($dat as $x => $x_value) {
			switch ($x_value){
				case null:
					$x_value = 0;
					break;
				case '-':
					$x_value = 0;
					break;
				default:
					$x_value;
					break;
			}
			
			if($field==$x){
				if(is_numeric($x_value)){
					$sql = "update rain_auto_300sec set $x = $x_value where station_id='$sta_val' and dcode='$dcode'";
				}else{				
					$sql = "update rain_auto_300sec set $x = '$x_value' where station_id='$sta_val' and dcode='$dcode'";				
				}			
				//echo $sql;
				//echo '<p>';

				require('../lib/conn.php');
				$dbconn = pg_connect($conn_rain) or die('Could not connect');

				pg_query($sql);	

				pg_close($dbconn);		
			}				
		}
	} 
}

$fields = array('location','latitude','longitude','elevation','weather','temp_c','tempmax_c',
				'tempmin_c','relative_humidity','relative_humidity_max','relative_humidity_min','dewpoint_c', 
				'wind_dir','wind_degrees','wind_kph','pressure_mbar','solar_radiation','solar_radiation_max',
				'solar_radiation_min','rain_1hr','rain_3hr','rain_24hr','rain_72hr','rain_gmt', 'observation_time');

$stas = array('530100-001','530102-001','530104-001','530105-001','530106-001','530107-001','530108-001','530110-001','530112-001','530113-001',
				'530114-001','530115-001','530116-001','530200-001','530201-001','530202-001','530202-002','530203-001','530204-001','530205-001',
				'530301-001','530301-002','530303-001','530303-002','530304-001','530305-001','530306-001','530308-001','530400-001','530401-001',
				'530402-001','530403-001','530404-001','530405-001','530405-002','530406-001','530500-001','530501-001','530502-001','530503-001',
				'530504-001','530600-001','530601-001','530603-001','530604-001','530701-001','530701-002','530702-001','530703-001','530704-001',
				'530704-002','530705-001','530706-001','530707-001','530708-001','530709-001','530710-001','530711-001','530801-001','530801-002',
				'530802-001','530802-002','530802-003','530802-004','530802-005','530802-006','530802-007','530802-008','530803-001','530804-001',
				'530804-002','530805-001','530806-001','530807-001','530807-002','530808-001','530900-001','530901-001','530902-001','530903-001',
				'530904-001','670312-001','670402-001','540118-001','540702-001','640505-001','500513-001','540702-001','540118-001');

foreach($stas as $sta => $sta_val){				
	//$xml  = simplexml_load_file('http://weatherwatch.in.th/xml/current_obs.php?station=540702-001'); 

	$dcode = date(DATE_ATOM);	
	$xml  = simplexml_load_file('http://weatherwatch.in.th/xml/current_obs.php?station='.$sta_val);

	// insert data
	$dat = xml2array_parse($xml);
	
	$dateSta = substr($dat['observation_time'], -10);	
	$dateNow = date("d/m/Y");

	if($dateSta == $dateNow){
		echo $dateSta." - ".$dateNow.'<p>';

		require('../lib/conn.php');
		$dbconn = pg_connect($conn_rain) or die('Could not connect');
		$sql = "insert into rain_auto_300sec (station_id,tstamp,dcode) values ('$sta_val',now()::timestamp with time zone at time zone 'Asia/Bangkok','$dcode')";
		echo $sql;
		pg_query($sql);
		pg_close($dbconn);	

		add2db($fields,$dat,$sta_val,$dcode);
	}

		
}
?>