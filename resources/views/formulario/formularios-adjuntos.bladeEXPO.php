@extends('layouts.app')
@section('title', 'Archivos adjuntos')

@section('content')
<div class="text-center head">
    <p class="text-primary m-0 p-0" id="titulo" style="font-size: 30px" > {{ $formulario['FRM_titulo'] }} </p>
    <p class=" m-0 p-0" id="establecimiento" style="font-size: 20px">Establecimiento: {{ $formulario['EST_nombre'] }}</p>
</div>
z
@if ($formulario['FK_EST_id'] == 676)
    @if ($formulario['FRM_id'] == 266)
    <ol style="--length: 5" role="list" id="list1" reversed>
        <li style="--i: 1">
            <h3>Formulario de observaciones</h3>
            <div class="row">
                <div class="col-sm-6 border-end">
                    <dl>
                        <dt>Fecha del documento:</dt>
                        <dd>28/03/2022</dd>

                        <dt>Responsable de la visita</dt>
                        <dd>Rommy Choque Ballesteros</dd>

                        <dt>Servidor público entrevistado</dt>
                        <dd>Sof. 2do. Aberto Cocarico</dd>
                    </dl>
                </div>
                {{-- Archivos e imagenes adjuntas --}}
                <div class="col-sm-6">
                    <p><strong>Archivo/s:</strong></p>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <i class="text-danger fs-1 bi bi-file-earmark-pdf-fill" title="Formulario de observaciones"></i>

                        <img style="height: 50px" src="/img/expo/30-03-2022-TUPIZA VILAZON.pdf" class="img-thumbnail d-none" alt="cvvvv">
                        <p class="d-none descripcion">Formulario de observaciones de visita</p>
                    </span>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <img style="height: 50px" src="/img/expo/3.jpg" class="img-thumbnail" alt="Lavandería en mal estado" title="Lavandería en mal estado">
                        <p class="d-none descripcion">Lavandería en mal estado</p>

                    </span>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <img style="height: 50px" src="/img/expo/5.jpg" class="img-thumbnail" alt="Baños deteriorados" title="Baños deteriorados">
                        <p class="d-none descripcion">Baños deteriorados</p>

                    </span>
                </div>
            </div>

        </li>
        <li style="--i: 2">
            <h3>Actas</h3>
            <div class="row">
                <div class="col-sm-6 border-end">
                    <dl>
                        <dt>Fecha del documento:</dt>
                        <dd>31/04/2022</dd>

                        <dt>Responsable de la visita</dt>
                        <dd>Rommy Choque Ballesteros</dd>

                        <dt>Servidor público entrevistado</dt>
                        <dd>Sgto. 1ro. Aberto S. Carvajal</dd>
                    </dl>
                </div>
                <div class="col-sm-6">
                    <p><strong>Archivo/s:</strong></p>

                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <i class="text-danger fs-1 bi bi-file-earmark-pdf-fill" title="Acta"></i>

                        <img style="height: 50px" src="/img/expo/acta2.pdf" class="img-thumbnail d-none" alt="cvvvv">
                        <p class="d-none descripcion">Acta de visita</p>
                    </span>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <i class="text-danger fs-1 bi bi-file-earmark-pdf-fill" title="Acta"></i>

                        <img style="height: 50px" src="/img/expo/acta1.pdf" class="img-thumbnail d-none" alt="cvvvv">
                        <p class="d-none descripcion">Acta de visita</p>
                    </span>

                </div>
            </div>
        </li>
        <li style="--i: 3">
            <h3>Casos comunes encontrados en centros penitenciarios</h3>
            <div class="row">
                <div class="col-sm-6 border-end">
                    <dl>
                        <dt>Fecha de visita:</dt>
                        <dd>20/04/2022</dd>

                        <dt>Responsable de la visita</dt>
                        <dd>Rommy Choque Ballesteros</dd>

                        <dt>Servidor público entrevistado</dt>
                        <dd>My. Jose Torrico Carvajal</dd>
                    </dl>
                </div>
                <div class="col-sm-6">
                    <p><strong>Archivo/s:</strong></p>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <img style="height: 50px" src="/img/expo/15.jpg" class="img-thumbnail" alt="PPLs encontrados en celdas de aislamiento con grilletes en los pies" title="PPLs encontrados en celdas de aislamiento con grilletes en los pies">
                        <p class="d-none descripcion">PPLs encontrados en celdas de aislamiento con grilletes en los pies</p>

                    </span>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <img style="height: 50px" src="/img/expo/16.jpg" class="img-thumbnail" alt="PPL con lesiones equimóticas producto de golpes con objeto contuso" title="PPL con lesiones equimóticas producto de golpes con objeto contuso">
                        <p class="d-none descripcion">PPL con lesiones equimóticas producto de golpes con objeto contuso</p>

                    </span>

                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <img style="height: 50px" src="/img/expo/17.jpg" class="img-thumbnail" alt="Celdas hacinadas en la carceleta de Guayaramerin" title="Celdas hacinadas en la carceleta de Guayaramerin">
                        <p class="d-none descripcion">Celdas hacinadas en la carceleta de Guayaramerin</p>

                    </span>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <img style="height: 50px" src="/img/expo/18.jpg" class="img-thumbnail" alt="Celdas hacinadas en la Carceleta de Riberalta" title="Celdas hacinadas en la Carceleta de Riberalta">
                        <p class="d-none descripcion">Celdas hacinadas en la Carceleta de Riberalta</p>
                    </span>
                </div>
            </div>
        </li>
    </ol>
    @endif
    @if ($formulario['FRM_id'] == 268)
        <ol style="--length: 5" role="list" id="list2">
            <li style="--i: 1">
                <h3>Entrevista en Audio</h3>
            <div class="row">
                <div class="col-sm-8 border-end">
                    <dl>
                        <dt>Fecha del documento:</dt>
                        <dd>30/05/2022</dd>

                        <dt>Responsable de la visita</dt>
                        <dd>Marco Quiroga</dd>
                        <dd>Edgar Quiroz</dd>

                        <dt>PPL entrevistado</dt>
                        <dd>Jose Luis Cossio</dd>
                    </dl>
                </div>
                {{-- Archivos e imagenes adjuntas --}}
                <div class="col-sm-4">
                    <p><strong>Archivo/s:</strong></p>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <span class="text-danger fs-1" title="Entrevista en audio">&#x1F5AD</span>

                        <img style="height: 50px" src="/img/expo/audio1.mp3" class="img-thumbnail d-none" alt="">
                        <p class="d-none descripcion">Entrevista en audio</p>
                    </span>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <i class="text-danger fs-1 bi bi-file-earmark-pdf-fill" title="Informe de atención 1"></i>

                        <img style="height: 50px" src="/img/expo/informe1.pdf" class="img-thumbnail d-none" alt="">
                        <p class="d-none descripcion">Informe de atención 1</p>
                    </span>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <i class="text-danger fs-1 bi bi-file-earmark-pdf-fill" title="Informe de atención 2"></i>

                        <img style="height: 50px" src="/img/expo/informe2.pdf" class="img-thumbnail d-none" alt="">
                        <p class="d-none descripcion">Informe de atención 2</p>
                    </span>
                    <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                        <img style="height: 50px" src="/img/expo/18.jpg" class="img-thumbnail" alt="Celdas hacinadas en la Carceleta de Riberalta" title="Celdas hacinadas en la Carceleta de Riberalta">
                        <p class="d-none descripcion">Celdas hacinadas en la Carceleta de Riberalta</p>
                    </span>


                </div>
            </div>
            </li>
            <li style="--i: 2">
                <h3>Verificación del estado de salud de la PPL Mauricio Salinas Gamboa </h3>
                <div class="row">
                    <div class="col-sm-8 border-end">
                        <dl>
                            <dt>Fecha del documento:</dt>
                            <dd>26/10/2022</dd>

                            <dt>Responsables de la visita</dt>
                            <dd>Marco Quiroga</dd>
                            <dd>Edgar Quiroz</dd>

                            <dt>PPL entrevistado</dt>
                            <dd>Salinas</dd>

                            <dt>Resumen</dt>
                            <dd>Mediante solicitud escrita que solicita verificación del estado de salud de la PPL Mauricio Salinas Gamboa quien habría sufrido agresiones físicas de parte del delegado de su sector en el penal de San Pedro de la ciudad de La Paz, parte del equipo del Mecanismo Nacional de Prevención de la Tortura acude al requerimiento</dd>
                        </dl>
                    </div>
                    {{-- Archivos e imagenes adjuntas --}}
                    <div class="col-sm-4">
                        <p><strong>Archivo/s:</strong></p>
                        <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                            {{-- <i class="text-danger fs-1 bi bi-file-earmark-pdf-fill" title="Entrevista en audio"></i> --}}
                            <i class="text-danger fs-1 bi bi-camera-reels-fill" title="Entrevista en audio"></i>
                            <img style="height: 50px" src="/img/expo/video1.mp4" class="img-thumbnail d-none" alt="">
                            <p class="d-none descripcion">Entrevista en audio</p>
                        </span>

                        <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                            <img style="height: 50px" src="/img/expo/22.jpeg" class="img-thumbnail" alt="Ambiente otorgado para valoración de PPL Salinas" title="Ambiente otorgado para valoración de PPL Salinas">
                            <p class="d-none descripcion">Ambiente otorgado para valoración de PPL Salinas</p>
                        </span>
                        <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                            <img style="height: 50px" src="/img/expo/20.jpg" class="img-thumbnail" alt="Revisión de cabeza y cuero cabelludo" title="Revisión de cabeza y cuero cabelludo">
                            <p class="d-none descripcion">Revisión de cabeza y cuero cabelludo</p>
                        </span>
                        <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                            <img style="height: 50px" src="/img/expo/23.jpeg" class="img-thumbnail" alt="Lesiones cortantes en cara anterior de antebrazo izquierdo" title="Lesiones cortantes en cara anterior de antebrazo izquierdo">
                            <p class="d-none descripcion">Lesiones cortantes en cara anterior de antebrazo izquierdo</p>
                        </span>
                        <span data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal">
                            <img style="height: 50px" src="/img/expo/20.jpeg" class="img-thumbnail" alt="Cicatrices antiguas en otras partes de su cuerpo" title="Cicatrices antiguas en otras partes de su cuerpo">
                            <p class="d-none descripcion">Cicatrices antiguas en otras partes de su cuerpo</p>
                        </span>


                    </div>
                </div>
            </li>
        </ol>
    @endif

<!-- Modal de imagen 1 -->
<div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="modal_header"><p class="p-0 modal-title"></p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="modal_body">
            </div>
        </div>
    </div>
</div>
<script>
    /*Se inserta los valores de src, descripcion para desplegar los archivos de imagenes o documentos*/
    $('.getFileModal').on("click", function () {
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

        if(extension[1] == 'mp3'){
            $('#modal_body').append('<audio controls><source src="'+srcActual+'" type="audio/mpeg"></audio>');
            $('#modal_header p').append(descripcionActual);
            $('.modal-dialog').removeClass('modal-lg');
        }
        if(extension[1] == 'mp4'){
            $('#modal_body').append('<video width="400" controls><source src="'+srcActual+'" type="video/mp4"></video>');
            $('#modal_header p').append(descripcionActual);
            $('.modal-dialog').removeClass('modal-lg');
        }
    });
</script>

<style>
    @import url("https://fonts.googleapis.com/css?family=Montserrat:400,700");

/* * {
	box-sizing: border-box;
} */
/*
body {
	--h: 212deg;
	--l: 43%;
	--brandColor: hsl(var(--h), 71%, var(--l));
	font-family: Montserrat, sans-serif;
	margin: 0;
	background-color: whitesmoke;
} */
/*
p {
	margin: 0;
	line-height: 1.6;
} */
dd{
    margin-left: 5%;
}
ol#list1, ol#list2 {
	list-style: none;
	counter-reset: list;
	padding: 0 1rem;
}

ol#list1 li, ol#list2 li {
	--stop: calc(100% / var(--length) * var(--i));
	--l: 62%;
	--l2: 88%;
	--h: calc((var(--i) - 1) * (180 / var(--length)));
	--c1: hsl(var(--h), 71%, var(--l));
	--c2: hsl(var(--h), 71%, var(--l2));

	position: relative;
	counter-increment: list;
	max-width: 80%;
	margin: 2rem auto;
	padding: 2rem 1rem 1rem;
	box-shadow: 0.1rem 0.1rem 1.5rem rgba(0, 0, 0, 0.3);
	border-radius: 0.25rem;
	overflow: hidden;
	background-color: white;
}

ol#list1  li::before, ol#list2  li::before {
	content: '';
	display: block;
	width: 100%;
	height: 1rem;
	position: absolute;
	top: 0;
	left: 0;
	background: linear-gradient(to right, var(--c1) var(--stop), var(--c2) var(--stop));
}

h3 {
	display: flex;
	align-items: baseline;
	margin: 0 0 1rem;
	color: rgb(70 70 70);
}

h3::before {
	display: flex;
	justify-content: center;
	align-items: center;
	flex: 0 0 auto;
	margin-right: 1rem;
	width: 3rem;
	height: 3rem;
	content: counter(list);
	padding: 1rem;
	border-radius: 50%;
	background-color: var(--c1);
	color: white;
}
</style>


@endif
@endsection
