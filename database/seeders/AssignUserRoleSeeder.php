<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AssignUserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // ========================
        // FINANCE
        // ========================
        User::where('email', 'budi.finance@mail.com')->first()?->syncRoles(['head']);
        User::where('email', 'andi.finance@mail.com')->first()?->syncRoles(['head']);
        User::where('email', 'rina.finance@mail.com')->first()?->syncRoles(['employee']);

        // ========================
        // IT
        // ========================
        User::where('email', 'dedi.it@mail.com')->first()?->syncRoles(['head']);
        User::where('email', 'maya.it@mail.com')->first()?->syncRoles(['head']);
        User::where('email', 'rizky.it@mail.com')->first()?->syncRoles(['employee']);

        // ========================
        // HR
        // ========================
        User::where('email', 'siti.hr@mail.com')->first()?->syncRoles(['hr']);
        User::where('email', 'fajar.hr@mail.com')->first()?->syncRoles(['hr']);
    }
}
