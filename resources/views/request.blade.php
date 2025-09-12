<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @include('license-client::spinner')
<div style="max-width: 600px; margin: 40px auto; padding: 32px 24px; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); font-family: 'Segoe UI', Arial, sans-serif;">
    <h2 style="text-align:center; color:#2d3748; margin-bottom: 24px;">Seu Request Code</h2>
    <textarea id="requestCode" cols="80" rows="5" readonly
        style="width:100%; font-size:16px; padding:12px; border-radius:8px; border:1px solid #cbd5e1; background:#f8fafc; resize:none; margin-bottom: 16px;">{{ $requestCode }}</textarea>
    <p style="color:#4b5563; margin-bottom: 20px; text-align:center;">
        Copie este código e envie ao administrador para gerar sua licença.
    </p>
    <div style="display:flex; align-items:center; justify-content:center; gap:16px;">
        <button onclick="copiarCodigo()"
            style="background:#2563eb; color:#fff; border:none; border-radius:6px; padding:10px 20px; font-size:15px; cursor:pointer; transition:background 0.2s;">
            Copiar código
        </button>
        <span id="mensagem" style="color:#16a34a; font-weight:500;"></span>
        <a href="{{ route('license.activate.form') }}"
           style="background:#f1f5f9; color:#2563eb; border-radius:6px; padding:10px 20px; font-size:15px; text-decoration:none; border:1px solid #2563eb; transition:background 0.2s;">
           Ativar licença
        </a>
    </div>
</div>

<script>
function copiarCodigo() {
    var textarea = document.getElementById('requestCode');

   
    var tempInput = document.createElement('textarea');
    tempInput.value = textarea.value;
    document.body.appendChild(tempInput);
    tempInput.select();

    try {
        var successful = document.execCommand('copy');
        var msg = document.getElementById('mensagem');
        if (successful) {
            msg.textContent = 'Código copiado!';
        } else {
            msg.textContent = 'Falha ao copiar!';
        }
        setTimeout(() => msg.textContent = '', 2000);
    } catch (err) {
        alert('Erro ao copiar o código.');
        console.error(err);
    }


    document.body.removeChild(tempInput);
}
</script>

</html>


