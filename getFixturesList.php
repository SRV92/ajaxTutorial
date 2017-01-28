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
      $homeid = $_GET["id"];

      try {
          $sql = $dbh->prepare("select name from teams where id=:homeid;");
          $sql->bindParam( ':homeid', $homeid );
          $sql->execute();
          if ( $sql->columnCount() > 0 )
          {
              $fields = $sql->fetch();
              $homename = $fields[0];

              $sql = $dbh->prepare("select id, away from fixtures where home=:homeid;");
              $sql->bindParam( ':homeid', $homeid );
              $sql->execute();
              if ( $sql->columnCount() > 0 )
              {
                  $items = array();
                  while($fields=$sql->fetch())
                  { 
                    $awayid = $fields[1];
                    $sql2 = $dbh->prepare("select name from teams where id=:awayid;");
                    $sql2->bindParam( ':awayid', $awayid );
                    $sql2->execute();
                    $awayfields=$sql2->fetch();
                    $awayname = $awayfields[0];
                    $items[] = array( $fields[0], $homename." vs ".$awayname ); 
                  } 
                  echo json_encode($items);

              }
              else
                  echo '{"error":"Database lookup failed"}';

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
