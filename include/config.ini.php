<?php
if($configPassword != "dNh9h{E(\Qm5tB6>"){
  die('wrong wrong wrong');
}
include('include/function.php');
//Database connect
try {
  /**************************************
  * Create databases and                *
  * open connections                    *
  **************************************/

  // Create (connect to) SQLite database in file
  $file_db = new PDO('sqlite:data/database.sqlite3');
  // Set errormode to exceptions
  $file_db->setAttribute(PDO::ATTR_ERRMODE,
                          PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
  // Print PDOException message
  echo "failed : ".$e->getMessage();
}
//lang management
include('include/lang.php');
//Updates or install
include('include/install.php');
//session management
include('include/session.php');
//plugins management
//include('include/plugins.php');

?>
