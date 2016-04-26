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
  
  # Check session expiry, and regen id every 5th access
  function initSession() {
    session_start();
    if ( isset($_SESSION['expires']) && time() < $_SESSION['expires'] ) {
      $_SESSION['expires'] = time()+(60*20);
      $_SESSION['requests'] = $_SESSION['requests'] + 1;
      if ( $_SESSION['requests'] == 5 ) {
        session_regenerate_id(false);
      }
    } else {
      include "logout.php";
      failureResponse('Login required');
    }
  }

?>