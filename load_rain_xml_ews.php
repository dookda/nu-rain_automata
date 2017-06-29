<?php
	//header("content-type: text/html; charset=windows-874");

	$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
	$url = 'http://202.129.59.76/website/ews_all/webservice.php';

	//$xml = file_get_contents($url, false, $context);	
	//echo $xml;

	function convert2UTF8($url){
		$f=fopen($url,"r") or exit("ไม่สามารถเปิดไฟล์ได้ !");
		$open = fopen("temp/copy.xml","w") ;
		while (!feof($f)) {
		$x=fgetc($f); //อ่านข้อความจากไฟล์มาทีละบรรทัด
		fwrite($open,"$x") ;	//วนลูปเพื่อเขียนข้อความที่อ่านมาลงสู่ไฟล์ที่กำหนด
		}
		fclose($f);
		fclose($open) ;

		$f=fopen("temp/copy.xml","r") or exit("ไม่สามารถเปิดไฟล์ได้ !");
		$open = fopen("temp/copy1.xml","w") ;
		while (!feof($f)) {
		$x=fgetc($f); //อ่านข้อความจากไฟล์มาทีละบรรทัด mb_convert_encoding($xmlstr,"UTF-8");
		//$conv_str = iconv('tis-620','iso-8859-11'.'//TRANSLIT',$x);
		//echo $x;

		$conv_str = iconv(mb_detect_encoding($x, mb_detect_order(), true), "TIS-620", $x);	
		fwrite($open,"$conv_str") ;	//วนลูปเพื่อเขียนข้อความที่อ่านมาลงสู่ไฟล์ที่กำหนด
		}
		fclose($f);
		fclose($open) ;
	}

	function checkType($x_in){
		if(is_null($x_in)){
			return 0;
		}elseif($x_in=='N/A') {
			return 0;
		}else{
			return $x_in;
		}
	}
    
	convert2UTF8($url);
	$xml = simplexml_load_file('temp/copy1.xml');

	// Connect database
    require('../lib/conn.php');
    $dbconn = pg_connect($conn_rain) or die('Could not connect'); 

    //loop insert data to db
	foreach($xml->children() as $dat)
    {
        $st_code=$dat->attributes()->stn;
        $st_name=$dat->name;
        $tam_name=$dat->subdistrict;
        $amp_name=$dat->district;
        $pro_name=$dat->province;
        $rain_date=$dat->date;
        $rain=$dat->rain;
        $rain12h=$dat->rain12h;
        $rain24h=$dat->rain24h;      

        $sql = "insert into rain_ews(st_code,st_name,tam_name,amp_name,pro_name,rain_date,rain,rain12h,rain24h)values('$st_code','$st_name','$tam_name','$amp_name','$pro_name','$rain_date',".checkType($rain).",".checkType($rain12h).",".checkType($rain24h).")";
        pg_query($dbconn, $sql);
    }
    echo "insert successful!";

	// Closing connection
    pg_close($dbconn);
?>