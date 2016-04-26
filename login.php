<?php

  include "fns.php";
  include "connection.php";

  $params = ["email", "password"];
  paramsPresenceCheck($params);

  # Get user password, salt
  $stmt = $m->prepare("SELECT `password`, `salt`, `id` FROM `users` WHERE email = ?");
  $stmt->bind_param('s', $_POST['email']);
  $stmt->execute();
  $stmt->bind_result($password, $salt, $userId);
  $stmt->fetch();
  $stmt->free_result();

  # Verify password
  if ( $password != sha1(md5(substr($salt, 0, 4)).md5($_REQUEST['password']).md5(substr($salt, 4))) ) {
    failureResponse("Incorrect password provided");
  }

  # Instantiate new session
  session_start();
  session_regenerate_id(true);
  $_SESSION = array();
  $_SESSION['expires'] = time()+(60*30);
  $_SESSION['requests'] = 0;

  # Save session
  $stmt = $m->prepare("INSERT INTO `sessions` (`session_id`, `user_id`, `created_at`) VALUES (?, ?, CURRENT_TIMESTAMP)");
  $stmt->bind_param('si', session_id(), $userId);
  if ( !$stmt->execute() ) {
    failureResponse("Internal server error");
  }

  successResponse("Login successful", null);

?>