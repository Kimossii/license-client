# 🛡️ License Client for Laravel

> **Valide, proteja e gerencie licenças de software em sua aplicação Laravel com facilidade e segurança.**

Pacote Laravel para validação e uso de licenças de software emitidas pelo **License Server**.  
O License Client garante que apenas clientes autorizados possam acessar funcionalidades protegidas, validando licenças, protegendo rotas e detectando adulterações.

---

## 📦 Instalação

**Via Composer:**

```bash
composer require eluki/license-client
```

> **Atenção:**  
> É necessário instalar também o License Server para emitir e gerenciar as licenças, utilizando a mesma `APP_KEY` e a chave pública gerada no servidor (Gerador):

```bash
composer require eluki/license-server
```

---

## 🔧 Publicação & Migrations

**Publicar arquivos de configuração e migrations:**

```bash
php artisan vendor:publish --provider="LicenseClient\ClientServiceProvider"
```

**Executar migrations do cliente:**

```bash
php artisan migrate
```

Se já executou as migrations do License Server:

```bash
php artisan migrate --path=database/migrations/vendor/license-server
```

---

## 🛠 Registrar Middleware

Adicione o middleware no Kernel da aplicação (`app/Http/Kernel.php`) ou no Bootstrap/App para versões recentes do Laravel:

```php
protected $routeMiddleware = [
    'license.check' => \LicenseClient\Http\Middleware\LicenseCheck::class,
];
```

---

## 🔐 Protegendo Rotas

Inclua as rotas que precisam de validação de licença dentro do middleware:

```php
Route::middleware('license.check')->group(function () {
    Route::get('/activate', [LicenseController::class, 'activateForm'])->name('license.activate.form');
    Route::post('/activate', [LicenseController::class, 'activate'])->name('license.activate');
    Route::get('/request-code', [LicenseController::class, 'requestCode'])->name('license.request');
    Route::get('/import/upload-key', [LicenseController::class, 'formKeyPublic'])->name('import.uploadKey');
    Route::post('/uploadkey', [LicenseController::class, 'uploadKey'])->name('client.uploadKey');
});
```

> 💡 **Dica:**  
> Coloque apenas as rotas que precisam de validação de licença dentro do middleware.

---

## ⚡ Funcionalidades

- **Validação segura** de licenças emitidas pelo License Server
- **Middleware** para proteção de rotas sensíveis
- **Upload/importação** de chave pública para validação offline
- **Logs** de tentativas de uso de licenças inválidas
- **Integração transparente** com o License Server

---

## 🗂 Estrutura do Projeto

```
license-client/
├── src/
│   ├── Helpers/
│   │   ├── globalVariables.php
│   │   ├── HardwareHelper.php
│   │   └── LicenseHelper.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── LicenseController.php
│   │   ├── Middleware/
│   │   │   ├── LicenseCheck.php
│   │   │   └── CheckLicense.php
│   │   └── ClientServiceProvider.php
│   ├── Models/
│   │   └── License.php
│   └── Services/
│       └── LicenseService.php
├── config/
│   └── license.php
├── database/
│   └── migrations
├── resources/
│   └── views
├── routes/
│   └── web.php
├── composer.json
└── README.md
```

---

## 🔐 Boas Práticas de Segurança

- Proteja rotas críticas com o middleware `license.check`
- Mantenha a chave pública segura e não a exponha
- Utilize logs para monitorar tentativas de uso de licenças inválidas
- Combine com autenticação Laravel para máxima segurança

---


---

## 📫 Contato

- **Email:** eluckimossi@gmail.com  
- **LinkedIn:** [eluki-baptista](https://www.linkedin.com/in/eluki-baptista/)  
- **GitHub:** [Kimossii](https://github.com/Kimossii)


# NOTA
## Fique à vontade para usar cada pacote com seu app separado e não se esqueça das chaves

- Utilize cada pacote (License Client e License Server) conforme a necessidade do seu projeto.
- Lembre-se de manter as chaves (APP_KEY e chave pública) seguras e consistentes entre os ambientes.







