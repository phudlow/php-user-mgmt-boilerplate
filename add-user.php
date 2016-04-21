<?php

  include "fns.php";
  include "connection.php";

  $params = ["email", "password"];
  paramsPresenceCheck($params);

  # Check password length
  if ( strlen($_POST["password"]) < 8 ) {
    failureResponse("Password must be at least 8 characters");
  }

  # Check that email is available
  $stmt = $m->prepare("SELECT * FROM `users` where `email` = ?");
  $stmt->bind_param('s', $_POST['email']);
  $stmt->execute();
  $stmt->store_result();
  if ( $stmt->num_rows > 0 ) {
    failureResponse('Email unavailable');
  }
  $stmt->free_result();

  # Create password hash
  $salt = uniqid();
  $password = sha1(md5(substr($salt, 0, 4)).md5($_POST['password']).md5(substr($salt, 4)));

  # Save user
  $stmt = $m->prepare("INSERT INTO `users` (`email`, `password`, `salt`, `created_at`) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
  $stmt->bind_param('sss', $_POST['email'], $password, $salt);
  if ( !$stmt->execute() ) {
    failureResponse('Internal server error');
  }

  # Send success response
  successResponse("User added successfully", null);

?>