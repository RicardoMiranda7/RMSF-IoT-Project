#!/usr/bin/php -q 
<?php 
define("IPServer", "192.168.0.101");
define("PORTServer", 1901);
define("HOST","db.ist.utl.pt");
define("USER", "ist175847");
define("PASS", "sgks0281");


/** 
  * Listens for requests and forks on each connection 
  */ 
$last_time_java;
$__server_listening = true; 

/* IP / port */ 
server_loop(IPServer, PORTServer);

error_reporting(E_ALL); 
set_time_limit(0); 
ob_implicit_flush(); 
declare(ticks = 1); 

become_daemon(); 

/* nobody/nogroup, change to your host's uid/gid of the non-priv user */ 
change_identity(1000, 1000); 

/* handle signals */ 
pcntl_signal(SIGTERM, 'sig_handler'); 
pcntl_signal(SIGINT, 'sig_handler'); 
pcntl_signal(SIGCHLD, 'sig_handler'); 



/** 
  * Change the identity to a non-priv user 
  */ 
function change_identity( $uid, $gid ) 
{ 
    if( !posix_setgid( $gid ) ) 
    { 
        print "Unable to setgid to " . $gid . "!\n"; 
        exit; 
    } 

    if( !posix_setuid( $uid ) ) 
    { 
        print "Unable to setuid to " . $uid . "!\n"; 
        exit; 
    } 
} 

/** 
  * Creates a server socket and listens for incoming client connections 
  * @param string $address The address to listen on 
  * @param int $port The port to listen on 
  */ 
function server_loop($address, $port) 
{ 
   
    GLOBAL $__server_listening; 

    if(($sock = socket_create(AF_INET, SOCK_STREAM, 0)) < 0) 
    { 
        echo "failed to create socket: ".socket_strerror($sock)."\n"; 
        exit(); 
    } 

    if(($ret = socket_bind($sock, $address, $port)) < 0) 
    { 
        echo "failed to bind socket: ".socket_strerror($ret)."\n"; 
        exit(); 
    } 

    if( ( $ret = socket_listen( $sock, 0 ) ) < 0 ) 
    { 
        echo "failed to listen to socket: ".socket_strerror($ret)."\n"; 
        exit(); 
    } 

    socket_set_nonblock($sock); 
    
    echo "waiting for clients to connect\n"; 

    while ($__server_listening) 
    { 
        $connection = @socket_accept($sock); 
        if ($connection === false) 
        { 
            usleep(100); 
        }elseif ($connection > 0) 
        { 
            handle_client($sock, $connection); 
        }else 
        { 
            echo "error: ".socket_strerror($connection); 
            die; 
        } 
    } 
} 

/** 
  * Signal handler 
  */ 
function sig_handler($sig) 
{ 
    switch($sig) 
    { 
        case SIGTERM: 
        case SIGINT: 
            exit(); 
        break; 

        case SIGCHLD: 
            pcntl_waitpid(-1, $status); 
        break; 
    } 
} 

/** 
  * Handle a new client connection 
  */ 
function handle_client($ssock, $csock) 
{ 
   
    $last_time_java = time();
    GLOBAL $__server_listening; 

    $pid = pcntl_fork(); 

    if ($pid == -1) 
    { 
        /* fork failed */ 
        echo "fork failure!\n"; 
        die; 
    }elseif ($pid == 0) 
    { 
        /* child process */ 
        $__server_listening = false; 
        socket_close($ssock); 
        interact($csock); 
        socket_close($csock); 
        //exit();
    }else 
    { 
        socket_close($csock); 
    } 
} 

function DBBaseStationRequest($PANid){
   $dsn = sprintf("mysql:host=%s;dbname=%s", HOST, USER);
   try{
            $connection = new PDO($dsn, USER, PASS);
    }
    catch(PDOException $exception){
        echo($exception->getMessage());exit();
    }
    $RetrieveSettings= "SELECT * FROM PAN WHERE idPAN = '$PANid' ;";
    $Result = $connection->prepare($RetrieveSettings);
$Result->execute();
    if ( $Result  == FALSE){
        $info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
    }

    
	$Settings = $Result->fetch(PDO::FETCH_ASSOC);
//echo("idPAN: " + $Result->idPAN + "	Buzzer: " + $Result->Buzzer + "\n");
	echo"Base Station Settings: ";echo($Settings['Buzzer']);echo($Settings['Propagation']);echo"\n";
//print_r($Result);
return $Settings;

}

function DBBaseStationSensorSettings($PANid){
   $dsn = sprintf("mysql:host=%s;dbname=%s", HOST, USER);
   try{
            $connection = new PDO($dsn, USER, PASS);
    }
    catch(PDOException $exception){
        echo($exception->getMessage());exit();
    }
    $RetrieveSettings= "SELECT * FROM Node WHERE idPAN = '$PANid' ;";
    $Result = $connection->prepare($RetrieveSettings);
$Result->execute();
    if ( $Result  == FALSE){
        $info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
    }

    $i=1;
    echo"Base Station Sensor Settings: ";
	while($Settings = $Result->fetch(PDO::FETCH_ASSOC)){
		echo($Array[$i] = $Settings['idNode']);
		$i++;
		echo($Array[$i] = $Settings['Activated']);
		$i++;
	}
	echo " ";
	echo($Array[1]); echo($Array[2]); echo($Array[3]); echo($Array[4]);
//echo("idPAN: " + $Result->idPAN + "	Buzzer: " + $Result->Buzzer + "\n");

//print_r($Result);
return $Array;

}



function DBRetrieveSettings($PANid){
   $dsn = sprintf("mysql:host=%s;dbname=%s", HOST, USER);
   try{
            $connection = new PDO($dsn, USER, PASS);
    }
    catch(PDOException $exception){
        echo($exception->getMessage());exit();
    }
    $RetrieveSettings= "SELECT * FROM PAN WHERE idPAN = '$PANid' ;";
    $Result = $connection->prepare($RetrieveSettings);
$Result->execute();
    if ( $Result  == FALSE){
        $info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
    }

	$BuzzerPropagation = $Result->fetch(PDO::FETCH_ASSOC);
//echo("idPAN: " + $Result->idPAN + "	Buzzer: " + $Result->Buzzer + "\n");
	echo"Result2: ";echo($BuzzerPropagation['Buzzer']);echo($BuzzerPropagation['Propagation']);echo"\n";
//print_r($Result);
return $BuzzerPropagation;

}

function DBRetrieveSensorSettings($PANid){
   $dsn = sprintf("mysql:host=%s;dbname=%s", HOST, USER);
   try{
            $connection = new PDO($dsn, USER, PASS);
    }
    catch(PDOException $exception){
        echo($exception->getMessage());exit();
    }
    $RetrieveSensorSettings= "SELECT DISTINCT idNode,Activated FROM Node NATURAL JOIN PersonPAN WHERE idPAN = '$PANid' ;";
    $Result = $connection->prepare($RetrieveSensorSettings);
$Result->execute();
    if ( $Result  == FALSE){
        $info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
    }
$SensorSetsStr = "";
	while($row = $Result->fetch(PDO::FETCH_ASSOC)){
		echo("row: ");echo($row['idNode']);echo(" ");echo($row['Activated']);
		$SensorSetsStr.= " " . $row['idNode'] . " " . $row['Activated'];
		
 }echo($SensorSetsStr);
return $SensorSetsStr;
//echo("idPAN: " + $Result->idPAN + "	Buzzer: " + $Result->Buzzer + "\n");


}

function AccessDatabaseLogin($Email, $Passwd){
   $dsn = sprintf("mysql:host=%s;dbname=%s", HOST, USER);
   try{
            $connection = new PDO($dsn, USER, PASS);
    }
    catch(PDOException $exception){
        echo($exception->getMessage());exit();
    }
    $VerifyEmail = "SELECT * FROM Person WHERE Email = '$Email' ";
    $EmailResult = $connection->query($VerifyEmail);
    if ($EmailResult == FALSE){
        $info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
    }
    echo"Email: [";echo $Email;echo"] ";echo "RowCount: ";echo ($EmailResult->rowCount());echo"\n";
    if (($EmailResult->rowCount())==0) return "NOK EMAIL";
    else{
        $VerifyPasswd = "SELECT * FROM Person WHERE Password = '$Passwd' AND Email = '$Email'";
        $PasswordResult = $connection->query($VerifyPasswd);
        if ($EmailResult == FALSE){
            $info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
        }
        echo"Passwd: [";echo $Passwd;echo"] ";echo "RowCount: ";echo ($PasswordResult->rowCount());echo"\n";
        if (($PasswordResult->rowCount())==0){ return "NOK PASSWD";
        }else {return "OK";}
    }
}

    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
function SendMail($Email, $Name,$GeneratedPasswd)
{
	$to = $Email;

	$subject = 'Website Change Reqest';

	$headers = "From: Home Security Project\r\n";

	$headers .= "MIME-Version: 1.0\r\n";

	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


	$message = '<html><body>';
	$message .= '<p>Hi ';
	$message .= $Name;
	$message .= ',</p>';
	$message .= '<p>Thanks for you register in HomeSecurity! Your password for accessing the HomeSecurity App is: ';
	$message .= $GeneratedPasswd;
	$message .= '</p>';
	$message .= '<p>Thanks,</p>';
	$message .= '<p>Jose & Diogo - @RMSF 2015/2016 Antonio Grilo - IST - ULISBOA</p>';
	$message .= '</body></html>';


	mail($to, $subject, $message, $headers);
}

function DBModifyBuzzer($PANid, $Parameter, $value, $idNode){
   $dsn = sprintf("mysql:host=%s;dbname=%s", HOST, USER);
   try{
            $connection = new PDO($dsn, USER, PASS);
    }
    catch(PDOException $exception){
        echo($exception->getMessage());exit();
    }
	switch ($Parameter){
		case "BUZZER":
			$Modify = "UPDATE PAN SET Buzzer = '$value' WHERE idPAN = '$PANid'";
			break;
		case "PROPAGATION":
			$Modify = "UPDATE PAN SET Propagation = '$value' WHERE idPAN = '$PANid'";			
			break;
		case "ENABLE":
			$Modify = "UPDATE PAN SET Enable = '$value' WHERE idPAN = '$PANid'";			
			break;
		case "SENSOR":
			$Modify = "UPDATE Node SET Activated = '$value' WHERE idPAN = '$PANid' AND idNode = '$idNode'";			
			break;
	}
   
    $Result = $connection->query($Modify);
    if ( $Result == FALSE){
        $info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
    }
return "OK";
}


function AccessDatabaseRegister($Name, $Email, $PANid, $PANsk){
   $dsn = sprintf("mysql:host=%s;dbname=%s", HOST, USER);
   try{
            $connection = new PDO($dsn, USER, PASS);
    }
    catch(PDOException $exception){
        echo($exception->getMessage());exit();
    }
    $VerifyPANid = "SELECT * FROM PAN WHERE idPAN = '$PANid'";
    $PANidResult = $connection->query($VerifyPANid);
    if ($PANidResult == FALSE){
        $info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
    }
    echo"PANid: [";echo $PANid;echo"] ";echo "RowCount: ";echo ($PANidResult->rowCount());echo"\n";
    if (($PANidResult->rowCount())==0) return "NOK PANID";
    else{
        $VerifyPANsk = "SELECT * FROM PAN WHERE Serial_key = '$PANsk' AND idPAN = '$PANid'";
        $PANskResult = $connection->query($VerifyPANsk);
        if ($PANskResult == FALSE){
            $info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
        }
        echo"PAN SK: [";echo $PANsk;echo"] ";echo "RowCount: ";echo ($PANskResult->rowCount());echo"\n";
        if (($PANskResult->rowCount())==0){ return "NOK PANSK";
        }else {
		$GeneratedPasswd = generateRandomString(6);
            echo"Password Generated for user $Name\n";
            $InsertData = "INSERT INTO Person VALUES('$Email', '$Name', '$GeneratedPasswd', '0')";
	$InsertResult = $connection->query($InsertData);
    	if ($InsertResult == FALSE){
        	$info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
    	}
 	$InsertData = "INSERT INTO PersonPAN VALUES('$Email', '$PANid', '1')";
	$InsertResult = $connection->query($InsertData);
    	if ($InsertResult == FALSE){
       	 $info = $connection->errorInfo();echo("Error: {$info[2]}\n");exit();
    	}

	SendMail($Email,$Name,$GeneratedPasswd);
            return "OK";
        }
    }
}
function DBReadNotify($Email){
	   $dsn = sprintf("mysql:host=%s;dbname=%s", HOST, USER);
   try{
            $connection = new PDO($dsn, USER, PASS);
    }
    catch(PDOException $exception){
        echo($exception->getMessage());exit();
    }
	$sql_notify = "SELECT Notify FROM Person WHERE Email = '$Email'";
	$read_result = $connection->query($sql_notify);
	if ($read_result == FALSE){
					$info = $connection->errorInfo();
					echo("Error: {$info[2]}\n");
					exit();
				}
	$notified = $read_result->fetch(PDO::FETCH_ASSOC);
	if($notified['Notify']==1) {
        //echo "Notified: $notified; ";
        return "UPTODATE";
	}

	$sql_read = "SELECT idNode, TStamp FROM NodeReadings NATURAL JOIN PersonPAN WHERE TStamp IN (SELECT MAX(TStamp) FROM NodeReadings);";
				$read_result = $connection->query($sql_read);
				if ($read_result == FALSE){
					$info = $connection->errorInfo();
					echo("Error: {$info[2]}\n");
					exit();
				}
				$sample = $read_result->fetch(PDO::FETCH_ASSOC);
				
				$str = sprintf("ALARM %d %d", time($sample['TStamp']), $sample['idNode']);
				
				return $str;

}


function AccessDatabase($mode, $packet_time=NULL, $packet_node_nr=NULL, $PANid)
{
		GLOBAL $last_time_java;
		 $dsn = sprintf("mysql:host=%s;dbname=%s", HOST, USER);
		try{
			$connection = new PDO($dsn, USER, PASS);
		}
		catch(PDOException $exception){
			echo("<p>Error: ");
			echo($exception->getMessage());
			echo("</p>");
			exit();
		}
		switch($mode){
			
			case "insert":	
				$sql_insert = "INSERT INTO NodeReadings VALUES('$packet_time', '$packet_node_nr', '$PANid');";
				$sql_update = "UPDATE Person NATURAL JOIN PersonPAN SET Notify = 0 WHERE idPAN = '$PANid'";
				echo("Packet TIme: ");echo($packet_time);echo(" Rest: ");echo($packet_node_nr);echo($PANid);
				$result = $connection->query($sql_insert);
				$result = $connection->query($sql_update);
				if ($result == FALSE){
					$info = $connection->errorInfo();
					echo("Error: {$info[2]}\n");
					exit();
				}
				return "OK";
				break;
	


			

				/*
				echo "$result\n";
				
				if($result==$last_time_java){
					return 0; // App is up to date
				}else{
					return $result;  // App needs to be notified
				}*/
				//break;
          
			}

}

function DBupdateNotify($Email){
				$dsn = sprintf("mysql:host=%s;dbname=%s", HOST, USER);
   try{
            $connection = new PDO($dsn, USER, PASS);
    }
    catch(PDOException $exception){
        echo($exception->getMessage());exit();
    }
				$sql_update = "UPDATE Person 
								SET Notify = '1' WHERE Email = '$Email';";
				
				$update_result = $connection->query($sql_update);
				if ($update_result == FALSE){
					$info = $connection->errorInfo();
					echo("Error: {$info[2]}\n");
					exit();
				}
				return 1;
}

function interact($socket) 
{ 	
		$notified_java=true;
		GLOBAL $last_time_java;
       $last_time_java = time();
        
        if (false === ($buf = socket_read($socket, 128, PHP_NORMAL_READ))) {
            echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
            //break 2;
        }
echo"Received: [";echo $buf;echo"] ";echo"\n";
        $newbuf=str_replace("\n","",$buf);
echo"Newbuf: [";echo $buf;echo"] ";echo"\n";
        $MsgParameters = explode(" ", $newbuf);
        foreach($MsgParameters as $MsgParameter){
			echo $MsgParameter;echo " ";
		}
		echo "\n";
        switch ($MsgParameters[0]) {
            case 'BASE':
                 echo "BASE detected! ";
                 switch ($MsgParameters[1]) {
					 case 'NOTIFICATION':
						// Comunicar com base de dados
						//AccessDatabase('insert',);
						//$last_time_base_sation;
						$packet_tstamp = new DateTime();
						$str = AccessDatabase('insert', $packet_tstamp->format('Y-m-d H:i:s'),$MsgParameters[2], $MsgParameters[3]);
						echo "Sequence Nr: $packet_seqno    Time stamp: ";
						echo ($packet_tstamp->format('Y-m-d H:i:s')); // Se mandar o U como argumento vai o inteiro UNIXcaalho
						socket_write($socket, $str, strlen($str));
						break;
					case 'REQSETS':
						echo(" New Resquest\n");
						$GeneralSettings = DBBaseStationRequest($MsgParameters[2]);
						$SensorSettings = DBBaseStationSensorSettings($MsgParameters[2]);
						$str = sprintf("OK %d %d %d %d %d %d %d", $GeneralSettings['Enable'], $GeneralSettings['Buzzer'], $GeneralSettings['Propagation'],$SensorSettings[1],$SensorSettings[2],$SensorSettings[3],$SensorSettings[4]);
						echo("Sent [Enabled] [Buzzer] [Propagation] [NodeID1] [Enabled1] [NodeID2] [Enabled2]: "); echo($str);
                        socket_write($socket, $str, strlen($str));
                        
						
				}	
                break;
			
            case 'JAVA':
 echo "JAVA detected! ";
                switch ($MsgParameters[1]) {
                    case 'LOGIN':
                        echo "[LOGIN]\n";
                        $str = AccessDatabaseLogin($MsgParameters[2], $MsgParameters[3]);
                        socket_write($socket, $str, strlen($str));
                        echo"Sent: ";echo($str);
                        break;
                    case 'REGISTER':
                        echo "[REGISTER]\n";
						$str = AccessDatabaseRegister($MsgParameters[2], $MsgParameters[3], $MsgParameters[4], $MsgParameters[5]);
						socket_write($socket, $str, strlen($str));
                        break;
                    case 'ADD':
                        echo "[ADD]\n";
                        break;  
                    case 'MODIFY':
                        echo "[MODIFY]\n";
			$str = DBModifyBuzzer($MsgParameters[2], $MsgParameters[3], $MsgParameters[4], $MsgParameters[5]);
                        socket_write($socket, $str, strlen($str));
                        break;
                    case 'RETRIEVE':
						echo "[RETRIEVE]\n";
 $SensorSetsStr = DBRetrieveSensorSettings($MsgParameters[2]);
						$Settings = DBRetrieveSettings($MsgParameters[2]);
						$str = sprintf("OK %d %d %d", $Settings['Enable'], $Settings['Buzzer'], $Settings['Propagation']);
$str.=$SensorSetsStr;						
echo("Sent: "); echo($str);
                        socket_write($socket, $str, strlen($str));
			
                        break;
                     case 'NOTIFICATION':
echo"NOtification Resquest\n";
                     
                            echo "Read from database; ";  echo "\n";
                            $str = DBReadNotify($MsgParameters[2]); // Rcvs Email 
			socket_write($socket, $str, strlen($str));
                            DBupdateNotify($MsgParameters[2]);
                             break;
                }

            
            default:
                echo "Default\n";
                break;
        }
	
} 

/** 
  * Become a daemon by forking and closing the parent 
  */ 
function become_daemon() 
{ 
    $pid = pcntl_fork(); 
    
    if ($pid == -1) 
    { 
        /* fork failed */ 
        echo "fork failure!\n"; 
        exit(); 
    }elseif ($pid) 
    { 
        /* close the parent */ 
        exit(); 
    }else 
    { 
        /* child becomes our daemon */ 
        posix_setsid(); 
        chdir('/'); 
        umask(0); 
        return posix_getpid(); 

    } 
} 

?>



