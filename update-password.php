<?php

  include "fns.php";
  include "connection.php";

  initSession();
  session_start();


  $params = ["email", "password"];
  paramsPresenceCheck($params);

  beginSession();

?>