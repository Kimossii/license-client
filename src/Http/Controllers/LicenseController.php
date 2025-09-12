<?php

namespace LicenseClient\Http\Controllers;

use Illuminate\Routing\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Models\License;
//use App\Helpers\LicenseHelper;
use LicenseClient\Helpers\LicenseHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Encryption\DecryptException;
//use App\Services\LicenseService;
use LicenseClient\Services\LicenseService;
require_once base_path('vendor/eluki/license-client/src/Helpers/globalVariables.php');
require_once base_path('vendor/eluki/license-client/src/Helpers/HardwareHelper.php');
require_once base_path('vendor/eluki/license-client/src/Helpers/LicenseHelper.php');


class LicenseController extends Controller
{
    private LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    public function formKeyPublic()
    {
        $keysPath = getClientStoragePathKeyPublic();
        $chaveExiste = File::exists($keysPath);

        return view("license-client::uploader_key", compact('chaveExiste'));
    }

    public function uploadKey(Request $request)
    {
        $request->validate([
            'public_key' => 'required|file|mimes:txt,pem|max:1024',
            'overwrite' => 'nullable|boolean',
        ]);

        $file = $request->file('public_key');
        $overwrite = $request->has('overwrite');

        $result = $this->licenseService->uploadPublicKey($file, $overwrite);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('warning', $result['message']);
    }
    public function activateForm()
    {

        return view('license-client::activate');
    }



    public function activate(Request $request, LicenseService $licenseService)
    {
        $request->validate([
            'license_code' => 'required|string',
        ]);

        $result = $licenseService->activateLicense($request->license_code);

        if ($result['success']) {
            return redirect()->route('index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }



    //Método para gerar request code
    public function requestCode()
    {
        $dados = [
            'client_name' => clientName(),
            'hardware_id' => getHardwareFingerprint(),
            'data' => now()->toDateString(),
        ];
        $requestCode = generateRequestCode($dados);

        return view('license-client::request', compact('requestCode'));
    }



    public function index()
    {
        $licenseInfo = $this->licenseService->checkLicense();

        if ($licenseInfo['days_left'] <= 5) {
            session()->flash('warning', 'Sua licença vai expirar em ' . $licenseInfo['days_left'] . ' dias!');
        }

        return view('license-client::index_teste');
    }
}
