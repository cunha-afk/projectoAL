<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // 1️⃣ Criar o utilizador
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'is_approved' => false,
        ]);

        // 2️⃣ Garantir que o role "cliente" existe com o guard certo
        $clienteRole = Role::firstOrCreate([
            'name' => 'cliente',
            'guard_name' => 'web',
        ]);

        // 3️⃣ Atribuir o role ao user (vai gravar em model_has_roles)
        $user->assignRole($clienteRole);

        // 4️⃣ Devolver o user
        return $user;
    }
}
