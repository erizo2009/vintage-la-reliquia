<?php

require_once 'vendor/autoload.php';

$client = new Google\Client();

$credentials = require __DIR__ . "/google_credentials.php";
if (!is_array($credentials) || empty($credentials["client_id"]) || empty($credentials["client_secret"]) || substr($credentials["client_id"], 0, 8) === "REPLACE_" || substr($credentials["client_secret"], 0, 8) === "REPLACE_") {
    throw new RuntimeException("Set your Google OAuth credentials in google_credentials.php.");
}
$client->setClientId($credentials["client_id"]);
$client->setClientSecret($credentials["client_secret"]);

$client->setRedirectUri("http://localhost/RELIQUIA/vintage-la-reliquia/inicio.php");

$client->addScope("email");
$client->addScope("profile");

?>