<?php
session_start();
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->addScope(Google_Service_Drive::DRIVE_READONLY);
$client->setRedirectUri('http://localhost/site/google_auth.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);
    
    // Store the token in a session for future use
    $_SESSION['access_token'] = $token;
    
    // Redirect to the main page or document list page
    header('Location: list_documents.php');
    exit;
} elseif (empty($_SESSION['access_token'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit;
}

$client->setAccessToken($_SESSION['access_token']);