<?php

  $host = '';
  $user = '';
  $pass = '';
  $db = '';

  $m = new mysqli($host, $user, $pass, $db);

  if ( $m->connect_errno > 0) {
    $response['content'] = 'Database connection error.';
    echo json_encode($response);
    exit;
  }

?>