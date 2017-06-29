<?php
    //connect to mysql db
    //$con = mysql_connect("username","password","") or die('Could not connect: ' . mysql_error());
    //connect to the employee database
    //mysql_select_db("employee", $con);
	
header("content-type: text/html; charset=utf-8");
//include "connect_db.php";
// open a file and read data
//read the json file contents
$jsondata = file_get_contents('http://data.tmd.go.th/api/Weather3Hours/V1/?type=json');
    
//convert json object to php associative array
$data = json_decode($jsondata, true);

$sta = $data['Stations'];
$cntsta = count($sta);
 
for ($x = 0; $x <= $cntsta-1; $x++) {
	echo "The number is: $x <br>";
	
    //get the employee details
    $sta_number = $data['Stations'][$x]['WmoNumber'];
    $sta_th = $data['Stations'][$x]['StationNameTh'];
    $sta_en = $data['Stations'][$x]['StationNameEng'];
    $lat = $data['Stations'][$x]['Latitude']['Value'];
    $lon = $data['Stations'][$x]['Longitude']['Value'];
    $dtime = $data['Stations'][$x]['Observe']['Time'];
    //$temperature = $data['Stations']['$x']['Observe']['BarometerTemperature']['Value'];
    $br_pressure = $data['Stations'][$x]['Observe']['BarometerTemperature']['Value'];
    $st_pressure = $data['Stations'][$x]['Observe']['StationPressure']['Value'];
    $msl_pressure = $data['Stations'][$x]['Observe']['MeanSeaLevelPressure']['Value'];
    $dewpoint = $data['Stations'][$x]['Observe']['DewPoint']['Value'];
    $rh = $data['Stations'][$x]['Observe']['RelativeHumidity']['Value'];
    $vapor_pressure = $data['Stations'][$x]['Observe']['VaporPressure']['Value'];
    $visibility = $data['Stations'][$x]['Observe']['LandVisibility']['Value'];
    $wind_dir = $data['Stations'][$x]['Observe']['WindDirection']['Value'];
    $wind_kph = $data['Stations'][$x]['Observe']['WindSpeed']['Value'];
    $rain = $data['Stations'][$x]['Observe']['Rainfall']['Value'];
    
    //insert into mysql table
    $sql = "INSERT INTO rain_tmd(sta_number, sta_th, sta_en, lat, lon, dtime, br_pressure, st_pressure, msl_pressure, dewpoint, rh, vapor_pressure, visibility, wind_dir, wind_kph, rain, tstamp)
    VALUES('$sta_number', '$sta_th', '$sta_en', $lat, $lon, now()::timestamp with time zone at time zone 'Asia/Bangkok', $br_pressure, $st_pressure, $msl_pressure, $dewpoint, $rh, $vapor_pressure, $visibility, '$wind_dir', $wind_kph, $rain, now()::timestamp with time zone at time zone 'Asia/Bangkok')";

    require('../lib/conn.php');
    $dbconn = pg_connect($conn_rain) or die('Could not connect');
    pg_query($sql);
    pg_close($dbconn);
}
	
	
?>