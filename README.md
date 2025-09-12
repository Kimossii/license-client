#License Manager for Laravel

#Pacotes Laravel para gerenciamento de licenças de software, divididos em License Server e License Client.

# 📦 Instalação
## composer require eluki/license-server
## composer require eluki/license-client

# Publicar os arquivos de configuração e migrations:
## License Server
php artisan vendor:publish --provider="LicenseServer\ServerServiceProvider"

## License Client
php artisan vendor:publish --provider="LicenseClient\ClientServiceProvider"

## Migrations do servidor
php artisan migrate --path=database/migrations/vendor/license-server
## Migrations do cliente
php artisan migrate

# 1. Registrar middleware
colocar a middlewre na pasta bosstrpa ou kernl: app
 $middleware->alias([
            'license.check' => \LicenseClient\Http\Middleware\LicenseCheck::class,
        ]);

# Usar middleware nas rotas do cliente

Route::middleware('license.check')->group(function () {
    Route::get('/activate', [LicenseController::class, 'activateForm'])
        ->name('license.activate.form');

    Route::post('/activate', [LicenseController::class, 'activate'])
        ->name('license.activate');

    Route::get('/request-code', [LicenseController::class, 'requestCode'])
        ->name('license.request');

    Route::get('/import/upload-key', [LicenseController::class, 'formKeyPublic'])
        ->name('import.uploadKey');

    Route::post('/uploadkey', [LicenseController::class, 'uploadKey'])
        ->name('client.uploadKey');
});

# License Server
use Illuminate\Support\Facades\Route;
use LicenseServer\Http\Controllers\LicenseServerController;

Route::prefix('license-server')->group(function () {
    Route::get('/', [LicenseServerController::class, 'createForm'])->name('server.create');
    Route::get('/generate', [LicenseServerController::class, 'generateKeyForm'])->name('generate.keys');
    Route::get('/download-key/{tipo}', [LicenseServerController::class, 'downloadKey'])->name('keys.download');

    Route::post('/license/store', [LicenseServerController::class, 'store'])->name('server.store');
    Route::post('/generate-keys', [LicenseServerController::class, 'generateKeys'])->name('keys.generate');
});


# license-server/
├── src/
├── config/
├── database/
├── composer.json
└── LICENSE

# license-client/
├── src/
├── config/
├── database/
├── composer.json
└── LICENSE
