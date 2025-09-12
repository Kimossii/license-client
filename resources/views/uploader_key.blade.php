<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Chave Pública</title>
</head>

<body>
    @include('license-client::spinner')
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f8fafc;">
        <div style="background: #fff; padding: 2.5rem 2rem; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); min-width: 350px; max-width: 400px;">
            <h1 style="text-align: center; font-size: 1.5rem; font-weight: 600; color: #22223b; margin-bottom: 1.5rem;">Importar a chave pública</h1>

            @if (session('success'))
                <div style="margin-bottom: 1rem; color: #16a34a; background: #dcfce7; border-radius: 6px; padding: 0.75rem; text-align: center;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($chaveExiste)
                <div style="margin-bottom: 1rem; color: #b45309; background: #fef3c7; border-radius: 6px; padding: 0.75rem; text-align: center;">
                    Chave pública já existe. Deseja substituir?
                </div>
                <form id="uploadKeyForm" action="{{ route('client.uploadKey') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    <input type="hidden" name="overwrite" value="1">
                    <label for="public_key" style="font-weight: 500; color: #22223b;">Enviar Public Key (.pem):</label>
                    <input type="file" id="public_key" name="public_key" accept=".pem" required onchange="previewFile()" style="padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <button type="submit" style="margin-top: 0.5rem; background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 0.75rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">Substituir Chave</button>
                </form>
            @else
                <form id="uploadKeyForm" action="{{ route('client.uploadKey') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    <label for="public_key" style="font-weight: 500; color: #22223b;">Enviar Public Key (.pem):</label>
                    <input type="file" id="public_key" name="public_key" accept=".pem" required onchange="previewFile()" style="padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <div id="filePreview" style="margin-top: 0.5rem; font-family: monospace; white-space: pre-wrap; background: #f1f5f9; border-radius: 6px; padding: 0.75rem; min-height: 48px; color: #334155; max-height: 180px; overflow-y: auto; font-size: 0.92rem;"></div>
                    <button type="submit" style="margin-top: 0.5rem; background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 0.75rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">Importar Chave</button>
                </form>
            @endif
            <a href="{{ route('license.activate') }}" style="display: block; text-align: center; margin-top: 1.5rem; background: #10b981; color: #fff; padding: 0.75rem 1rem; border-radius: 6px; font-weight: 600; text-decoration: none; transition: background 0.2s;">
                Ativar a licença
            </a>
        </div>
    </div>





</body>
<script>
        function previewFile() {
            const preview = document.getElementById('filePreview');
            const file = document.getElementById('public_key').files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.textContent = e.target.result.substring(0, 500) + (e.target.result.length > 500 ? '...' :
                        '');
                }
                reader.readAsText(file);
            } else {
                preview.textContent = '';
            }
        }
    </script>

</html>
