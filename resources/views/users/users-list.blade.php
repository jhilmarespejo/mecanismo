@extends('layouts.app')
@section('title', 'Usuarios')

@section('content')
 {{-- @dump($users) --}}

 <div class="text-center">
    <h2 class="text-primary fs-2">Verificación y edición de datos de usuarios </h2>
</div>
<!-- Display success message -->
@if (session('status'))
<div class="mb-4 font-medium text-sm text-green-600">
    <p class="alert alert-success">{{ session('status') }}</p>
</div>
@endif
    <div class="container m-sm-3 p-sm-4 p-0" style="overflow-x:auto;">
        <table class="p-3 table table-border table-hover table-responsive-lg bg-light border" id="users">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombres y apellidos</th>
                    <th>Nombre de usuario</th>
                    {{-- <th>Password</th> --}}
                    <th>Cuenta activa</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $k=>$user)
                <tr id="{{$user->id}}">
                    <td>{{$k+1}}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    {{-- <td class="col-2">{{ $user->password }}</td> --}}
                    <td>
                       <span class="d-none"> {{$user->status}}</span>
                        <div class="form-check form-switch">
                            <input class="form-check-input switch" type="checkbox" id="switch_{{$user->id}}" {{($user->status == 1)? 'checked': '' }}>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="deleteUser({{ $user->id }})">Eliminar Usuario</button>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>

    <script type="text/javaScript">
        $(document).ready(function () {
            $('#users').DataTable({
                "pageLength": 50
            });
        });

        //Evento para eliminar un usuario
        function deleteUser(userId) {
            console.log(userId);
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¡No podrás revertir esto!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminarlo'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar solicitud de eliminación al controlador
                    $.ajax({
                        // async: true,
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        url: '/users/'+userId,
                        type: 'delete',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    '¡Eliminado!',
                                    'El usuario ha sido eliminado.',
                                    'success'
                                );
                                // var fila = document.getElementById(userId);
                                // Verificar si la fila existe
                                if ( $('#'+userId)) {
                                     $('#'+userId).remove();
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
            });
        }

        //Evento para cambiar de estado a un usuario
        $('.switch').change(function() {
            var isChecked = $(this).prop('checked');
            var id = $(this).attr('id');

            // Realizar solicitud AJAX
            $.ajax({
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: '/users/changeState', // Cambia esta URL por la ruta que deseas llamar
                type: 'POST', // Puedes cambiar el método según tus necesidades
                data: {id: id, status: isChecked},
                success: function(response) {
                    // Mostrar alerta con SweetAlert
                    Swal.fire({
                        title: 'Éxito',
                        text: 'El estado del usuario ha sido cambiado exitosamente',
                        icon: 'success'
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error al cambiar el estado del usuario: ' + error);
                    // Mostrar alerta con SweetAlert
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema al cambiar el estado del usuario',
                        icon: 'error'
                    });
                }
            });
        });

//TAREA, VERIFICAR QUE SOLO LOS USUARIOS CON ESTADO 1 INICIEN SESION
    </script>
@endsection
