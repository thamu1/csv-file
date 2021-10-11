<?php
$starttime = microtime(true); 
$file = $_FILES["file"]["name"];
$table = $_POST['table'];
$pass='';
ini_set('auto_detect_line_endings',TRUE);
$handle = fopen($file,'r');
if ( ($data = fgetcsv($handle) ) === FALSE )
{
?>	
<h2 style="text-align: center;">Cannot read from csv <?php echo $file;  ?>  </h2>
<?php
die();
}
$fields = array();
$field_count = 0;
for($i=0;$i<count($data); $i++) {
    $f = strtolower(trim($data[$i]));	
    if ($f) 
	{
		$f = substr(preg_replace ('/[^0-9a-z]/', '_', $f), 0, 20);
        $field_count++;
        $fields[] = $f.' VARCHAR(50)';
		if($data[$i]=='password' || $data[$i]=='Password' )
		{
			$pass=$data[$i];
        
		}

    }
}


$con=mysqli_connect("localhost","root","","csvexcel");


$query1 = "CREATE TABLE $table (" . implode(', ', $fields) . ')';
$reg1= mysqli_query($con,$query1);


mysqli_query($con, "LOAD DATA LOCAL INFILE '.$file.' INTO TABLE '.$table.' FIELDS TERMINATED by ','LINES TERMINATED BY '\n' IGNORE 1 LINES");

 
if($pass!=''){
$query2="UPDATE ".$table." SET ".$pass." = AES_ENCRYPT(".$pass.", 'encryption_key');";
$reg2= mysqli_query($con,$query2);
}



$sql4="SELECT * FROM ".$table;
$conclusion = mysqli_query($con,$sql4);
$rows = mysqli_num_rows($conclusion);



fclose($handle);
ini_set('auto_detect_line_endings',FALSE);
$endtime = microtime(true);
$time = ($endtime - $starttime);



if($rows)
{
?>
<body style="background-color:black; margin-top:20%; color:green">  
<h1 style="text-align: center;">Successfully Uploded</h1>
<h1 style="text-align: center;"><?php echo $rows; echo " "; ?> Number of Record Processed </h1>
<h2 style="text-align: center;"> Process Time: <?php echo $execution_time;  ?> Seconds </h2>
<?php
}
else{
	?>
	<body style="background-color:black;margin-top:20%; color:green; font-family:'Akronim',cursive;">    
   
	<h1 style="text-align: center;">UPLOAD SUCCESSFUL....!</h1>	
	<?php
	}
?>