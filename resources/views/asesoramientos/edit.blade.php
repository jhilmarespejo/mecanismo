{{-- categorias/index --}}
@extends('layouts.app')
@section('title', 'Asesoramiento')

@section('content')
<div class="container mt-5">


    <div class="card">
        <div class="card-header">
            <h2 class="mb-4 text-center text-primary">Modificacion de registro</h2>
        </div>
        @include('layouts.breadcrumbs', $breadcrumbs)
        <form action="/asesoramientos/{{ $asesoramiento->ASE_id }}" method="POST">
            @csrf @method('PUT')

            <div class="card-body">
                <div class="mb-3">
                    <label for="FK_MAN_id" class="form-label">Mandato:</label>
                    <select id="FK_MAN_id" name="FK_MAN_id" class="form-select">
                        @foreach ($mandatos as $mandato)
                            <option value="{{ $mandato->MAN_id }}" {{ old('FK_MAN_id', $asesoramiento->FK_MAN_id) == $mandato->MAN_id ? 'selected' : '' }}>
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
                    <input type="text" id="ASE_actividad" name="ASE_actividad" class="form-control" value="{{ old('ASE_actividad', $asesoramiento->ASE_actividad) }}">
                    @error('ASE_actividad')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="ASE_fecha_actividad" class="form-label">Fecha de Actividad:</label>
                    <input type="date" id="ASE_fecha_actividad" name="ASE_fecha_actividad" class="form-control" value="{{ old('ASE_fecha_actividad', $asesoramiento->ASE_fecha_actividad) }}">
                    @error('ASE_fecha_actividad')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                {{-- <div class="mb-3">
                    <label for="ASE_recomendacion" class="form-label">Recomendación:</label>
                    <input type="text" id="ASE_recomendacion" name="ASE_recomendacion" class="form-control" value="{{ old('ASE_recomendacion', $asesoramiento->ASE_recomendacion) }}">
                    @error('ASE_recomendacion')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                 --}}
            </div>

            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Actualizar registro</button>
            </div>
        </form>
    </div>

</div>

@endsection

