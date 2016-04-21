<?php

  $response = array();
  $response['status'] = 'FAILURE';

  # Send "FAILURE" response with message
  function failureResponse($message) {
    $response['message'] = $message;
    echo json_encode($response);
    exit;
  }

  # Send "SUCCESS" response with message and data
  function successResponse($message, $content) {
    $response['status'] = "SUCCESS";
    $response['message'] = $message;
    if ( isset($content) ) {
      $response['content'] = $content;
    }
    echo json_encode($response);
    exit;
  }

  # Check that request params are present
  function paramsPresenceCheck($params) {
    foreach ( $params as $key => $value ) {
      if ( !isset($_REQUEST[$value]) || $_REQUEST[$value] === '' ) {
        failureResponse("No ".$value." sent");
      }
    }
  }

  # Unset all Session variables and kill the Cookie
  function logout() {
    session_start();
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
          $params["path"], $params["domain"],
          $params["secure"], $params["httponly"]
      );
    }
    $_SESSION = array();
    session_destroy();
  }
  
  # Check session expiry, and regen password every 5th access
  function initSession() {
    session_start();
    if ( time() < $_SESSION['expires'] ) {
      // $_SESSION['expires'] = time()+(60*20);
      $_SESSION['expires'] = time()+10;
      $_SESSION['requests'] = $_SESSION['requests'] + 1;
      if ( $_SESSION['requests'] == 5 ) {
        session_regenerate_id();
      }
    } else {
      logout();
      failureResponse('Login required');
    }
  }



?>