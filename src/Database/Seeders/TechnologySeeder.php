<?php

namespace IncadevUns\CoreDomain\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder se encarga de configurar aspectos tecnológicos del sistema:
     * - Asignación de permisos al rol admin
     * - Configuraciones de infraestructura tecnológica
     * - Datos iniciales relacionados con tecnología
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🔧 Ejecutando TechnologySeeder...');
        $this->command->info('');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🔐 Asignando permisos al rol admin...');

        // Obtener el rol admin
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('❌ El rol "admin" no existe. Por favor, créalo primero.');
            return;
        }

        $this->command->info('✅ Rol admin encontrado!');

        // Obtener TODOS los permisos de la base de datos
        $allPermissions = Permission::all();

        if ($allPermissions->isEmpty()) {
            $this->command->error('❌ No hay permisos en la base de datos. Ejecuta primero el PermissionsSeeder.');
            return;
        }

        $this->command->info('🔄 Asignando ' . $allPermissions->count() . ' permisos al rol admin...');

        // Asignar TODOS los permisos al rol admin
        $adminRole->syncPermissions($allPermissions);

        $this->command->info('✅ Todos los permisos han sido asignados exitosamente al rol admin!');
        $this->command->info('');
        $this->command->info('📊 Resumen:');
        $this->command->info('   - Rol: admin');
        $this->command->info('   - Total de permisos asignados: ' . $allPermissions->count());
        $this->command->info('');

        // Mostrar algunos permisos como ejemplo
        $this->command->info('📝 Algunos permisos asignados:');
        $samplePermissions = $allPermissions->take(10);
        foreach ($samplePermissions as $permission) {
            $this->command->info('   ✓ ' . $permission->name);
        }
        if ($allPermissions->count() > 10) {
            $this->command->info('   ... y ' . ($allPermissions->count() - 10) . ' más.');
        }

        $this->command->info('');
        $this->command->info('✅ TechnologySeeder completado exitosamente!');
    }
}
