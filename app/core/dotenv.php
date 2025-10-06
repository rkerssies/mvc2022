<?php

	/**
	 * Project: MVC2025.
	 * Author:  InCubics
	 * Date:    01/10/2025
	 * File:    dotenv.php
	 */


function loadEnv(string|null $file = null): object 
{
   
    if ($file === null) {
        $file = __DIR__ . '/../../.env';  // pad naar root-project
    }

    if (!file_exists($file)) {
        throw new Exception(".env file not found at $file");
    }

    $env = [];
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, ';') === 0) continue; // skip comments

        $line = preg_split('/\s*[#;].*$/', $line)[0];   // remove inline comments

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        $env[$key] = $value;
    }   

    if(count($env) == 0)
    {
        throw new Exception(" The .env is empty or invalid");
    }
    $_ENV = $env;   // populate global $_ENV array with env values

    // set default timezone if APP_TIMEZONE exists
    if (isset($env['APP_TIMEZONE'])) {
        date_default_timezone_set($env['APP_TIMEZONE']);
    }

    // ----------------------
    // Rubriceren en constants definiëren
    // ----------------------
    $parts = [];

    foreach ($env as $key => $value) {
        if (strpos($key, '_') === false) {
            // geen underscore → fallback global
            $parts['global'][strtolower($key)] = $value;
            // continue;
        }

        [$prefix, $subkey] = explode('_', $key, 2);
        $prefix = strtolower($prefix);
        $subkey = strtolower($subkey);

        $parts[$prefix][$subkey] = $value;
    }

    // define constants per prefix
    foreach ($parts as $prefix => $config) {
        $constName = strtoupper($prefix);
        if (!defined($constName)) {
            // Ensure APP_URL and APP_DOMAIN include a scheme (default to http:// if missing)
            if ($prefix === 'app') {
                if (!empty($config['url'])) {
                    $config['url'] = normalize_scheme($config['url']);
                }
                if (!empty($config['domain'])) {
                    $config['domain'] = normalize_scheme($config['domain']);
                }
            }

            define($constName, $config); // array constant
        }
        // unset($parts[$prefix]);
    }
    
    return (object) $parts;
}



////////////////////

function env(string $key = ''): object|null{
    $key = strtolower($key);

    static $env = null;
    if ($env === null) {
        $env = loadEnv();
    }
    
    if(empty($key)) {
        return $env;
    }

    return (object) $env->$key ?? null;
}


/**
 * Ensure a URL or domain contains a scheme. If it already has http(s)://, return as-is.
 * If it looks like just a domain (no scheme), prepend http:// by default.
 */
function normalize_scheme(string $url): string {
    $trimmed = trim($url);
    if (preg_match('#^https?://#i', $trimmed)) {
        return $trimmed;
    }

    // If it's empty or starts with a slash, return as-is
    if ($trimmed === '' || strpos($trimmed, '/') === 0) {
        return $trimmed;
    }

    // Default to http://
    return 'http://' . $trimmed;
}