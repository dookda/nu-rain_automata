<?php
    //connect to mysql db
    //$con = mysql_connect("username","password","") or die('Could not connect: ' . mysql_error());
    //connect to the employee database
    //mysql_select_db("employee", $con);
	
header("content-type: text/html; charset=utf-8");
//include "connect_db.php";
// open a file and read data
//read the json file contents
$jsondata = file_get_contents('http://ews.dwr.go.th/website/webservice/rain_daily.php?uid=sakda&upass=da45060071&dmode=1&dtype=2');


//convert json object to php associative array
$data = json_decode($jsondata, true);

$sta = $data['station'];
$cntsta = count($sta);

//echo $sta;
//echo $cntsta;

    function replaceVal($x_in){

        if(is_numeric($x_in)){
            $x_new = $x_in;
            return $x_new;
        }elseif(is_null($x_in)){
            $x_new = 0;
            return $x_new;
        }else{
            $x_new = 0;
            return $x_new;
        }
    }

for ($x = 0; $x <= $cntsta-1; $x++) {
	//echo "The number is: $x <br>";
	
    //get the employee details
    $station_id = $data['station'][$x]['id'];
    $rrain_12hr = $data['station'][$x]['rain12h'];
    $rrain_7hr = $data['station'][$x]['rain07h'];
    $rtemp_c = $data['station'][$x]['temp'];
    $rwater_lev = $data['station'][$x]['wl'];
    $rsoil_moi = $data['station'][$x]['soil'];



    $rain_12hr = replaceVal($rrain_12hr);
    $rain_7hr = replaceVal($rrain_7hr);
    $temp_c = replaceVal($rtemp_c);
    $water_lev = replaceVal($rwater_lev);
    $soil_moi = replaceVal($rsoil_moi);
    
    //insert into mysql table
    $sql = "INSERT INTO rain_dwr(station_id, rain_12hr, rain_7hr, temp_c, water_lev, soil_moi, tstamp)
    VALUES('$station_id', $rain_12hr, $rain_7hr, $temp_c, $water_lev, $soil_moi, now()::timestamp with time zone at time zone 'Asia/Bangkok')";

    require('../lib/conn.php');
    $dbconn = pg_connect($conn_rain) or die('Could not connect');
    pg_query($sql);
    pg_close($dbconn);

	echo $sql;
}
	
	
?>