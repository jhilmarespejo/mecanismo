{{-- categorias/index --}}
@extends('layouts.app')
@section('title', 'Ajustes')

@section('content')
ajustes
<script>
    // $(document).ready(function () {

    //     $("#btn_guarda_categoria").click( function(e){
    //         // console.log('xxxx');
    //         $.ajax({
    //             async: true,
    //             headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    //             url: "categorias/guardaNuevaCategoria",
    //             type: 'post',
    //             data: $("#form_nueva_categoria").serialize(),
    //             beforeSend: function () { },
    //             success: function (data, response) {
    //                 $('small.error').empty();
    //                 jQuery.each(data.errors, function( key, value ){
    //                     $('#'+key+'_err').append( value );
    //                 });
    //                 if(!data.errors){
    //                     Swal.fire({
    //                         icon: 'success',
    //                         title: data.message,
    //                         showConfirmButton: true,
    //                     });
    //                     // setTimeout(function(){ location.reload() }, 2000);
    //                 }
    //             },
    //             error: function(response){ console.log(response) }
    //         });
    //     });


    // });
</script>

@endsection

