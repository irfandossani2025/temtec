<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature = 'temtec:create-admin {email} {name=Administrator} {--password= : Leave empty to generate one}';

    protected $description = 'Create (or promote) an admin user who can access /admin';

    public function handle(): int
    {
        $password = $this->option('password') ?: Str::password(16, symbols: false);

        $user = User::firstOrNew(['email' => strtolower($this->argument('email'))]);
        $isNew = ! $user->exists;

        $user->forceFill([
            'name' => $user->name ?: $this->argument('name'),
            'is_admin' => true,
            ...($isNew || $this->option('password') ? ['password' => $password] : []),
        ])->save();

        $this->info(($isNew ? 'Created' : 'Promoted').' admin: '.$user->email);

        if ($isNew && ! $this->option('password')) {
            $this->warn("Generated password: {$password}  (change it after first login)");
        }

        return self::SUCCESS;
    }
}
