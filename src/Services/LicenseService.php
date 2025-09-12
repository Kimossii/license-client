<?php
//namespace App\Services;
namespace LicenseClient\Services;
use Carbon\Carbon;
//use App\Helpers\LicenseHelper;
use LicenseClient\Helpers\LicenseHelper;

use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
//use App\Models\License;
use LicenseClient\Models\License;
require_once base_path('vendor/eluki/license-client/src/Helpers/globalVariables.php');
require_once base_path('vendor/eluki/license-client/src/Helpers/HardwareHelper.php');
require_once base_path('vendor/eluki/license-client/src/Helpers/LicenseHelper.php');

class LicenseService
{
    public function uploadPublicKey(UploadedFile $file, bool $overwrite = false): array
    {
        $path = $this->getStorageKeys(); // ou crie getStorageKeys() no serviço

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        } elseif (File::exists($path . '/public.pem') && !$overwrite) {
            //write_Clientlicense_log("Chave pública já existe. Marque para substituir.");
            write_Clientlicense_log("Chave pública já existe. Marque para substituir.");
            return ['success' => false, 'message' => 'Chave pública já existe. Marque para substituir.'];
        }

        $file->move($path, 'public.pem');
        // write_Clientlicense_log("Chave pública enviada com sucesso!");
        write_Clientlicense_log("Chave pública enviada com sucesso");
        return ['success' => true, 'message' => 'Chave pública enviada com sucesso!'];
    }



    public function getStorageKeys(): string
    {
        return storage_path('keys');
    }

    private function extraSecret()
    {
        return LicenseHelper::getSegredoExtra();
    }
    public function getClientStoragePathKeyPublic()
    {
        return config('license.client.storage_path_key_public');
    }
    public function getClientStoragePathKeyDat()
    {
        return config('license.client.storage_path_dat');
    }

    private function getClientStoragePathDat()
    {
        return config('license.client.storage_path_dat');
    }
    private function getLicenseCodeBd()
    {
        return License::latest()->first();
    }
    //Verificação de licença existente no file
    private function getLicenseCodeFile()
    {
        $path = $this->getClientStoragePathDat();
        if (file_exists($path) && filesize($path) > 0) {
            return file_get_contents($path);
        }
        return null;
    }
    // Resolve a licença (arquivo ou banco)
    private function getLicenseCode()
    {
        $filePath = $this->getClientStoragePathDat();

        if (file_exists($filePath)) {
            $licenseFromFile = getLicenseCodeFile();
            if (!empty($licenseFromFile)) {
                return $licenseFromFile;
            }
            //write_Clientlicense_log("Arquivo de licença existe mas está vazio ou inválido: $filePath");
            \Log::warning("Arquivo de licença existe mas está vazio ou inválido: $filePath");
            write_Clientlicense_log("Arquivo de licença existe mas está vazio ou inválido: $filePath");
        }

        $latestLicense = $this->getLicenseCodeBd();
        if ($latestLicense && !empty($latestLicense->license_code)) {
            return $latestLicense->license_code;
        }
        //write_Clientlicense_log("Nenhum código de licença encontrado (arquivo ou banco)");
        \Log::error("Nenhum código de licença encontrado (arquivo ou banco)");
        write_Clientlicense_log("Nenhum código de licença encontrado (arquivo ou banco)");

        return false;
    }


    // Retorna os dados descriptografados
    public function getLicenseData(): array
    {
        $licenseCode = $this->getLicenseCode();

        if (!$licenseCode) {
            write_Clientlicense_log("Licença ausente ou não encontrada, por favor active a licença ou contacte o suporte administativo.");
            return [
                'status' => false,
                'message' => 'Licença ausente ou não encontrada, por favor active a licença ou contacte o suporte administativo.'
            ];
        }

        try {
            $decoded = decryptLicenseCode($licenseCode);

            if (!is_string($decoded)) {
                \Log::warning("decryptLicenseCode não retornou string", ['type' => gettype($decoded)]);
                write_Clientlicense_log(
                    "decryptLicenseCode não retornou string",
                    'warning', // nível do log
                    ['type' => gettype($decoded)] // contexto extra
                );


                return ['status' => false, 'message' => 'Formato de licença inválido'];
            }

            $dados = json_decode($decoded, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::warning("Não foi possível decodificar a licença", ['error' => json_last_error_msg()]);
                write_Clientlicense_log(
                    "Não foi possível decodificar a licença",
                    'warning',
                    ['error' => json_last_error_msg()]
                );


                return ['status' => false, 'message' => 'Não foi possível decodificar a licença'];
            }

            \Log::info("Licença carregada com sucesso", ['data' => $dados]);
            write_Clientlicense_log(
                "Licença carregada com sucesso",
                'info',
                ['data' => $dados]
            );



            return ['status' => true, 'message' => 'Licença carregada com sucesso', 'data' => $dados];
        } catch (\Exception $e) {
            write_Clientlicense_log(
                "Erro ao decodificar licença: " . $e->getMessage(),
                'error' // nível do log
            );

            \Log::error("Erro ao decodificar licença: " . $e->getMessage());
            return ['status' => false, 'message' => 'Erro ao decodificar licença: ' . $e->getMessage()];
        }
    }


    // Pegar a data
    private function isExpired(): bool
    {
        $licenseData = $this->getLicenseData();
        return now()->gt(Carbon::parse($licenseData['data']['expire_in']));
    }



    // Verifica se o hardware bate
    private function isHardwareValid(): bool
    {
        $licenseData = $this->getLicenseData();

        return $licenseData['data']['hardware_id'] === getHardwareFingerprint();
    }

    private function isPublicKeyValid(): bool
    {
        $licenseData = $this->getClientStoragePathKeyPublic();
        return file_exists($licenseData);
    }
    private function isRSASignatureValid(array $licenseData): bool
    {
        // Pega os dados reais da licença
        $data = $licenseData['data'];

        if (empty($data['rsa'])) {
            return false;
        }

        $publicKeyPath = $this->getClientStoragePathKeyPublic();
        ;
        if (!file_exists($publicKeyPath)) {
            return false;
        }

        $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyPath));
        $assinaturaRSA = base64_decode($data['rsa']);

        $dadosCriticos = json_encode([
            'client_name' => $data['client_name'],
            'hardware_id' => $data['hardware_id'],
            'expire_in' => $data['expire_in'],
        ]);

        return openssl_verify($dadosCriticos, $assinaturaRSA, $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }

    private function isHmacValid(): bool
    {
        $licenseData = $this->getLicenseData();

        // Se não houver licença ou dados
        if (!$licenseData) {
            return false;
        }

        $data = $licenseData['data'];

        if (empty($data['hmac'])) {
            return false;
        }

        $dadosParaHMAC = $data;
        unset($dadosParaHMAC['hmac']);

        // Calcula assinatura
        $assinaturaCorreta = hash_hmac('sha256', json_encode($dadosParaHMAC), $this->extraSecret());

        // Compara com segurança
        return hash_equals($assinaturaCorreta, $data['hmac']);
    }



    //resposta de verificação expirada e hardware
    public function verificationStatus(): array
    {
        if (!$this->isPublicKeyValid()) {
            write_Clientlicense_log("Chave pública é inválida ou não existe, contate o suporte para adquirir uma nova chave!", 'warning');
            return ['status' => false, 'message' => 'Chave pública é inválida ou não existe, contate o suporte para adquirir uma nova chave!'];
        }

        $licenseData = $this->getLicenseData();
        if (!$licenseData['status']) {
            write_Clientlicense_log($licenseData['message'], 'warning');
            return ['status' => false, 'message' => $licenseData['message']];
        }

        if ($this->isExpired()) {
            write_Clientlicense_log("Licença expirada", 'warning');
            return ['status' => false, 'message' => 'Licença expirada'];
        }

        if (!$this->isHardwareValid()) {
            write_Clientlicense_log("Licença inválida para este hardware ou máquina", 'warning');
            return ['status' => false, 'message' => 'Licença inválida para este hardware ou máquina'];
        }

        if (!$this->isRSASignatureValid(getLicenseCodedecrypted())) {
            write_Clientlicense_log("Assinatura RSA inválida ou ausente!", 'warning');
            return ['status' => false, 'message' => 'Assinatura RSA inválida ou ausente!'];
        }

        if (!$this->isHmacValid()) {
            write_Clientlicense_log("Assinatura HMAC inválida!", 'warning');
            return ['status' => false, 'message' => 'Assinatura HMAC inválida!'];
        }

        write_Clientlicense_log("Licença válida!", 'info');
        return ['status' => true, 'message' => 'Licença válida'];
    }



    public function checkLicense(): array
    {

        $licenseData = $this->getLicenseData();

        $now = Carbon::now();
        $expire = Carbon::parse($licenseData['data']['expire_in']);
        $daysLeft = ceil($now->diffInDays($expire, false));

        return [
            'valid' => $daysLeft >= 0,
            'days_left' => $daysLeft
        ];
    }

    //Ativar a licença
    public function activateLicense(string $licenseCode): array
    {
        if (!File::exists($this->getStorageKeys())) {
            write_Clientlicense_log("Chave pública não existe. Faça o upload primeiro, para ativar a licença.", 'warning');
            return ['success' => false, 'message' => 'Chave pública não existe. Faça o upload primeiro, para ativar a licença.'];
        }

        try {
            $dados = json_decode(decryptLicenseCode($licenseCode), true);
        } catch (\Exception $e) {
            write_Clientlicense_log("Código de licença inválido.", 'warning');
            return ['success' => false, 'message' => 'Código de licença inválido!'];
        }

        if (!$dados) {
            write_Clientlicense_log("Código de licença inválido!", 'warning');
            return ['success' => false, 'message' => 'Código de licença inválido!'];
        }

        // Segredo protegido e HMAC
        $extraSecret = $this->extraSecret();
        $dadosParaAssinatura = $dados;
        unset($dadosParaAssinatura['hmac']);

        $assinaturaCorreta = hash_hmac('sha256', json_encode($dadosParaAssinatura), $extraSecret);
        if (!hash_equals($assinaturaCorreta, $dados['hmac'])) {
            write_Clientlicense_log("Licença adulterada (HMAC inválido).", 'warning');
            return ['success' => false, 'message' => 'Licença adulterada (HMAC inválido)!'];
        }

        // Verificação RSA
        if (!empty($dados['rsa'])) {
            $publicKeyPath = $this->getClientStoragePathKeyPublic();
            $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyPath));
            $assinaturaRSA = base64_decode($dados['rsa']);

            if (
                openssl_verify(json_encode([
                    'client_name' => $dados['client_name'],
                    'hardware_id' => $dados['hardware_id'],
                    'expire_in' => $dados['expire_in'],
                ]), $assinaturaRSA, $publicKey, OPENSSL_ALGO_SHA256) !== 1
            ) {
                write_Clientlicense_log("Licença adulterada (Chave pública inválida).", 'warning');
                return ['success' => false, 'message' => 'Licença adulterada (Chave pública inválida)!'];
            }
        } else {
            write_Clientlicense_log("Licença inválida (sem assinatura RSA).", 'warning');
            return ['success' => false, 'message' => 'Licença inválida (sem assinatura RSA)!'];
        }

        // Valida expiração
        if (now()->gt(Carbon::parse($dados['expire_in']))) {
            write_Clientlicense_log("Licença expirada.", 'warning');
            return ['success' => false, 'message' => 'Licença expirada!'];
        }

        // Valida hardware
        if ($dados['hardware_id'] !== getHardwareFingerprint()) {
            write_Clientlicense_log("Licença não corresponde a esta máquina.", 'warning');
            return ['success' => false, 'message' => 'Licença não corresponde a esta máquina!'];
        }

        // Salva no banco e arquivo
        License::updateOrCreate(
            ['id' => 1],
            [
                'license_code' => $licenseCode,
                'valid_until' => $dados['expire_in'],
            ]
        );

        file_put_contents(getClientStoragePathDat(), $licenseCode);
        write_Clientlicense_log("Licença salva no banco e arquivo com sucesso.", 'info');

        write_Clientlicense_log("Licença ativada com sucesso! Válida até " . $dados['expire_in'], 'info');
        return ['success' => true, 'message' => 'Licença ativada com sucesso! Válida até ' . $dados['expire_in']];
    }



}
