<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiAuthService
{
    private const API_URL = 'https://dev.defensoria.gob.bo/user_bk/api/login';
    private const API_KEY = 'ZJBH!zB)<9o#lCl7VLxcz2'; 
    
    

public function authenticate(string $username, string $password): bool
{
    try {
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'userclient' => self::API_KEY
        ])->timeout(30)->post(self::API_URL, [
            'usr_usuario' => $username,
            'usr_password' => $password
        ]);

        logger()->info('API Response:', $response->json());

        if ($response->successful() && $response->json('codigo') == 1) {
            $this->createOrUpdateUser($response->json());
            return true;
        }

        return false;
    
    } catch (\Exception $e) {
        logger()->error('Error en autenticación API: ' . $e->getMessage());
        return false;
    }
}
    
    // Almacena datos del usuarui en la tabla users
    
    private function createOrUpdateUser(array $apiData): void
    {
        $user = User::updateOrCreate(
            ['username' => $apiData['usuariores']['usuario']],
            [
                'name' => $apiData['usuariores']['usuario'],
                'rol' => $apiData['usuariores']['cod_rol'],
                'id_entidad' => $apiData['usuariores']['id_entidad'],
                'cod_oficina' => $apiData['usuariores']['cod_oficina'],
                'nombre_oficina' => $apiData['usuariores']['nombre_oficina'],
                'password' => bcrypt(Str::random(16)),
                'status' => 1  // Establecer como activo por defecto
            ]
        );
        
        Auth::login($user, true);  // Añadir el segundo parámetro para "recordar"
        session(['api_token' => $apiData['token']]);
    }

}   