<?php
/**
 * 👤 USER SEEDER
 * Seeder pour créer les rôles, permissions et utilisateurs de base.
 * Fichier : database/seeders/UserSeeder.php
 */

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Rôles correspondant à l'enum 'role' de la table users
        $roles = [
            'administrateur' => 'Administrateur',
            'responsable_commercial' => 'Responsable Commercial',
            'vendeur' => 'Vendeur',
            'magasinier' => 'Magasinier',
            'responsable_achats' => 'Responsable Achats',
            'comptable' => 'Comptable',
            'caissiere' => 'Caissière',
            'invite' => 'Invité/Stagiaire',
        ];

        foreach ($roles as $name => $display_name) {
            Role::firstOrCreate(['name' => $name], ['guard_name' => 'web']);
        }

        // Permissions
        $permissions = [
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'sales.view', 'sales.create', 'sales.edit', 'sales.delete',
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.delete',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
            'reports.view', 'reports.export',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'settings.view', 'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission], ['guard_name' => 'web']);
        }

        // Attribution des permissions
        Role::findByName('administrateur')->givePermissionTo(Permission::all());

        Role::findByName('responsable_commercial')->givePermissionTo([
            'products.view', 'customers.view', 'customers.create', 'customers.edit',
            'sales.view', 'sales.create', 'reports.view', 'inventory.view'
        ]);

        Role::findByName('vendeur')->givePermissionTo([
            'products.view', 'customers.view', 'customers.create',
            'sales.view', 'sales.create'
        ]);

        // Admin principal
        $admin = User::firstOrCreate(
            ['email' => 'admin@stockguardian.ci'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'phone' => '+225 01 02 03 04 05',
                'role' => 'administrateur',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('administrateur');

        // Utilisateurs de test
        $testUsers = [
            ['name' => 'Marie Kouassi', 'email' => 'manager@stockguardian.ci', 'role' => 'responsable_commercial'],
            ['name' => 'Jean Ouattara', 'email' => 'seller@stockguardian.ci', 'role' => 'vendeur'],
            ['name' => 'Paul Yao', 'email' => 'stock@stockguardian.ci', 'role' => 'magasinier'],
            ['name' => 'Emma Diabate', 'email' => 'purchase@stockguardian.ci', 'role' => 'responsable_achats'],
            ['name' => 'Sophie Bamba', 'email' => 'accountant@stockguardian.ci', 'role' => 'comptable'],
            ['name' => 'Grace Kone', 'email' => 'cashier@stockguardian.ci', 'role' => 'caissiere'],
        ];

        foreach ($testUsers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'phone' => '+225 0' . rand(1, 9) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                    'role' => $data['role'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($data['role']);
        }

        // Utilisateurs aléatoires
        User::factory(15)->create()->each(function ($user) {
            $randomRole = collect(['vendeur', 'magasinier', 'caissiere'])->random();
            $user->assignRole($randomRole);
            $user->update(['role' => $randomRole]);
        });
    }
}
