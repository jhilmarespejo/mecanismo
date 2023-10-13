<?php

namespace App\Http\Controllers;

use App\Models\{ModFormulario};

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

// use Image;
// use Intervention\Image\Facades\Image;
// use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Storage;
/* agrupa la estructura del resultado de una consulta  por el $key */
class CustomController extends Controller {
    public static function array_group(array $array, string $key) {
        $grouped = [];
        foreach ($array as $item) {
            if(!$item[$key]){
                $item[$key] = 'nokey';
            }
            $grouped[$item[$key]][] = $item;
        }
        return $grouped;
    }
}
