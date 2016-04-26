<?php

  include "fns.php";
  include "connection.php";

  $params = ["email", "password"];
  paramsPresenceCheck($params);

  initSession();

  # Get user password, salt
  $stmt = $m->prepare("SELECT `password`, `salt`, `id` FROM `users` WHERE email = ?");
  $stmt->bind_param('s', $_POST['email']);
  $stmt->execute();
  $stmt->bind_result($password, $salt, $userId);
  $stmt->fetch();
  $stmt->free_result();

  # Verify password
  if ( $password != sha1(md5(substr($salt, 0, 4)).md5($_REQUEST['password']).md5(substr($salt, 4))) ) {
    failureResponse("Incorrect old password provided");
  }

  # Delete user
  $stmt = $m->prepare("DELETE FROM `users` WHERE `id` = ?");
  $stmt->bind_param('s', $userId);
  if ( !$stmt->execute() ) {
    failureResponse("Internal server error");
  }

  include "logout.php";
  successResponse("User deleted");

?>