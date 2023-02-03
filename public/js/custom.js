
/*Se inserta los valores de src, descripcion para desplegar los archivos de imagenes o documentos*/
$(document).on('click', '.getFileModal', function(e){
// $('').on("click", function () {
    $('#modal_header p').empty();
    $('#modal_body').empty();

    let srcActual = $(this).find('img').attr('src');
    let descripcionActual = $(this).find('p').text();
    let extension = srcActual.split(".");

    if( extension[1] == 'jpg' || extension[1] == 'png' || extension[1] == 'jpeg' ){
        $('#modal_body').append('<img src="'+srcActual+'" class="img-fluid" alt="'+descripcionActual+'">');
        $('#modal_header p').append(descripcionActual);
        $('.modal-dialog').removeClass('modal-lg');
    }
    if( extension[1] == 'pdf' ){
        $('#modal_body').append('<div class="embed-responsive embed-responsive-4by3" ><iframe class="embed-responsive-item w-100" style="height: 500px;" src="'+srcActual+'"></iframe></div>');
        $('#modal_header p').append(descripcionActual);
        $('.modal-dialog').addClass('modal-lg');
    }

    if(extension[1] == 'ogg' || extension[1] == 'mp3' ||extension[1] == 'flak' || extension[1] == 'acc' || extension[1] == 'wav' ){
        $('#modal_body').append('<audio controls><source src="'+srcActual+'" type="audio/mpeg"></audio>');
        $('#modal_header p').append(descripcionActual);
        $('.modal-dialog').removeClass('modal-lg');
    }
    if(extension[1] == 'mp4' || extension[1] == 'avi' || extension[1] == 'webm' || extension[1] == 'mov' || extension[1] == 'flv' || extension[1] == 'mkv' || extension[1] == 'wmv' ){
        $('#modal_body').append('<video width="400" controls><source src="'+srcActual+'" type="video/mp4"></video>');
        $('#modal_header p').append(descripcionActual);
        $('.modal-dialog').removeClass('modal-lg');
    }
    if(extension[1] == 'docx' || extension[1] == 'doc' || extension[1] == 'xlsx' || extension[1] == 'xls' || extension[1] == 'pptx' || extension[1] == 'ppt' ){
        $('#modal_body').append("<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=127.0.0.1:8001/uploads/adjuntos/FOKNNt4FZhujfv1D9f2zxnUocsEMtOou5bMmSFmd.doc' frameborder='0'></iframe>");
        $('#modal_header p').append(descripcionActual);
        $('.modal-dialog').removeClass('modal-lg');
    }
});


/* CODIGO PARA OBSERVACIONES/RECOMENDACIONES */

    /* Adiciona un input file + una descripcion para nuevo archivo o documento */
        let a=0
    $(document).on('click', '.nuevo-adjunto', function(){

            let id = $(this).attr('id').replace(/[^0-9]/g,'');

        $("#archivos").append('<fieldset id="archivos_'+a+'" class="hover border px-2 mx-2"><legend class="fs-6 float-none w-auto p-2">Archivos <i class="btn p-0 text-danger bi bi-trash eliminar-archivo" id="eliminar_archivo_'+a+'"></i></legend>   <input type="file" class="form-control col-sm" id="ARC_archivo_'+a+'" name="ARC_archivo[]" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" capture />     <p id="ARC_archivo_'+a+'_err" class="text-danger error col"></p><label for="formFileSm" class="form-label">Descripción:</label><input type="text" class="form-control" name="ARC_descripcion[]"><p id="ARC_descripcion_'+a+'_err" class="text-danger error col"></p></fieldset>');
            $('#archivos_'+a).hide().slideDown(800);
            ++a;
        });

    // Agrega una nueva recomendación
    $(document).on('click', '.eliminar-archivo', function(e){
        let id = $(this).attr('id').replace(/[^0-9]/g,'');
        console.log(id);
        $("#archivos_"+id).slideToggle(500, function()
        { $("#archivos_"+id).remove();  });
    });

    // Guardar nueva recomendación
    $(document).on('click', '.nueva-recomendacion', function(e){
        let id = $(this).attr('id').replace(/[^0-9]/g,'');
        e.preventDefault();
        var formData = new FormData( $('#form_recomendaciones_'+id)[0] );
        // console.log(formData);
        $.ajax({
            async: true,
            // headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: "/recomendaciones/nuevaRecomendacion",
            type: 'post',
            data: formData, //$(".form-recomendaciones").serialize(),
            beforeSend: function () {
                $('#cargando_'+id).removeClass('d-none');
                $('#guardar_recomendacion_'+id).addClass('d-none');
            },
            processData: false,
            contentType: false,
            success: function (data, response) {
                $('#cargando_'+id).addClass('d-none');
                $('#guardar_recomendacion_'+id).removeClass('d-none');

                $('small.error').empty();
                // Muestra los errores de validacion
                // console.log(data.errors );
                jQuery.each(data.errors, function( key, value ){
                    let error = ('#'+key+'_err').replace('.','_');
                    $(error).append( value );
                });

                /*valida input file vacio*/
                $('#form_recomendaciones_1 :input[type="file"]').each(function () {
                    // console.log($(this).find('input [type=file]'));
                    $(this).empty();
                    if(!$(this).val() ){
                        console.log('vacio');
                        $('#'+$(this).attr('id')+'_err').append('El dato es necesario!');
                        // console.log( $('input[type="file"]').attr('name') );
                    }else{
                        console.log('sss');
                    }
                });

                if(data.message == 'correcto'){
                    console.log('correcto');
                    $('#guardar_recomendacion_'+id).text('Guardado');
                    // location.reload();
                }
                // window.location.reload();
            },
            error: function(response){ console.log(response) }
        });
    });

    /*agrega inputs para nuevos archivos*/
    let j=0;
    $(document).on('click', '.nuevo-archivo', function(){
        //let id = $(this).attr('id').replace(/[^0-9]/g,'');
        $(".archivos").append('<div class="input-group input-group-sm p-1 adjunto" id="adjunto_'+j+'"> <input type="file" class="form-control input-archivo" id="archivo_'+j+'" name="REC_archivo[]" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" capture /> <span class="input-group-text">Descripción:</span>  <input type="text" class="form-control" id="descripcion_'+j+'" name="ARC_descripcion[]">   <span class="input-group-text btn-danger text-light rounded remover-adjunto" id="remover_adjunto_'+j+'">    <i class="text-dark bi bi-trash"></i> </span>     <div class="container row">    <small class="text-danger col" id="archivo_'+j+'_err"></small><small class="text-danger col" id="descripcion_'+j+'_err"></small></div></div>');
        ++j;
    });

    /*Elimina el contenedor de archivos adjuntos*/
    $(document).on('click', '.remover-adjunto', function(){
        let id = $(this).attr('id').replace(/[^0-9]/g,'');
        $("#adjunto_"+id).remove();
    });

    /*Agrega nuevo cumplimiento*/
    function agregarCumplimiento( recomendacion, fecha, id ){
        $(".archivos").empty();
        $('textarea[name="REC_detallesCumplimiento"]').val('');
        $(".val-recomendacion, .val-fecha-recomendacion, .rec-id").empty();
        $(".val-recomendacion").append(recomendacion);
        $(".val-fecha-recomendacion").append(fecha);
        $(".rec-id").val(id);
    }

    /*Guarda el cumplimiento de una observación*/
    $(document).on('click', '#guardar_cumplimiento', function(e){
    // $("#guardar_cumplimiento").on('click', function(e){
        e.preventDefault();
        let formData = new FormData($('#recomendaciones_form')[0]);
        $.ajax({
            async: true,
            // headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: "/recomendaciones/cumplimiento",
            type: 'POST',
            data: formData,//$('#recomendaciones_form').serialize(),
            contentType: false,
            processData: false,
            beforeSend: function () {
                /*laravel solo valida una campo de imagen. Entonces La validación se hace manualmente*/
                if ( $('.adjunto').length ) {
                    let vacio;
                    $("#recomendaciones_form :input").each(function () {
                        let id = $(this).attr('id');
                        $('#'+id+'_err').empty();
                        if ( $('#'+id ).val() == '' ) {
                            // console.log('vacio');
                            $('#'+id+'_err').append('El dato es necesario!');
                            vacio = true;
                        }else{
                            $('#'+id+'_err').empty();
                            // console.log('lleno');
                        }
                    });
                    if (vacio) {
                        return false;
                        // console.log('error');
                    }
                }
            },
            success: function (data, response) {
                $('small.error').empty();
                // Muestra los errores de validacion
                console.log(data.errors);
                jQuery.each(data.errors, function(key, value){
                    // console.log(key, value);
                    $('#'+key+'_err').append( '<p>'+value+'</p>' );
                });
                if(data.message == 'correcto'){
                    location.reload();
                }
            },
            error: function(response){  }
        });
    });
