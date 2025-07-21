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
    
    

// public function authenticate(string $username, string $password): bool
// {
//     try {
//         $response = Http::withOptions([
//             'verify' => false
//         ])->withHeaders([
//             'userclient' => self::API_KEY
//         ])->timeout(30)->post(self::API_URL, [
//             'usr_usuario' => $username,
//             'usr_password' => $password
//         ]);

//         // logger()->info('API Response:', $response->json());
//         $apiJson = $response->json();
//         logger()->info('API Response:', is_array($apiJson) ? $apiJson : []);
        
        
//         if ($response->successful() && $response->json('codigo') == 1) {
//             $this->createOrUpdateUser($response->json());
//             return true;
//         }

//         return false;
    
//     } catch (\Exception $e) {
//         logger()->error('Error en autenticación API: ' . $e->getMessage());
//         return false;
//     }
// }
    
    public function authenticate(string $username, string $password): array
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

            $apiJson = $response->json();
            logger()->info('API Response:', is_array($apiJson) ? $apiJson : []);

            if ($response->status() >= 500) {
                return ['success' => false, 'error' => 'Servidor externo no disponible'];
            }

            if ($response->successful() && $apiJson['codigo'] == 1) {
                $this->createOrUpdateUser($apiJson);
                return ['success' => true];
            }
            
            return ['success' => false, 'error' => 'Credenciales inválidas'];
        
        } catch (\Exception $e) {
            logger()->error('Error en autenticación API: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error de conexión con el API'];
        }
    }
    
    
    // Almacena datos del usuarui en la tabla users
    
    // private function createOrUpdateUser(array $apiData): void
    // {
    //     dump($apiData);exit;
    //     $user = User::updateOrCreate(
    //         ['username' => $apiData['usuariores']['usuario']],
    //         [
    //             'name' => $apiData['usuariores']['usuario'],
    //             'rol' => $apiData['usuariores']['cod_rol'],
    //             'id_entidad' => $apiData['usuariores']['id_entidad'],
    //             'cod_oficina' => $apiData['usuariores']['cod_oficina'],
    //             'nombre_oficina' => $apiData['usuariores']['nombre_oficina'],
    //             'password' => bcrypt(Str::random(16)),
    //             'status' => 1  // Establecer como activo por defecto
    //         ]
    //     );
        
    //     Auth::login($user, true);  // Añadir el segundo parámetro para "recordar"
    //     session(['api_token' => $apiData['token']]);
    // }

    private function createOrUpdateUser(array $apiData): void
    {
        $token = $apiData['token'];
        
        // Dividir el token JWT en partes
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            logger()->error('Token JWT malformado');
            return;
        }
        
        // Decodificar el payload (parte 2 del JWT)
        $payload = json_decode(base64_decode($parts[1]), true);
        
        if (!$payload || !isset($payload['usuario'])) {
            logger()->error('Token JWT sin datos esperados', ['payload' => $payload]);
            return;
        }
        //dump($payload['id_usuario']);exit;

        // Crear o actualizar usuario con los datos del token
        $user = User::updateOrCreate(
            ['username' => $payload['usuario']],
            [
                'name' => $payload['usuario'],
                'rol' => $payload['cod_rol'] ?? null,
                'id_entidad' => $payload['id_entidad'] ?? null,
                'cod_oficina' => $payload['cod_oficina'] ?? null,
                'nombre_oficina' => $payload['nombre_oficina'] ?? null,
                'password' => bcrypt(Str::random(16)),
                'status' => 1,
                'id_usuario_dp' => $payload['id_usuario'],
            ]
        );
        
        Auth::login($user, true);
        session(['api_token' => $token]);
    }
    

    // private function createOrUpdateUser(array $apiData): void
    // {
    //     $token = $apiData['token'];

    //     // Dividir el token JWT en partes
    //     $parts = explode('.', $token);

    //     if (count($parts) !== 3) {
    //         logger()->error('Token JWT malformado');
    //         return;
    //     }

    //     // Decodificar el payload (parte 2 del JWT)
    //     $payload = json_decode(base64_decode($parts[1]), true);

    //     // Puedes ver los datos con un dump o logger
    //     logger()->info('Datos del token decodificado:', $payload);
    //     // dump($payload); // para mostrar en la pantalla del navegador
    //     // dump($apiData); // para mostrar en la consola
    //     // exit;
    //     // Crear o actualizar usuario
    //     $user = User::updateOrCreate(
    //         ['username' => $apiData['usuariores']['usuario']],
    //         [
    //             'name' => $apiData['usuariores']['usuario'],
    //             'rol' => $apiData['usuariores']['cod_rol'],
    //             'id_entidad' => $apiData['usuariores']['id_entidad'],
    //             'cod_oficina' => $apiData['usuariores']['cod_oficina'],
    //             'nombre_oficina' => $apiData['usuariores']['nombre_oficina'],
    //             'password' => bcrypt(Str::random(16)),
    //             'status' => 1
    //         ]
    //     );

    //     Auth::login($user, true);
    //     session(['api_token' => $token]);
    // }


}   