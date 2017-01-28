<?php
  include "mysqlconnect.php";
  $ok=true;
  try {
	$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
  }
  catch (PDOException $e) {
	$ok = false;
  }
  if($ok)
  {
    $items = array(); 

    try {
        $sql = $dbh->prepare("select id, name from divisions;");
        $sql->execute();
    
        if ( $sql->columnCount() > 0 )
        {
          while($row=$sql->fetch())
          { 
            $items[] = array( $row[0], $row[1] ); 
          } 
          echo json_encode($items);
        } 
        else
          echo '{"error":"Database lookup failed"}';
    }
    catch (PDOException $e) {
	$ok = false;
	echo '{"error":"Database lookup failed"}';
    }
  }
  else
    echo '{"error":"Database connection failed"}';
?>