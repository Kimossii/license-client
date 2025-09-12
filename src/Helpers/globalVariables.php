<?php

//use App\Services\LicenseService;
use LicenseClient\Services\LicenseService;
use LicenseClient\Models\License;
use Illuminate\Support\Facades\File;

// --- Paths ---
if (!function_exists('getClientStoragePathKeyPublic')) {
    function getClientStoragePathKeyPublic()
    {
        return app(LicenseService::class)->getClientStoragePathKeyPublic();
    }
}

if (!function_exists('getClientStoragePathDat')) {
    function getClientStoragePathDat()
    {
        return app(LicenseService::class)->getClientStoragePathKeyDat();
    }
}

if (!function_exists('getStorageKeys')) {
    function getClientStorageKeys()
    {
        return storage_path('keys');
    }
}

// --- License helpers ---
if (!function_exists('getLicenseCodeFile')) {
    function getLicenseCodeFile()
    {
        $path = getClientStoragePathDat();

        if (file_exists($path) && filesize($path) > 0) {
            return file_get_contents($path);
        }

        return null;
    }
}

if (!function_exists('getLicenseCodeBd')) {
    function getLicenseCodeBd()
    {
        return License::latest()->first(); // pega o mais recente
    }
}

if (!function_exists('getLicenseCodedecrypted')) {
    function getLicenseCodedecrypted()
    {
        return licenseService()->getLicenseData();
    }
}

if (!function_exists('getStatusLicense')) {
    function getStatusLicense()
    {
        return licenseService()->verificationStatus();
    }
}

// --- Service instance ---
if (!function_exists('licenseService')) {
    function licenseService(): LicenseService
    {
        static $service;
        if (!$service) {
            $service = app(LicenseService::class);
        }
        return $service;
    }
}

// --- Log ---
if (!function_exists('write_Clientlicense_log')) {
    function write_Clientlicense_log(string $message): void
    {
        $logDir = storage_path('logs');
        $logPath = $logDir . '/ClientLicense.log';
        $timestamp = now()->toDateTimeString();

        if (!File::exists($logDir)) {
            File::makeDirectory($logDir, 0755, true);
        }

        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        File::append($logPath, $logMessage);
    }
}
