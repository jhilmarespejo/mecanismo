<div class="mb-4 row">
    <div class="col">
        <select class="form-select" wire:model="dep">
            <option selected>Departamentos...</option>
            @foreach ($departamentos as $departamento)
                <option value="{{$departamento->CID_id}}">{{$departamento->CID_nombre}}</option>
            @endforeach
        </select>
    </div>
    <div class="col">
        <select class="form-select" wire:model="prov">
            <option selected>Provincias...</option>
            @foreach ($provincias as $provincia)
                <option value="{{$provincia->CID_id}}">{{$provincia->CID_nombre}}</option>
            @endforeach
        </select>
    </div>
    <div class="col">
        <select class="form-select" wire:model="FK_CID_id">
            <option selected>Municipios...</option>
            @foreach ($municipios as $municipio)
                <option value="{{$municipio->CID_id}}">{{$municipio->CID_nombre}}</option>
            @endforeach
        </select>
    </div>
</div>

