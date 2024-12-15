{{-- categorias/index --}}
@extends('layouts.app')
@section('title', 'Asesoramiento')

@section('content')
<div class="card ">
    @include('layouts.breadcrumbs', $breadcrumbs)
    <div class="card-header">
        <h2 class="mb-4 text-center text-primary">Nueva actividad de asesoramiento</h2>
    </div>
    <form action="/asesoramientos/store" method="POST">
        @csrf
        <div class="card-body">
            <div class="mb-3">
                <label for="FK_MAN_id" class="form-label">Mandato:</label>
                <select id="FK_MAN_id" name="FK_MAN_id" class="form-select">
                    <option value="" selected>Seleccione una opcion</option>
                    @foreach ($mandatos as $mandato)
                        <option value="{{ $mandato->MAN_id }}" {{ old('FK_MAN_id') == $mandato->MAN_id ? 'selected' : '' }}>
                            {{ $mandato->MAN_mandato }}
                        </option>
                    @endforeach
                </select>
                @error('FK_MAN_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="ASE_actividad" class="form-label">Actividad:</label>
                <textarea id="ASE_actividad" name="ASE_actividad" class="form-control" cols="30" rows="3"> {{ old('ASE_actividad') }} </textarea>
                @error('ASE_actividad')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="ASE_fecha_actividad" class="form-label">Fecha:</label>
                <input type="date" id="ASE_fecha_actividad" name="ASE_fecha_actividad" class="form-control" value="{{ old('ASE_fecha_actividad') }}">
                @error('ASE_fecha_actividad')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
           


        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-success">Crear</button>
        </div>
    </form>
</div>


@endsection

