{{-- <x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-3" />


    </x-jet-authentication-card>
</x-guest-layout> --}}




@extends('layouts.app')
@section('title', 'Nuevo Usuario')

@section('content')
<div class="card-body">
    <h2 class="text-center" >Registro de nuevo usuario</h2>

 <!-- Display success message -->
@if (session('status'))
 <div class="mb-4 font-medium text-sm text-green-600">
     {{ session('status') }}
 </div>
@endif

<!-- Display error messages -->
@if ($errors->any())
 <div class="mb-4">
     <ul class="list-disc list-inside text-sm text-red-600">
        @foreach ($errors->all() as $error)
            @if ($error == 'validation.unique')
                <span class="alert alert-warning" >El nombre de usuario ya existe!</span>
            @else
                <li>{{ $error }}</li>
            @endif

         @endforeach
     </ul>
 </div>
@endif


    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <x-jet-label value="{{ __('Nombre') }}" />

            <x-jet-input class="{{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                         :value="old('name')" required autofocus autocomplete="name" />
            <x-jet-input-error for="name"></x-jet-input-error>
        </div>

        <div class="mb-3">
            {{-- <x-jet-label value="{{ __('Email') }}" />
            <x-jet-input class="{{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email"
                         :value="old('email')" required />
            <x-jet-input-error for="email"></x-jet-input-error> --}}

            <x-jet-label for="username" value="{{ __('Nombre de Usuario') }}" />
            <x-jet-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required />

        </div>
        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select">
                <option value='' selected>Seleccione</option>
                <option value="Operador">Operador</option>
                <option value="Administrador">Administrador</option>
            </select>
            {{-- <input class="form-control" type="text" name="rol" readonly value="Operador"> --}}
        </div>

        <div class="mb-3">
            <x-jet-label value="{{ __('Password') }}" />

            <x-jet-input class="{{ $errors->has('password') ? 'is-invalid' : '' }}" type="password"
                         name="password" required autocomplete="new-password" />
            <x-jet-input-error for="password"></x-jet-input-error>
        </div>

        <div class="mb-3">
            <x-jet-label value="{{ __('Confirmar Password') }}" />

            <x-jet-input class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mb-3">
                <div class="custom-control custom-checkbox">
                    <x-jet-checkbox id="terms" name="terms" />
                    <label class="custom-control-label" for="terms">
                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'">'.__('Privacy Policy').'</a>',
                            ]) !!}
                    </label>
                </div>
            </div>
        @endif

        <div class="mb-0">
            <div class="d-flex justify-content-end align-items-baseline">
                <a class="text-muted me-3 text-decoration-none" href="{{ route('login') }}">
                    {{ __('Ya está registrado?') }}
                </a>

                <x-jet-button>
                    {{ __('Registrar') }}
                </x-jet-button>
            </div>
        </div>
    </form>
</div>
@endsection
