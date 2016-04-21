<?php

  session_start();

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

  # Initiate new session;
  logout();
  session_start();
  echo json_encode($_SESSION);
  $_SESSION['expires'] = time()+(60*20);
  $_SESSION['requests'] = 1;

  # Save session
  $stmt = $m->prepare("INSERT INTO `sessions` (`session_id`, `user_id`, `created_at`) VALUES (?, ?, CURRENT_TIMESTAMP)");
  $stmt->bind_param('si', session_id(), $userId);
  if ( !$stmt->execute() ) {
    failureResponse($stmt->error);
  }

  # Send login success response
  successResponse("Login successful", null);

?>