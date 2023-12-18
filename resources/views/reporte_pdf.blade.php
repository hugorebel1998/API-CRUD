<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>Reporte de usuarios</title>
</head>

<body>
    <div class="text-center">
        <h1>Reporte de usuarios</h1>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Imagen</th>
                <th scope="col">Nombres</th>
                <th scope="col">Correo</th>
                <th scope="col">Celular</th>
                <th scope="col">Tipo usuario</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <img src="{{ base_path('public/img/products/' . $usuario->fotografia) }}"
                        style="max-width: 100px; max-height: 80px;">
                    <th> {{ $usuario->nombre }} {{ $usuario->apellidos }}</th>
                    <td>{{ $usuario->correo_electronico }}</td>
                    <td>{{ $usuario->celular }}</td>
                    <td>
                        @if ($usuario->rol_id === 1)
                            <div>Administrador</div>
                        @else
                            <div>Básico</div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
