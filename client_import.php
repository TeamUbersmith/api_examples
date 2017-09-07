<?php
// Ubersmith API Example
// Client Import
// This script demonstrates how to perform the initial import of clients into Ubersmith.

// Display all errors
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

// Path to Ubersmith API Configuration file
require_once __DIR__ .'/config.inc.php';

// Configuration Extension
$config = array_merge($config, [
	// Parameters for API call
	'params' => [
		// Salesperson Admin ID (should be created before running this script)
		'salesperson_id' => 0,
		
		// Salesperson Relationship ID (should be verified before running this script)
		'salesperson_relationship_id' => 1,
		
		// Field Mapping
		// (Each field corresponds to a column in the spreadsheet)
		'mapping' => [
			// Basic Client Info
			'company'            => 0,
			'first'              => 1,
			'last'               => 2,
			'phone'              => 3,
			'email'              => 4,
			'address'            => 9,
			'city'               => 10,
			'state'              => 11,
			'zip'                => 12,
			
			// Contact Info
			'tech_contact_name'  => 5,
			'tech_contact_email' => 6,
			'misc_contact_name'  => 7,
			'misc_contact_email' => 8,
			
			// Custom Data field named "thing1" should be created before running this script
			'customdata_thing1'  => 13,
			
			// Custom Data field named "thing2" should be created before running this script
			'customdata_thing2'  => 14,
		],
	],
]);

// Initialize Ubersmith API Client
require_once $config['api_client_path'];
$api_client = new uber_api_client($config['domain'], $config['api_username'], $config['api_token']);

// Get CSV contents (don't forget to set its newline format to Unix LF!)
$csv = array_map('str_getcsv', file('customer_list.csv'));

print "\n";

// Begin creating clients
try {
	$count = 0;
	
	foreach ($csv as $row) {
		// Prepare Client data
		$data = [
			'company'     => '',
			'first'       => '',
			'last'        => '',
			'phone'       => '',
			'email'       => '',
			'address'     => '',
			'city'        => '',
			'state'       => '',
			'zip'         => '',
		];
		foreach ($data as $key => $value) {
			// Fill data (default to empty string if unavailable)
			$data[$key] = empty($row[$config['params']['mapping'][$key]]) ? '' : $row[$config['params']['mapping'][$key]];
		}
		// Add Custom Data
		if (!empty($row[$config['params']['mapping']['customdata_thing1']])) {
			$data['meta_thing1'] = $row[$config['params']['mapping']['customdata_thing1']];
		}
		if (!empty($row[$config['params']['mapping']['customdata_thing2']])) {
			$data['meta_thing2'] = $row[$config['params']['mapping']['customdata_thing2']];
		}
		// Add Client
		$client_id = $api_client->call('client.add', $data);
		if (empty($client_id) || is_array($client_id)) {
			print 'Error while adding Client ['. $row[$config['params']['mapping']['email']] .']: ';
			var_dump($client_id);
			continue;
		}
		$count++;
		
		// Add Contact(s)
		$contacts = [
			[
				'real_name'   => $row[$config['params']['mapping']['tech_contact_name']],
				'email'       => $row[$config['params']['mapping']['tech_contact_email']],
				'description' => 'Technical Contact',
			],
			[
				'real_name'   => $row[$config['params']['mapping']['misc_contact_name']],
				'email'       => $row[$config['params']['mapping']['misc_contact_email']],
				'description' => 'Miscellaneous Contact',
			],
		];
		foreach ($contacts as $contact) {
			// Skip contacts that lack both a name and email address
			if (empty($contact['real_name']) && empty($contact['email'])) {
				continue;
			}
			
			// Prepare Contact data
			$data = $contact;
			$data['client_id'] = $client_id;
			// Add Contact
			$result = $api_client->call('client.contact_add', $data);
			if (empty($result) || is_array($result)) {
				print 'Error while adding '. $data['description'] .' for client ['. $row[7] .']: ';
				var_dump($result);
				continue;
			}
		}
		
		if (!empty($config['params']['salesperson_id'])) {
			// Prepare Salesperson data
			$data = [
				'client_id'             => $client_id,
				'person_id'             => $config['params']['salesperson_id'], // Salesperson ID
				'people_client_type_id' => $config['params']['salesperson_relationship_id'], // Salesperson Relationship
			];
			// Add Salesperson
			$result = $api_client->call('uber.admin_client_relationship_add', $data);
			if (empty($result) || is_array($result)) {
				print 'Error while adding Salesperson for Client ['. $row[$config['params']['mapping']['email']] .']: ';
				var_dump($result);
				continue;
			}
		}
	}
} catch (Exception $e) {
	print 'Error: '. $e->getMessage() .' (Code '. $e->getCode() .')'."\n\n";
	print $count .' clients imported.'."\n";
	
	// Early exit so success message below doesn't get printed
	exit;
}

print 'Success: '. $count .' clients imported.'."\n";

// end of script
