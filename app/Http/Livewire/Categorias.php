<?php

namespace App\Http\Livewire;
use App\Models\ModCategoria;
// use App\Models\Ciudad;
use Livewire\Component;
use DB;

class Categorias extends Component
{
    public $categoria, $subCategoria, $subCategoriaActual;
    public $update = false;
    public $categorias = [], $subCategorias = [];

    protected $listeners = ['emit_Categorias_FKCATid', 'emit_Categorias_modal'];
    protected $paginationTheme = 'bootstrap';

    public function emit_Categorias_modal(){
        // $this->update = $estado;
        $this->reset(['update', 'subCategoria', 'categoria']);
        $this->subCategorias = collect();

    }

    public function emit_Categorias_FKCATid($CAT_id, $FK_CAT_id, $subCategoriaActual, $update){
        if(!$CAT_id){
            $this->categoria = $FK_CAT_id;
            $this->subCategoria = $FK_CAT_id;
            $this->subCategorias = collect();
        } else {
            $this->categoria = $CAT_id;
            $this->subCategoria = $FK_CAT_id;

            DB::enableQueryLog();
            $this->subCategorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
            ->where( 'FK_CAT_id', $this->categoria )
            ->get();

            // $quries = DB::getQueryLog();
            // dump($quries);//exit;
            //$this->subCategorias = collect();
        }
        $this->update = $update;
    }

    public function updatedCategoria( $CAT_id ){
        //$this->FK_CAT_id = $FK_CAT_id;
        $this->subCategorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
        ->where( 'FK_CAT_id', $CAT_id )
        ->get();

        // $this->subCategoria = $this->subCategorias->first()->CAT_id ?? null;

        /* NOTA: envia el $CAT_id al banco de preguntas como FK_CAT_id*/
        $this->emit('emit_BancoPreguntas_nuevaCategoria', $CAT_id);
    }
    public function updatedSubCategoria( $FK_CAT_id ){
        $this->emit('emit_BancoPreguntas_nuevaCategoria', $FK_CAT_id);
    }


    public function mount( ){

        $this->categorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
        ->whereNull('FK_CAT_id')
        ->orderby('CAT_categoria', 'asc')
        ->get();
        $this->subCategorias = collect();
    }
    public function render()
    {
        return view('livewire.categorias');
    }
}













// class Categorias extends Component
// {
//     public $categoria, $subCategoria, $FK_CAT_id, $CAT_id;
//     public $update = false;
//     public $categorias = [], $subCategorias = [];

//     protected $listeners = ['emit_Categorias_FKCATid'];

//     public function emit_Categorias_FKCATid($FK_CAT_id, $update){
//         $this->FK_CAT_id = $FK_CAT_id;
//         $this->update = $update;
//     }

//     public function updatedCategoria( $FK_CAT_id ){
//         $this->FK_CAT_id = $FK_CAT_id;
//         $this->subCategorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
//         ->where( 'FK_CAT_id', $this->FK_CAT_id )
//         ->get();

//         $this->subCategoria = $this->subCategorias->first()->CAT_id ?? null;

//         $this->emit('idCategoria', $this->FK_CAT_id);
//         //$this->emitUp('ponerCategoria',  $this->FK_CAT_id);

//     }
//     public function updatedSubCategoria( $FK_CAT_id ){
//         $this->emit('idCategoria', $FK_CAT_id);
//     }

//     public function mount( $CAT_id = null,){

//         if( $this->update ){
//             $this->categorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
//             ->where('CAT_id', $CAT_id)
//             ->get();

//         }else{
//             $this->categorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
//             ->whereNull('FK_CAT_id')
//             ->get();
//         }

//         $this->subCategorias = collect();
//     }
//     public function render()
//     {
//         return view('livewire.categorias');
//     }
// }
