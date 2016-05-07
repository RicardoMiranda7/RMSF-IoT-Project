#!/usr/bin/php -q 
<?php 
define("IPServer", "192.168.0.103");
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
    }else 
    { 
        socket_close($csock); 
    } 
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

function AccessDatabase($mode, $packet_seq_nr=NULL, $packet_time=NULL, $packet_node_nr=NULL)
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
				$sql_insert = " INSERT INTO sensor_readings 
								VALUES ('$packet_seq_nr', '$packet_time', $packet_node_nr, false);";
				$result = $connection->query($sql_insert);
				if ($result == FALSE){
					$info = $connection->errorInfo();
					echo("Error: {$info[2]}\n");
					exit();
				}
				return 0;
				break;
	
			case "read":
				
				$sql_read = "SELECT * FROM sensor_readings WHERE tstamp IN (SELECT MAX(tstamp) FROM sensor_readings);";
				$read_result = $connection->query($sql_read);
				if ($read_result == FALSE){
					$info = $connection->errorInfo();
					echo("Error: {$info[2]}\n");
					exit();
				}
				$notified = $read_result->fetch(PDO::FETCH_ASSOC);
				
					echo("	NOTIFIED: ");	
					echo($notified['notified']); 
			
				return $notified;
				break;
				
			case "update":	
				$sql_update = "UPDATE sensor_readings 
								SET tstamp=tstamp, notified = true order by tstamp desc limit 1;";
				
				$update_result = $connection->query($sql_update);
				if ($update_result == FALSE){
					$info = $connection->errorInfo();
					echo("Error: {$info[2]}\n");
					exit();
				}
				return 1;
			

				/*
				echo "$result\n";
				
				if($result==$last_time_java){
					return 0; // App is up to date
				}else{
					return $result;  // App needs to be notified
				}*/
				break;

          
			}

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
        echo"3 Arg: ";echo$MsgParameters[2];echo"_";echo"\n";
        echo"4 Arg: ";echo$MsgParameters[3];echo"_";echo"\n";
        switch ($MsgParameters[0]) {
            case 'BASE':
                        //echo "BASE detected! ";
                    // Comunicar com base de dados
                    //AccessDatabase('insert',);
                //$last_time_base_sation;
                $packet_tstamp = new DateTime();
                list($protocol_msg, $tstamp, $packet_seqno) = sscanf($buf, "%s %d %d");
                $packet_tstamp->setTimestamp($tstamp);
                $i = AccessDatabase('insert', $packet_seqno, $packet_tstamp->format('Y-m-d H:i:s'), 1);
                echo "Sequence Nr: $packet_seqno    Time stamp: ";
                echo ($packet_tstamp->format('Y-m-d H:i:s')); // Se mandar o U como argumento vai o inteiro UNIX
                echo "  returned: $i\n";
                break;
            case 'JAVA':
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
                        # code...
                        break;
                    case 'RETRIEVE':
                        # code...
                        break;
                     case 'NOTIFICATION':
                            $notified = AccessDatabase('read');
                            echo "Read from database; "; echo($notified['notified']); echo "\n";
                            if(!$notified['notified']) {
                                //echo "Notified: $notified; ";
                                $str = sprintf("ALARM %d %d", time($notified['tstamp']), $notified['sequence_nr']); 
                                socket_write($socket, $str, strlen($str));
                                AccessDatabase('update');
                                echo "Updated;\n";
                            }else{
                                socket_write($socket, "UPTODATE", strlen("UPTODATE"));
                            }   
                             break;
                    default:
                        # code...
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



