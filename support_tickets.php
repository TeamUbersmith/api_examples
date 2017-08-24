<?php
// Ubersmith API Example
// Support Tickets
// This script demonstrates how to mass modify support tickets.

// Display all errors
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

// Config
$config = [
	// Target Instance
	// If this script will be executed on the same server, you can use 'http://localhost'
	'domain' => 'https://your-ubersmith-domain.com',
	
	// Path to Ubersmith API Client
	'api_client_path' => __DIR__ .'/class.uber_api_client.php',
	
	// API Username
	'api_username' => 'API_USERNAME',
	
	// API Token
	'api_token' => 'API_TOKEN',
	
	// Criteria for targeting tickets
	'from' => [
		'queue'    => 1, // Department (Queue) ID
		'type'     => 'On Hold', // Type
		'priority' => 2, // Priority (2 = High)
		'limit'    => 500 // Maximum amount of tickets to find
	],
	
	// Details to modify in targeted tickets
	'to' => [
		'queue'      => 2, // Department (Queue) ID
		'type'       => 'Open', // Type
		'priority'   => 1, // Priority (1 = Normal)
		'impact'     => 2, // Impact (1 = Moderate/Limited)
		'assignment' => 5, // Assigned Admin ID
	],
];

// Initialize Ubersmith API Client
require_once $config['api_client_path'];
$api_client = new uber_api_client($config['domain'], $config['api_username'], $config['api_token']);

try {
	// Retrieve list of tickets based on specified criteria
	$tickets = $api_client->call('support.ticket_list', $config['from']);
	if (empty($tickets)) {
		throw new Exception('No tickets found for the specified criteria.');
	}
	
	// Initialize counter and begin new line for status output
	$count = 0;
	print "\n";
	
	// Loop through ticket list and apply modifications
	foreach ($tickets as $ticket_id => $ticket) {
		// Set the targeted Ticket ID
		$config['to']['ticket_id'] = $ticket_id;
		
		$api_client->call('support.ticket_update', $config['to']);
		print 'Modifying ticket #'. $ticket_id ."\n";
		$count++;
	}
} catch (Exception $e) {
	print 'Error: '. $e->getMessage() .' ('. $e->getCode() .')'."\n";
	print $count .' tickets modified.'."\n";
}

print 'Success: '. $count .' tickets modified.'."\n";

// end of script
