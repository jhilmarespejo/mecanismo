
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


