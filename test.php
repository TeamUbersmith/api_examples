<?php
// Ubersmith API Example
// Test Script
// This script tests your connection to the API endpoint.
// You can either execute this via the command line using PHP,
// or load it via your browser if it's in a publicly accessible directory.
// If you do view this in your browser, your best bet is to view source 
// in order to properly visualize whitespace and formatting.

// Display all errors
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

// Path to Ubersmith API Configuration file
require_once __DIR__ .'/config.inc.php';

// Configuration Extension
$config = array_merge($config, [
	// Parameters for test call
	'params' => [
		'limit'    => 1 // Maximum amount of results to return for test call
	],
]);

// Initialize Ubersmith API Client
require_once $config['api_client_path'];
$api_client = new uber_api_client($config['domain'], $config['api_username'], $config['api_token']);

print "\n";
try {
	// Retrieve list of tickets based on specified criteria
	$clients = $api_client->call('client.list',$config['params']);
	
	print "Everything looks good. Have fun with the Ubersmith API! You can find our documentation here:"."\n\n";
	print "https://docs.ubersmith.com/display/UbersmithDocumentation/Using+the+Ubersmith+API"."\n\n";
	print "You can also generate the documentation by using the uber.documentation method."."\n\n";
} catch (Exception $e) {
	print 'Error: '. $e->getMessage() .' (Code '. $e->getCode() .')'."\n\n";
	switch ($e->getCode()) {
		case 6:
			print "This error usually indicates that your Ubersmith instance domain is unreachable from the server that is running this script."."\n\n"."Please check that you've typed it correctly in the configuration section. If you're certain that you have the correct domain, please ensure that you can ping the domain from the server you're using:"."\n\n";
			print $config['domain'] ."\n\n";
			print "Please note that the configuration expects a full URL, including protocol, in the following format:"."\n\n";
			print "https://domain.com"."\n";
			break;
		case 22:
			print "This error usually indicates that the supplied username or token is invalid. Please ensure that you've specified the Ubersmith username (not token name), and that you have generated an API token for this user, both of which should match the credentials you've configured (shown below):"."\n\n";
			print "Username:"."\n";
			print $config['api_username'] ."\n\n";
			print "API Token:"."\n";
			print $config['api_token'] ."\n";
			break;
		case 28:
			print "This error usually indicates that either the cURL timeout is set too low, or the server receiving the call has its PHP execution time set too low."."\n\n";
			print "You can attempt to increase the cURL timeout value by adding this line after the API client is first defined:"."\n\n";
			print '$api_client->set_option(\'timeout\',60);'."\n";
			break;
		default:
			print "Please ensure that this server supports the usage of cURL, and check your configuration."."\n";
	}
}

// needs an exit() in catch() block above
//print 'Success: '. $count .' clients looped through.'."\n";

// end of script
