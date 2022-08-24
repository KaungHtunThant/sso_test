<?php
$content = "grant_type=authorization_code";
  $content .= "&client_id=" . $client_id;
  $content .= "&redirect_uri=" . urlencode($redirect_uri);
  $content .= "&code=" . $_GET["code"];
  $content .= "&client_secret=" . urlencode($client_secret);
  $options = array(
    "http" => array(  //Use "http" even if you send the request with https
      "method"  => "POST",
      "header"  => "Content-Type: application/x-www-form-urlencoded\r\n" .
        "Content-Length: " . strlen($content) . "\r\n",
      "content" => $content
    )
  );
  $context  = stream_context_create($options);
  $json = file_get_contents("https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/token", false, $context);
  if ($json === false) errorhandler(array("Description" => "Error received during Bearer token fetch.", "PHP_Error" => error_get_last(), "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);
  $authdata = json_decode($json, true);
  if (isset($authdata["error"])) errorhandler(array("Description" => "Bearer token fetch contained an error.", "\$authdata[]" => $authdata, "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);

  var_dump($authdata);  //Debug print
  $_SESSION['authdata'] = $authdata;

  //Fetching the basic user information that is likely needed by your application
  $options = array(
    "http" => array(  //Use "http" even if you send the request with https
      "method" => "GET",
      "header" => "Accept: application/json\r\n" .
        "Authorization: Bearer " . $authdata["access_token"] . "\r\n"
    )
  );
  $context = stream_context_create($options);
  $json = file_get_contents("https://graph.microsoft.com/v1.0/me", false, $context);
  if ($json === false) errorhandler(array("Description" => "Error received during user data fetch.", "PHP_Error" => error_get_last(), "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);
  $userdata = json_decode($json, true);  //This should now contain your logged on user information
  if (isset($userdata["error"])) errorhandler(array("Description" => "User data fetch contained an error.", "\$userdata[]" => $userdata, "\$authdata[]" => $authdata, "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);

  var_dump($userdata); //Debug print
  $_SESSION['userdata'] = $userdata;
  // echo "<script>console.log($userdata)</script>";
  // exit;

} else {
  //If we end up here, something has obviously gone wrong... Likely a hacking attempt since sent and returned state aren't matching and no $_GET["error"] received.
  echo "Hey, please don't try to hack us!\n\n";
  echo "PHP Session ID used as state: " . session_id() . "\n";  //And for production version you likely don't want to show these for the potential hacker
  var_dump($_GET);  //But this being a test script having the var_dumps might be useful
  errorhandler(array("Description" => "Likely a hacking attempt, due state mismatch.", "\$_GET[]" => $_GET, "\$_SESSION[]" => $_SESSION), $error_email);}
?>
