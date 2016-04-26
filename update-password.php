<?php

  include "fns.php";
  include "connection.php";

  $params = ["email", "old_password", "new_password"];
  paramsPresenceCheck($params);

  initSession();

  # Check new password length
  if ( strlen($_POST["new_password"]) < 8 ) {
    failureResponse("Password must be at least 8 characters");
  }

  # Get user password, salt
  $stmt = $m->prepare("SELECT `password`, `salt`, `id` FROM `users` WHERE email = ?");
  $stmt->bind_param('s', $_POST['email']);
  $stmt->execute();
  $stmt->bind_result($oldPassword, $salt, $userId);
  $stmt->fetch();
  $stmt->free_result();

  # Verify old password
  if ( $oldPassword != sha1(md5(substr($salt, 0, 4)).md5($_REQUEST['old_password']).md5(substr($salt, 4))) ) {
    failureResponse("Incorrect old password provided");
  }

  # Generate new password hash
  $salt = uniqid();
  $password = sha1(md5(substr($salt, 0, 4)).md5($_POST['new_password']).md5(substr($salt, 4)));

  # Save new password
  $stmt = $m->prepare("UPDATE `users` SET `password` = ?,  `salt` = ?, `updated_at` = CURRENT_TIMESTAMP WHERE `id` = ?");
  $stmt->bind_param('ssi', $password, $salt, $userId);
  if ( !$stmt->execute() ) {
    failureResponse("Internal server error");
  }

  successResponse("Password updated successfully", null);

?>