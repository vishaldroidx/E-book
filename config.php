<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Required environment variables
$required_vars = [
    'OPENAI_API_KEY',
    'GITHUB_TOKEN',
    'GITHUB_USERNAME',
    'GITHUB_REPO',
    'MAX_RETRIES',
    'RETRY_DELAY'
];

// Validate environment variables
foreach ($required_vars as $var) {
    if (!isset($_ENV[$var])) {
        throw new RuntimeException("Missing required environment variable: {$var}");
    }
}

// Configuration settings
define('OPENAI_API_KEY', $_ENV['OPENAI_API_KEY']);
define('GITHUB_TOKEN', $_ENV['GITHUB_TOKEN']);
define('GITHUB_USERNAME', $_ENV['GITHUB_USERNAME']);
define('GITHUB_REPO', $_ENV['GITHUB_REPO']);
define('MAX_RETRIES', (int)$_ENV['MAX_RETRIES']);
define('RETRY_DELAY', (int)$_ENV['RETRY_DELAY']);

// Novel settings
define('WORDS_PER_CHAPTER', 2000);
define('CHAPTER_DIRECTORY', 'chapters');

// Create log directory if it doesn't exist
define('LOG_DIRECTORY', __DIR__ . '/logs');
if (!file_exists(LOG_DIRECTORY)) {
    mkdir(LOG_DIRECTORY, 0777, true);
}

// Configure error logging
ini_set('log_errors', 1);
ini_set('error_log', LOG_DIRECTORY . '/error.log');
