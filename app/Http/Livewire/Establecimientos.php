<?php

namespace App\Http\Livewire;

use Livewire\{Component, WithPagination};
use App\Models\ModEstablecimiento;
use DB;

class Establecimientos extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['render', 'emit_eliminarEstablecimiento'];

    public $buscarEstablecimiento = '';
    public $open = false;
    public $buscarPorTipo = '';
    public $modal_edicion = false;
    public $readyToLoad = false;
    public $establecimiento;
    public $ordenColumna = 'EST_id',
            $ordenDireccion = 'Desc';

    public function updatingBuscarEstablecimiento()
    {
        $this->reset(['buscarPorTipo']);
    }

    public function render()
    {   DB::enableQueryLog();
        $tiposEstablecimientos = DB::table('tipo_establecimiento')
            ->select('TES_id', 'TES_tipo')
            ->get();

        $establecimientos = ModEstablecimiento::select('establecimientos.EST_id','establecimientos.EST_nombre','establecimientos.EST_direccion', 'establecimientos.EST_telefonoContacto', 'tes.TES_tipo', 'c.CID_nombre as Municipio', 'c2.CID_nombre as Provincia', 'c3.CID_nombre as Departamento')
            ->join('tipo_establecimiento as tes', 'establecimientos.FK_TES_id', 'tes.TES_id')
            ->leftJoin('ciudades as c', 'c.CID_id', 'establecimientos.FK_CID_id')
            ->leftJoin('ciudades as c2', 'c2.CID_id', 'c.FK_CID_id')
            ->leftJoin('ciudades as c3', 'c3.CID_id', 'c2.FK_CID_id')
            ->orWhere('establecimientos.FK_TES_id', 'ilike', '%' . $this->buscarPorTipo . '%')
            ->where('establecimientos.EST_nombre',  'ilike', '%' . $this->buscarEstablecimiento . '%')
            ->where('establecimientos.estado', 1)
            //->where('tes.TES_tipo','Centro Penitenciario' )
            ->orderby($this->ordenColumna, $this->ordenDireccion)
            ->paginate(5);
            $quries = DB::getQueryLog();
            dump($quries);
        return view('livewire.establecimientos',compact('establecimientos', 'tiposEstablecimientos') );
    }

    public function ordenar($columna){
        // dump($columna);exit;
        if ($this->ordenColumna == $columna) {
            if ($this->ordenDireccion == 'desc') {
                $this->ordenDireccion = 'asc';
            } else {
                $this->ordenDireccion = 'desc';
            }
        } else {
            $this->ordenColumna = $columna;
            $this->ordenDireccion = 'asc';
        }
    }

    public function emit_eliminarEstablecimiento($id){
        $dataUpdate = ModEstablecimiento::find( $id );
        $dataUpdate->estado = '0';
        if($dataUpdate->save()){
            $this->emit('success', 'Inhabilitado correctamente');
            $this->reset();
        } else {
            $this->emit('danger', 'Error, intenete nuevamente');
        }
    }

    // public function edit(ModEstablecimiento $establecimientos){
    //     $this->modal_edicion = true;
    //     $this->establecimientos = $establecimientos;
    // }

}
