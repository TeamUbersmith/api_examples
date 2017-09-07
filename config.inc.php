<?php
// Ubersmith API Configuration
// This file should be included in other scripts, so you can reuse your credentials.

// Configuration
$config = [
	// Target Instance
	// If this script will be executed on the same server, you can use 'http://localhost'
	'domain' => 'https://your-ubersmith-domain.com',
	
	// Path to Ubersmith API Client
	'api_client_path' => __DIR__ .'/class.uber_api_client.php',
	
	// API Username
	'api_username' => 'API_USERNAME',
	
	// API Token
	'api_token' => 'API_TOKEN'
];

// end of script
