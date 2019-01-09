<?php
/**
 * This example allows a specific library's holdings to be checked for copy availability
 *
 * Authentication objects are managed by the OCLC PHP Authentication library, and restful calls are handled
 * with the popular Guzzle library. Composer is used to manage these dependencies - see the README markdown
 * file for details on how to install this example.
 *
 * We use the index.php page to present a form into which an OCLC Number can be input, or if an OCLC is present to call the views/show.php page to use our access token
 * to make restful calls against the WMS Availability API.
 *
 */

require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

use OCLC\Auth\WSKey;
use OCLC\Auth\AuthCode;
use OCLC\Auth\AccessToken;
use OCLC\User;
use Aws\Kms\KmsClient;

// The global configuration object.
// If you have not created a config.yaml file with your authentication parameters in it,
// copy app/config/sampleConfig.yaml to app/config/config.yaml and set the parameters.
global $config;

$KmsClient = new KmsClient([
		'profile' => 'default',
		'region' => 'us-east-1',
		'version' => '2014-11-01'
]);

try {
	$result = $KmsClient->decrypt([
			'CiphertextBlob' => file_get_contents("app/config/prod_config_encrypted.txt"),
	]);
	$plaintext = $result['Plaintext'];
	$config = Yaml::parse($plaintext);
} catch (AwsException $e) {
	// Output error message if fails
	echo $e->getMessage();
	echo "\n";
}

// We use sessions to persist our authentication between calls to the WMS Availability service.
session_start();

// Construct a new WSkey object using the key, secret and an options array that contains the services
// you want to access and your redirect_uri
$options = array('services' => array('WMS_Availability', 'refresh_token'));
$wskey = new WSKey($config['wskey'], $config['secret'], $options);

// if you don't have an Access Token already
if (empty($_SESSION['accessToken'])) {
    $accessToken = $wskey->getAccessTokenWithClientCredentials($config['institution'], $config['institution']);
    $_SESSION['accessToken'] = $accessToken;

} else {
    // Otherwise, get the existing access token from the session
    $accessToken = $_SESSION['accessToken'];
}

// Was there an error during the attempt to get or use an access token?
if (empty($accessToken->getValue())) {

    // Clear the bad token from the session memory and display the response with the error information
    unset($_SESSION['accessToken']);
    echo $accessToken->getResponse();

    // There is no token error, but is the token expired?
} elseif ($accessToken->isExpired()) {

    // Get a new valid token
    $accessToken = $wskey->getAccessTokenWithClientCredentials($config['institution'], $config['institution']);
    $_SESSION['accessToken'] = $accessToken;

} else {

    // No token error and the token is not expired, so move on to using the token to make requests against
    // the WMS Availability API
    if (empty($_GET['oclcNumber'])) {
        // No OCLC Number was specified as a parameter on the url, so show a form
        include 'app/views/searchAvailability.php';
    } else {
        // An OCLC number was specified on the url (ie, localhost/oclc-auth-test/?oclcNumber=)
        // so display the availability related to that OCLC Number.
        include 'app/views/showAvailability.php';
    }
}

?>