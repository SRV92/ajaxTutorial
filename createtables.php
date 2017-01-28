<?php
  include "mysqlconnect.php";
  
  $tables[0] = "divisions";
  $tables[1] = "teams";
  $tables[2] = "fixtures";

  function createDivision ( $division )
  {
      global $dbh;

      try {
          $sql = $dbh->prepare("INSERT INTO divisions ( name ) VALUES ( :division );");
          $sql->bindParam( ':division', $division, PDO::PARAM_STR );
          $sql->execute();
      }
      catch (PDOException $e) {
	  $ok = false;
	  echo("Database divisions table insertion failed.<br><br>");
      }
  }//createDivision

  function createDivisions()
  {
    createDivision ( 'Premiership' );
    createDivision ( 'Championship' );
    createDivision ( 'League One' );
    createDivision ( 'League Two' );
  }//createDivisions

  function createTeam ( $divid, $teamName )
  {
    
    global $dbh;

    try {
      $sql = $dbh->prepare("INSERT INTO teams ( divid, name, won, drawn, lost, goalsfor, goalsagainst, points ) VALUES ( :divid, :teamName,0,0,0,0,0,0 );");
      $sql->bindParam( ':divid', $divid );
      $sql->bindParam( ':teamName', $teamName, PDO::PARAM_STR );         
      $sql->execute();
    }
    catch (PDOException $e) {
	  $ok = false;
	  echo("Database teams table insertion failed.<br><br>");
    }
    
  }//createTeam

  function createPremiershipTeams()
  {

    global $dbh;
    global $ok;
	 
    try {
      $sql = $dbh->prepare('select id from divisions where name = "Premiership";');
      $sql->execute();
      $fields=$sql->fetch();
      $id = $fields[0];
    }
    catch (PDOException $e) {
	  $ok = false;
	  echo("Database teams table insertion failed, unable to retrieve Premiership divisions' id entry.<br><br>");
    }

    if ( $ok )
    {
        createTeam ( $id, "Aston Villa" );
        createTeam ( $id, "Arsenal" );
        createTeam ( $id, "Bournemouth Town" );
        createTeam ( $id, "Chelsea" );
        createTeam ( $id, "Crystal Palace" );
        createTeam ( $id, "Everton" );
        createTeam ( $id, "Leicester City" );
        createTeam ( $id, "Liverpool" );
        createTeam ( $id, "Manchester City" );
        createTeam ( $id, "Manchester United" );
        createTeam ( $id, "Newcastle United" );
        createTeam ( $id, "Norwich City" );
        createTeam ( $id, "Southampton" );
        createTeam ( $id, "Stoke City" );
        createTeam ( $id, "Sunderland" );
        createTeam ( $id, "Swansea City" );
        createTeam ( $id, "Tottenham Hotspur" );
        createTeam ( $id, "Watford Town" );
        createTeam ( $id, "West Bromwich Albion" );
        createTeam ( $id, "West Ham United" );
    }
	 
  }//createPremiershipTeams

  function createChampionshipTeams()
  {
    global $dbh;
    global $ok;
	 
    try {
      $sql = $dbh->prepare("select id from divisions where name = 'Championship';");
      $sql->execute();
      $fields=$sql->fetch();
      $id = $fields[0];
    }
    catch (PDOException $e) {
	  $ok = false;
	  echo("Database teams table insertion failed, unable to retrieve Championship divisions' id entry.<br><br>");
    }

    if ( $ok )
    {
        createTeam ( $id, "Birmingham City" );
        createTeam ( $id, "Blackburn Rovers" );
        createTeam ( $id, "Bolton Wanderers" );
        createTeam ( $id, "Brentford" );
        createTeam ( $id, "Brighton and Hove Albion" );
        createTeam ( $id, "Bristol City" );
        createTeam ( $id, "Burnley" );
        createTeam ( $id, "Cardiff City" );
        createTeam ( $id, "Charlton Athletic" );
        createTeam ( $id, "Derby County" );
        createTeam ( $id, "Fulham" );
        createTeam ( $id, "Huddersfield Town" );
        createTeam ( $id, "Hull City" );
        createTeam ( $id, "Ipswich Town" );
        createTeam ( $id, "Leeds United" );
        createTeam ( $id, "Middlesbrough" );
        createTeam ( $id, "Milton Keynes Dons" );
        createTeam ( $id, "Nottingham Forest" );
        createTeam ( $id, "Preston North End" );
        createTeam ( $id, "QPR" );
        createTeam ( $id, "Reading" );
        createTeam ( $id, "Rotherham United" );
        createTeam ( $id, "Sheffield Wednesday" );
        createTeam ( $id, "Wolverhampton Wanderers" );
    }
  }//createChampionshipTeams
   

  function createFixtures($division)
  {
     global $dbh;
     global $ok;

     try {	 

         $teamids = array();

         $sql1 = $dbh->prepare("select id from teams where teams.divid in (select id from divisions where name = :division);");
         $sql1->bindParam( ':division', $division, PDO::PARAM_STR );
         $sql1->execute();

         while($fields=$sql1->fetch())
         {
            $teamids[] = $fields[0];
         }

         for ( $home = 0; $home < count($teamids); $home++ )
         {
           for ( $away = 0; $away < count($teamids); $away++ )
           {

             if ( $home != $away )
	     {
	       $sql3 = $dbh->prepare("INSERT INTO fixtures ( home, away, homegoals, awaygoals, date ) VALUES ( :homeid, :awayid, 0, 0, null );");
               $sql3->bindParam( ':homeid', $teamids[$home] );
               $sql3->bindParam( ':awayid', $teamids[$away] );
               $sql3->execute();
	     }
           }
         }
     }
     catch (PDOException $e) {
	$ok = false;
	echo("Database fixture table insertion failed, please try again later.<br><br>");
     }

  }//createFixtures

  $ok=true;
  try {
	$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
  }
  catch (PDOException $e) {
	$ok = false;
	echo("Database connection failed, please try again later.<br><br>");
  }
  if($ok)
  {
      try {
          $i = 0;
          while($i < 3 )
          {
	      $sql = $dbh->prepare("drop table if exists ".$tables[$i].";");
              $sql->execute();
              $i++;
          }



          $sql = $dbh->prepare("CREATE TABLE divisions ( id INT UNSIGNED NOT NULL AUTO_INCREMENT, name VARCHAR( 20 ) NOT NULL , PRIMARY KEY ( id ) );");
          $sql->execute();
          createDivisions();

          $sql = $dbh->prepare("CREATE TABLE teams ( id INT UNSIGNED NOT NULL AUTO_INCREMENT, divid INT UNSIGNED NOT NULL, name VARCHAR( 30 ) NOT NULL , won INT UNSIGNED NOT NULL, drawn INT UNSIGNED NOT NULL, lost INT UNSIGNED NOT NULL , goalsfor INT UNSIGNED NOT NULL, goalsagainst INT UNSIGNED NOT NULL, points INT UNSIGNED NOT NULL, PRIMARY KEY ( id ) );");
          $sql->execute();
          createPremiershipTeams();
          createChampionshipTeams();
            
          $sql = $dbh->prepare("CREATE TABLE fixtures ( id INT UNSIGNED NOT NULL AUTO_INCREMENT, home INT UNSIGNED NOT NULL , away INT UNSIGNED NOT NULL ,homegoals INT UNSIGNED NOT NULL, awaygoals INT UNSIGNED NOT NULL, date DATETIME, PRIMARY KEY ( id ) );");
          $sql->execute();
        
          createFixtures("Premiership");
          createFixtures("ChampionShip");

      
          echo "Done";
     }
     catch (PDOException $e) {
	  $ok = false;
	  echo("Database table creation & insertion failed, please try again later.<br><br>");
     }
  }
  
?>