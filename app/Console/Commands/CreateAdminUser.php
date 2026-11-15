<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zaffe:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively create a Super Admin user with a strong password and explicit admin role';

    private const WEAK_PASSWORDS = [
        'password123',
        'password',
        '12345678',
        '123456789',
        '123456789012',
        'admin123',
        'admin123456',
        'zaffe123',
        'qwerty123456',
    ];

    public function handle(): int
    {
        $name = trim((string) $this->ask('Admin full name'));
        $email = strtolower(trim((string) $this->ask('Admin email address')));

        $identityValidator = Validator::make(
            ['name' => $name, 'email' => $email],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
            ]
        );

        if ($identityValidator->fails()) {
            foreach ($identityValidator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error("A user with email [{$email}] already exists. Refusing to overwrite existing account credentials.");

            return self::FAILURE;
        }

        $password = (string) $this->secret('Admin password (min 12 characters, mixed case, number, symbol)');
        $passwordConfirmation = (string) $this->secret('Confirm admin password');

        if ($password !== $passwordConfirmation) {
            $this->error('Password confirmation does not match.');

            return self::FAILURE;
        }

        if (in_array(strtolower($password), self::WEAK_PASSWORDS, true)) {
            $this->error('Refusing weak or default demo password. Please choose a unique strong password.');

            return self::FAILURE;
        }

        $passwordValidator = Validator::make(
            ['password' => $password],
            [
                'password' => [
                    'required',
                    'string',
                    Password::min(12)->letters()->mixedCase()->numbers()->symbols(),
                ],
            ]
        );

        if ($passwordValidator->fails()) {
            foreach ($passwordValidator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info("Super Admin [{$admin->email}] created with role [admin]. Sign in at /admin/login.");

        return self::SUCCESS;
    }
}

