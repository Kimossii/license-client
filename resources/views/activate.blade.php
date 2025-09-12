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
<div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f4f6fb;">
    <div style="background: #fff; padding: 32px 36px 28px 36px; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); min-width: 380px; max-width: 480px; width: 100%;">
        <h2 style="text-align:center; color:#2d3748; margin-bottom: 24px; font-weight: 700;">Ativar Licença</h2>
        @if(session('success'))
            <p style="color:#38a169; background:#e6fffa; border-radius:6px; padding:10px 14px; margin-bottom: 16px; text-align:center;">{{ session('success') }}</p>
        @endif
        @if(session('error'))
            <p style="color:#e53e3e; background:#fff5f5; border-radius:6px; padding:10px 14px; margin-bottom: 16px; text-align:center;">{{ session('error') }}</p>
        @endif

        <form method="POST" action="{{ route('license.activate') }}">
            @csrf
            <label style="font-weight: 500; color:#4a5568; margin-bottom: 8px; display:block;">Insira a sua License Code:</label>
            <textarea name="license_code" cols="80" rows="5" required style="width:100%; border:1px solid #cbd5e0; border-radius:6px; padding:10px; font-size:15px; margin-bottom: 18px; resize: vertical; background: #f7fafc;"></textarea>
            <div style="display: flex; gap: 12px;">
                <button type="submit" name="action" value="activate" style="flex: 1; min-width: 80px; max-width: 140px; padding: 10px 0; font-size: 15px; background: #3182ce; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.2s;">Ativar</button>
                <a href="{{route('license.request')}}" style="flex: 1; min-width: 80px; max-width: 140px; padding: 10px 0; font-size: 15px; background: #edf2f7; color: #2b6cb0; border-radius: 6px; text-align: center; text-decoration: none; font-weight: 600; transition: background 0.2s;">Código de Ativação</a>
                <a href="{{route('import.uploadKey')}}" style="flex: 1; min-width: 80px; max-width: 140px; padding: 10px 0; font-size: 15px; background: #f6e05e; color: #744210; border-radius: 6px; text-align: center; text-decoration: none; font-weight: 600; transition: background 0.2s;">Importar chave pública</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
