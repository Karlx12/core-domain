<?php

namespace IncadevUns\CoreDomain\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use IncadevUns\CoreDomain\Enums\ContentStatus;
use IncadevUns\CoreDomain\Enums\ContentType;
use IncadevUns\CoreDomain\Enums\NewsCategory;
use IncadevUns\CoreDomain\Enums\TicketPriority;
use IncadevUns\CoreDomain\Enums\TicketStatus;
use IncadevUns\CoreDomain\Enums\TicketType;
use IncadevUns\CoreDomain\Models\ContentItem;
use IncadevUns\CoreDomain\Models\SecuritySetting;
use IncadevUns\CoreDomain\Models\Ticket;
use IncadevUns\CoreDomain\Models\TicketReply;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder se encarga de configurar aspectos tecnológicos del sistema:
     * - Asignación de permisos al rol admin
     * - Asignación de permisos de soporte técnico a roles
     * - Asignación de permisos de seguridad a roles
     * - Configuración de seguridad del sistema
     * - Contenido de prueba (noticias, anuncios, alertas)
     * - Datos de muestra para el módulo de soporte técnico
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🔧 Ejecutando TechnologySeeder...');
        $this->command->info('');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->assignAdminPermissions();
        $this->assignSupportTechnicalPermissions();
        $this->assignSecurityPermissions();
        $this->createSecuritySettings();
        $this->seedContentItems();
        $this->seedSupportTechnicalSampleData();

        $this->command->info('');
        $this->command->info('✅ TechnologySeeder completado exitosamente!');
    }

    /**
     * Asignar todos los permisos al rol admin
     */
    private function assignAdminPermissions(): void
    {
        $this->command->info('🔐 Asignando permisos al rol admin...');

        // Obtener el rol admin
        $adminRole = Role::where('name', 'admin')->first();

        if (! $adminRole) {
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

        $this->command->info('🔄 Asignando '.$allPermissions->count().' permisos al rol admin...');

        // Asignar TODOS los permisos al rol admin
        $adminRole->syncPermissions($allPermissions);

        $this->command->info('✅ Todos los permisos han sido asignados exitosamente al rol admin!');
        $this->command->info('');
        $this->command->info('📊 Resumen:');
        $this->command->info('   - Rol: admin');
        $this->command->info('   - Total de permisos asignados: '.$allPermissions->count());
        $this->command->info('');
    }

    /**
     * Asignar permisos del módulo de soporte técnico a los roles
     */
    private function assignSupportTechnicalPermissions(): void
    {
        $this->command->info('🎫 Asignando permisos de soporte técnico a roles...');

        // Obtener roles
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $supportRole = Role::where('name', 'support')->first();

        // Obtener todos los roles regulares (excluyendo admin, super_admin, y support)
        $regularRoles = Role::whereNotIn('name', ['admin', 'super_admin', 'support'])->get();

        // Permisos de tickets
        $ticketPermissions = [
            'tickets.view-any',
            'tickets.view',
            'tickets.create',
            'tickets.update',
            'tickets.delete',
        ];

        // Permisos de respuestas
        $replyPermissions = [
            'ticket-replies.create',
            'ticket-replies.update',
            'ticket-replies.delete',
        ];

        // Permisos de adjuntos
        $attachmentPermissions = [
            'reply-attachments.delete',
        ];

        $allTicketPermissions = array_merge($ticketPermissions, $replyPermissions, $attachmentPermissions);

        // Asignar permisos a super_admin (todos los permisos)
        if ($superAdminRole) {
            foreach ($allTicketPermissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $superAdminRole->givePermissionTo($perm);
                }
            }
        }

        // Asignar permisos a admin (todos los permisos)
        if ($adminRole) {
            foreach ($allTicketPermissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $adminRole->givePermissionTo($perm);
                }
            }
        }

        // Asignar permisos a support (todos los permisos)
        if ($supportRole) {
            foreach ($allTicketPermissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $supportRole->givePermissionTo($perm);
                }
            }
        }

        // Asignar permisos básicos a roles regulares (solo crear y ver sus propios tickets)
        $regularUserPermissions = [
            'tickets.view',
            'tickets.create',
            'ticket-replies.create',
        ];

        foreach ($regularRoles as $role) {
            foreach ($regularUserPermissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $role->givePermissionTo($perm);
                }
            }
        }

        $this->command->info('✓ Permisos del módulo SupportTechnical asignados correctamente');
        $this->command->info('  - super_admin: Todos los permisos');
        $this->command->info('  - admin: Todos los permisos');
        $this->command->info('  - support: Todos los permisos');
        $this->command->info('  - Roles regulares ('.$regularRoles->count().'): tickets.view, tickets.create, ticket-replies.create');
        $this->command->info('');
    }

    /**
     * Asignar permisos del módulo de seguridad a los roles
     */
    private function assignSecurityPermissions(): void
    {
        $this->command->info('🔒 Asignando permisos de seguridad a roles...');

        // Permisos básicos (para usuarios normales)
        $basicPermissions = [
            'security-dashboard.view',
            'sessions.view',
            'sessions.terminate',
            'tokens.view',
            'tokens.revoke',
            'security-events.view',
        ];

        // Permisos administrativos (para rol security)
        $adminPermissions = [
            'security-dashboard.view-any',
            'sessions.view-any',
            'sessions.terminate-any',
            'tokens.view-any',
            'tokens.revoke-any',
            'security-events.view-any',
            'security-events.export',
            'security-alerts.view',
            'security-alerts.resolve',
            'security-users.view',
            'security-users.block',
            'security-users.unblock',
        ];

        $allSecurityPermissions = array_merge($basicPermissions, $adminPermissions);

        // Obtener o crear rol security
        $securityRole = Role::where('name', 'security')->first();

        if ($securityRole) {
            // Asignar TODOS los permisos al rol security
            foreach ($allSecurityPermissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $securityRole->givePermissionTo($perm);
                }
            }
            $this->command->info('✅ Rol "security" tiene acceso global al módulo de seguridad');
        }

        // Asignar permisos al rol admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($allSecurityPermissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $adminRole->givePermissionTo($perm);
                }
            }
            $this->command->info('✅ Rol "admin" tiene acceso completo al módulo de seguridad');
        }

        // Asignar permisos al rol super_admin
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            foreach ($allSecurityPermissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $superAdminRole->givePermissionTo($perm);
                }
            }
            $this->command->info('✅ Rol "super_admin" tiene acceso completo al módulo de seguridad');
        }

        $this->command->info('');
    }

    /**
     * Crear permisos de seguridad adicionales
     */
    private function createSecurityPermissions(): void
    {
        $permissions = [
            // Permisos de bloqueos de usuarios
            'user-blocks.view-any',
            'user-blocks.create',
            'user-blocks.delete',

            // Permisos de configuración de seguridad
            'security-settings.view',
            'security-settings.update',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->info('✓ Permisos de seguridad creados correctamente.');
    }

    /**
     * Crear configuraciones de seguridad del sistema
     */
    private function createSecuritySettings(): void
    {
        $this->command->info('⚙️ Creando configuraciones de seguridad...');

        // Crear permisos de seguridad adicionales
        $this->createSecurityPermissions();

        // Asignar permisos a roles
        $securityPermissions = [
            'user-blocks.view-any',
            'user-blocks.create',
            'user-blocks.delete',
            'security-settings.view',
            'security-settings.update',
            'sessions.view-any',
            'sessions.terminate-any',
            'security-events.view-any',
        ];

        $securityRole = Role::where('name', 'security')->first();
        if ($securityRole) {
            foreach ($securityPermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && ! $securityRole->hasPermissionTo($permissionName)) {
                    $securityRole->givePermissionTo($permission);
                }
            }
        }

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($securityPermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && ! $adminRole->hasPermissionTo($permissionName)) {
                    $adminRole->givePermissionTo($permission);
                }
            }
        }

        // Crear configuraciones de seguridad
        $settings = [
            [
                'key' => 'max_failed_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Número máximo de intentos fallidos de login antes de bloquear al usuario',
                'group' => 'login',
            ],
            [
                'key' => 'failed_login_window_minutes',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Ventana de tiempo (en minutos) para contar los intentos fallidos de login',
                'group' => 'login',
            ],
            [
                'key' => 'block_duration_minutes',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Duración del bloqueo automático (en minutos) cuando se exceden los intentos fallidos',
                'group' => 'blocking',
            ],
            [
                'key' => 'session_timeout_minutes',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Tiempo de inactividad (en minutos) antes de considerar una sesión como inactiva',
                'group' => 'sessions',
            ],
            [
                'key' => 'max_concurrent_sessions',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Número máximo de sesiones concurrentes permitidas por usuario',
                'group' => 'sessions',
            ],
            [
                'key' => 'detect_multiple_ips',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Habilitar detección de logins desde múltiples IPs en poco tiempo',
                'group' => 'anomaly_detection',
            ],
            [
                'key' => 'multiple_ip_window_minutes',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Ventana de tiempo (en minutos) para detectar logins desde múltiples IPs',
                'group' => 'anomaly_detection',
            ],
        ];

        foreach ($settings as $setting) {
            SecuritySetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✓ Configuraciones de seguridad creadas correctamente.');
        $this->command->info('');
    }

    /**
     * Crear contenido de prueba (noticias, anuncios, alertas)
     */
    private function seedContentItems(): void
    {
        $this->command->info('📰 Creando contenido de prueba...');

        $contentItems = [
            // NEWS 1 - Tecnología
            [
                'content_type' => ContentType::NEWS->value,
                'title' => 'Nueva plataforma de aprendizaje en línea revoluciona la educación',
                'slug' => 'nueva-plataforma-aprendizaje-revoluciona-educacion',
                'content' => 'Una nueva era en la educación digital

La plataforma Incadev presenta su nuevo sistema de gestión de aprendizaje que integra inteligencia artificial para personalizar la experiencia educativa de cada estudiante.

Características principales:

- Análisis predictivo de deserción estudiantil
- Chatbot inteligente con IA para soporte 24/7
- Dashboard interactivo para estudiantes y profesores
- Sistema de evaluación automática

Esta innovación marca un antes y un después en cómo se imparte la educación en línea.',
                'summary' => 'Incadev lanza su nueva plataforma educativa con IA integrada que promete transformar la experiencia de aprendizaje.',
                'image_url' => 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=1200&h=800&fit=crop',
                'status' => ContentStatus::PUBLISHED->value,
                'views' => 1234,
                'priority' => 1,
                'published_date' => now()->subDays(2),
                'category' => NewsCategory::EDUCATION->value,
                'seo_title' => 'Nueva Plataforma Educativa con IA - Incadev',
                'seo_description' => 'Descubre cómo la nueva plataforma de Incadev está revolucionando la educación en línea con inteligencia artificial.',
                'metadata' => [
                    'author' => 'Equipo Incadev',
                    'reading_time' => '3 min',
                    'tags' => ['educación', 'tecnología', 'IA', 'e-learning'],
                ],
            ],
            // NEWS 2 - Tecnología
            [
                'content_type' => ContentType::NEWS->value,
                'title' => 'Inteligencia Artificial en la educación: El futuro es ahora',
                'slug' => 'inteligencia-artificial-educacion-futuro',
                'content' => 'La IA transforma el aula

Los sistemas de inteligencia artificial están cambiando radicalmente la forma en que los estudiantes aprenden y los profesores enseñan.

Desde tutores virtuales hasta sistemas de evaluación automática, la IA está democratizando el acceso a educación de calidad.

"La tecnología no reemplaza al profesor, lo potencia" - Dr. Juan Pérez, experto en EdTech

El impacto de estas tecnologías se refleja en mejores tasas de retención estudiantil y mayor satisfacción tanto de alumnos como de docentes.',
                'summary' => 'La inteligencia artificial está revolucionando la educación con soluciones innovadoras para estudiantes y profesores.',
                'image_url' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=1200&h=800&fit=crop',
                'status' => ContentStatus::PUBLISHED->value,
                'views' => 856,
                'priority' => 2,
                'published_date' => now()->subDays(5),
                'category' => NewsCategory::TECHNOLOGY->value,
                'seo_title' => 'IA en la Educación: Transformando el Aprendizaje',
                'seo_description' => 'Conoce cómo la inteligencia artificial está revolucionando la educación moderna.',
                'metadata' => [
                    'author' => 'María González',
                    'reading_time' => '5 min',
                    'tags' => ['IA', 'educación', 'tecnología', 'futuro'],
                ],
            ],
            // NEWS 3 - Educación
            [
                'content_type' => ContentType::NEWS->value,
                'title' => 'Mejores prácticas para el aprendizaje en línea exitoso',
                'slug' => 'mejores-practicas-aprendizaje-en-linea',
                'content' => 'Consejos para maximizar tu experiencia de aprendizaje

El aprendizaje en línea requiere disciplina y estrategia. Aquí te compartimos las mejores prácticas:

1. Crea un espacio dedicado para estudiar
2. Establece un horario regular
3. Participa activamente en foros y discusiones
4. Toma descansos regulares
5. Conecta con tus compañeros

Siguiendo estos consejos, podrás aprovechar al máximo tu experiencia educativa en línea. La clave está en la constancia y en mantener una rutina que te permita balancear tus estudios con otras actividades.',
                'summary' => 'Descubre las estrategias comprobadas para tener éxito en tu educación en línea.',
                'image_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=1200&h=800&fit=crop',
                'status' => ContentStatus::PUBLISHED->value,
                'views' => 645,
                'priority' => 3,
                'published_date' => now()->subWeek(),
                'category' => NewsCategory::EDUCATION->value,
                'seo_title' => 'Guía Completa para el Aprendizaje en Línea Exitoso',
                'seo_description' => 'Aprende las mejores prácticas y estrategias para destacar en tu educación en línea.',
                'metadata' => [
                    'author' => 'Carlos Rodríguez',
                    'reading_time' => '4 min',
                    'tags' => ['educación', 'tips', 'e-learning', 'estudiantes'],
                ],
            ],
            // NEWS 4 - Negocios
            [
                'content_type' => ContentType::NEWS->value,
                'title' => 'Incadev alcanza 10,000 estudiantes activos',
                'slug' => 'incadev-10000-estudiantes-activos',
                'content' => 'Un hito importante para nuestra comunidad

Estamos orgullosos de anunciar que hemos alcanzado los 10,000 estudiantes activos en nuestra plataforma.

Este logro representa el compromiso de nuestra comunidad con la educación de calidad y el aprendizaje continuo.

Gracias a todos nuestros estudiantes, profesores y colaboradores por hacer esto posible. Este es solo el comienzo de un camino que esperamos nos lleve a impactar positivamente la vida de miles de personas más.',
                'summary' => 'Incadev celebra un hito importante al alcanzar 10,000 estudiantes activos en su plataforma.',
                'image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1200&h=800&fit=crop',
                'status' => ContentStatus::PUBLISHED->value,
                'views' => 2341,
                'priority' => 1,
                'published_date' => now()->subDays(1),
                'category' => NewsCategory::BUSINESS->value,
                'seo_title' => 'Incadev Alcanza 10,000 Estudiantes Activos',
                'seo_description' => 'La plataforma educativa Incadev celebra un importante hito con 10,000 estudiantes activos.',
                'metadata' => [
                    'author' => 'Equipo Incadev',
                    'reading_time' => '2 min',
                    'tags' => ['hito', 'comunidad', 'logro', 'crecimiento'],
                ],
            ],
            // ANNOUNCEMENT 1
            [
                'content_type' => ContentType::ANNOUNCEMENT->value,
                'title' => 'Nuevos cursos disponibles en Programación Web',
                'slug' => 'nuevos-cursos-programacion-web',
                'content' => 'Nos complace anunciar que hemos agregado 5 nuevos cursos de programación web a nuestro catálogo:

- React Avanzado
- Node.js y Express
- Vue.js 3 desde cero
- TypeScript para desarrolladores
- Full Stack Developer Bootcamp

Las inscripciones están abiertas. ¡No te pierdas esta oportunidad de potenciar tus habilidades como desarrollador web!',
                'summary' => 'Inscríbete ahora en nuestros nuevos cursos de programación web y lleva tus habilidades al siguiente nivel.',
                'image_url' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=1200&h=800&fit=crop',
                'status' => ContentStatus::ACTIVE->value,
                'views' => 523,
                'priority' => 1,
                'start_date' => now()->subDays(3),
                'end_date' => now()->addWeeks(2),
                'link_url' => '/courses',
                'link_text' => 'Ver cursos disponibles',
                'button_text' => 'Inscribirme ahora',
                'metadata' => [
                    'background_color' => '#4F46E5',
                    'text_color' => '#FFFFFF',
                ],
            ],
            // ANNOUNCEMENT 2
            [
                'content_type' => ContentType::ANNOUNCEMENT->value,
                'title' => 'Mantenimiento programado del sistema',
                'slug' => 'mantenimiento-programado-sistema',
                'content' => 'Informamos que realizaremos un mantenimiento programado de nuestros servidores el próximo domingo 25 de noviembre de 2:00 AM a 6:00 AM.

Durante este periodo, la plataforma no estará disponible.

Pedimos disculpas por las molestias y agradecemos su comprensión. Este mantenimiento nos permitirá mejorar el rendimiento y la seguridad de la plataforma.',
                'summary' => 'Mantenimiento del sistema el domingo 25 de noviembre de 2:00 AM a 6:00 AM.',
                'image_url' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=1200&h=800&fit=crop',
                'status' => ContentStatus::ACTIVE->value,
                'views' => 892,
                'priority' => 2,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(5),
                'link_url' => '/support',
                'link_text' => 'Más información',
                'button_text' => 'Contactar soporte',
                'metadata' => [
                    'background_color' => '#F59E0B',
                    'text_color' => '#000000',
                ],
            ],
            // ALERT 1
            [
                'content_type' => ContentType::ALERT->value,
                'title' => 'Actualiza tu perfil para mejorar tu experiencia',
                'slug' => 'actualiza-perfil-mejora-experiencia',
                'content' => 'Hemos detectado que tu perfil está incompleto. Actualiza tu información para obtener recomendaciones personalizadas de cursos y una mejor experiencia en la plataforma.',
                'summary' => 'Completa tu perfil para recibir recomendaciones personalizadas.',
                'image_url' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1200&h=800&fit=crop',
                'status' => ContentStatus::ACTIVE->value,
                'views' => 234,
                'priority' => 3,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'item_type' => 'info',
                'link_url' => '/profile/edit',
                'link_text' => 'Actualizar perfil',
                'button_text' => 'Ir a mi perfil',
                'metadata' => [
                    'dismissible' => true,
                    'icon' => 'info',
                ],
            ],
            // ALERT 2
            [
                'content_type' => ContentType::ALERT->value,
                'title' => '¡Últimos días para inscribirte con descuento!',
                'slug' => 'ultimos-dias-inscripcion-descuento',
                'content' => 'Aprovecha el 30% de descuento en todos nuestros cursos. La promoción termina el 30 de noviembre.

¡No dejes pasar esta oportunidad de invertir en tu educación!',
                'summary' => '30% de descuento en todos los cursos hasta el 30 de noviembre.',
                'image_url' => 'https://images.unsplash.com/photo-1607703703674-df96af81dffa?w=1200&h=800&fit=crop',
                'status' => ContentStatus::ACTIVE->value,
                'views' => 1567,
                'priority' => 1,
                'start_date' => now()->subWeek(),
                'end_date' => now()->addWeek(),
                'item_type' => 'warning',
                'link_url' => '/courses?promo=true',
                'link_text' => 'Ver cursos en promoción',
                'button_text' => 'Aprovechar descuento',
                'metadata' => [
                    'dismissible' => false,
                    'icon' => 'megaphone',
                    'promo_code' => 'PROMO30',
                ],
            ],
            // NEWS 5 - Ciencia
            [
                'content_type' => ContentType::NEWS->value,
                'title' => 'El aprendizaje adaptativo: ciencia detrás de la educación personalizada',
                'slug' => 'aprendizaje-adaptativo-ciencia-educacion',
                'content' => 'La neurociencia aplicada a la educación

Estudios recientes demuestran que el aprendizaje adaptativo puede mejorar la retención de conocimiento hasta en un 60%.

La clave está en ajustar el contenido y el ritmo de aprendizaje según las necesidades individuales de cada estudiante.

Beneficios comprobados:

- Mayor retención de información
- Reducción del estrés académico
- Mejor rendimiento en evaluaciones
- Mayor satisfacción estudiantil

Los investigadores coinciden en que este enfoque representa el futuro de la educación personalizada.',
                'summary' => 'Investigaciones científicas revelan los beneficios del aprendizaje adaptativo en la educación moderna.',
                'image_url' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=1200&h=800&fit=crop',
                'status' => ContentStatus::PUBLISHED->value,
                'views' => 423,
                'priority' => 4,
                'published_date' => now()->subDays(10),
                'category' => NewsCategory::SCIENCE->value,
                'seo_title' => 'Aprendizaje Adaptativo: La Ciencia de la Educación Personalizada',
                'seo_description' => 'Descubre cómo la ciencia respalda el aprendizaje adaptativo y sus beneficios comprobados.',
                'metadata' => [
                    'author' => 'Dr. Ana Martínez',
                    'reading_time' => '6 min',
                    'tags' => ['ciencia', 'neurociencia', 'aprendizaje', 'investigación'],
                ],
            ],
            // NEWS 6 - Salud
            [
                'content_type' => ContentType::NEWS->value,
                'title' => 'Salud mental y educación en línea: Consejos para estudiantes',
                'slug' => 'salud-mental-educacion-en-linea',
                'content' => 'Cuida tu bienestar mientras estudias

La educación en línea trae muchos beneficios, pero también nuevos desafíos para la salud mental de los estudiantes.

Estrategias para mantener el equilibrio:

1. Establece límites entre estudio y tiempo personal
2. Mantén contacto social con compañeros
3. Practica ejercicio regularmente
4. Duerme suficiente
5. No dudes en pedir ayuda si la necesitas

Recuerda: tu salud mental es tan importante como tu rendimiento académico. Cuidarte a ti mismo es parte fundamental del proceso de aprendizaje.',
                'summary' => 'Consejos prácticos para cuidar tu salud mental mientras estudias en línea.',
                'image_url' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=1200&h=800&fit=crop',
                'status' => ContentStatus::PUBLISHED->value,
                'views' => 789,
                'priority' => 2,
                'published_date' => now()->subDays(4),
                'category' => NewsCategory::HEALTH->value,
                'seo_title' => 'Salud Mental en la Educación en Línea: Guía Práctica',
                'seo_description' => 'Aprende a cuidar tu salud mental mientras estudias en línea con estos consejos prácticos.',
                'metadata' => [
                    'author' => 'Lic. Patricia Vega',
                    'reading_time' => '4 min',
                    'tags' => ['salud mental', 'bienestar', 'estudiantes', 'consejos'],
                ],
            ],
        ];

        foreach ($contentItems as $item) {
            ContentItem::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }

        $this->command->info('✓ 10 items de contenido creados exitosamente:');
        $this->command->info('   - 6 Noticias (NEWS)');
        $this->command->info('   - 2 Anuncios (ANNOUNCEMENT)');
        $this->command->info('   - 2 Alertas (ALERT)');
        $this->command->info('');
    }

    /**
     * Generar datos de muestra para el módulo de soporte técnico
     */
    private function seedSupportTechnicalSampleData(): void
    {
        $this->command->info('🎫 Generando datos de muestra para SupportTechnical...');

        $userModelClass = config('auth.providers.users.model', 'App\Models\User');

        // Obtener usuarios con diferentes roles
        $regularUsers = $userModelClass::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['admin', 'super_admin', 'support']);
        })->limit(5)->get();

        $supportUsers = $userModelClass::whereHas('roles', function ($query) {
            $query->whereIn('name', ['support', 'admin']);
        })->limit(2)->get();

        if ($regularUsers->isEmpty()) {
            $this->command->error('✗ No se encontraron usuarios regulares (sin roles admin, super_admin o support)');
            $this->command->warn('Por favor, ejecuta primero el seeder de usuarios');

            return;
        }

        if ($supportUsers->isEmpty()) {
            $this->command->warn('⚠ No se encontraron usuarios con rol "support" o "admin"');
            $this->command->info('Los tickets se crearán sin respuestas de soporte.');
        }

        DB::transaction(function () use ($regularUsers, $supportUsers) {
            $ticketsCreated = 0;
            $repliesCreated = 0;

            // Datos de muestra de tickets
            $ticketSamples = [
                // OPEN tickets
                [
                    'title' => 'No puedo acceder al sistema LMS',
                    'description' => 'Desde esta mañana no puedo ingresar al sistema LMS. Me aparece un error de "Credenciales inválidas" aunque estoy usando mi contraseña correcta.',
                    'type' => TicketType::Technical,
                    'priority' => TicketPriority::High,
                    'status' => TicketStatus::Open,
                    'replies_count' => 0,
                ],
                [
                    'title' => 'Solicitud de certificado académico',
                    'description' => 'Necesito un certificado de estudios para presentar en mi nuevo trabajo. ¿Cómo puedo solicitarlo?',
                    'type' => TicketType::Academic,
                    'priority' => TicketPriority::Medium,
                    'status' => TicketStatus::Open,
                    'replies_count' => 1,
                ],
                [
                    'title' => '¿Cómo exportar reportes a Excel?',
                    'description' => 'Necesito saber cómo puedo exportar los reportes del módulo de análisis de datos a formato Excel. No encuentro la opción.',
                    'type' => TicketType::Inquiry,
                    'priority' => TicketPriority::Low,
                    'status' => TicketStatus::Open,
                    'replies_count' => 2,
                ],
                // PENDING tickets
                [
                    'title' => 'Error al subir archivos grandes',
                    'description' => 'Cuando intento subir archivos mayores a 10MB, el sistema se queda cargando y eventualmente da timeout.',
                    'type' => TicketType::Technical,
                    'priority' => TicketPriority::High,
                    'status' => TicketStatus::Pending,
                    'replies_count' => 3,
                ],
                [
                    'title' => 'Actualización de datos personales',
                    'description' => 'Necesito actualizar mi dirección y número de teléfono en el sistema administrativo.',
                    'type' => TicketType::Administrative,
                    'priority' => TicketPriority::Low,
                    'status' => TicketStatus::Pending,
                    'replies_count' => 2,
                ],
                // CLOSED tickets
                [
                    'title' => 'No recibo notificaciones por correo',
                    'description' => 'Configuré las notificaciones pero no me llegan los correos. Ya revisé mi bandeja de spam.',
                    'type' => TicketType::Technical,
                    'priority' => TicketPriority::Medium,
                    'status' => TicketStatus::Closed,
                    'replies_count' => 4,
                ],
                [
                    'title' => 'Solicitud de constancia de matrícula',
                    'description' => 'Por favor, necesito una constancia de matrícula vigente para el trámite de beca.',
                    'type' => TicketType::Academic,
                    'priority' => TicketPriority::Medium,
                    'status' => TicketStatus::Closed,
                    'replies_count' => 2,
                ],
                [
                    'title' => '¿Cómo cambiar mi contraseña?',
                    'description' => 'Necesito instrucciones para cambiar mi contraseña de acceso al sistema.',
                    'type' => TicketType::Inquiry,
                    'priority' => TicketPriority::Low,
                    'status' => TicketStatus::Closed,
                    'replies_count' => 1,
                ],
                // More OPEN tickets
                [
                    'title' => 'Dashboard no carga las estadísticas',
                    'description' => 'El dashboard principal se queda en blanco cuando intento ver las estadísticas del mes.',
                    'type' => TicketType::Technical,
                    'priority' => TicketPriority::High,
                    'status' => TicketStatus::Open,
                    'replies_count' => 0,
                ],
                [
                    'title' => 'Consulta sobre horarios de atención',
                    'description' => '¿Cuáles son los horarios de atención de la oficina de registro académico?',
                    'type' => TicketType::Inquiry,
                    'priority' => TicketPriority::Low,
                    'status' => TicketStatus::Open,
                    'replies_count' => 1,
                ],
            ];

            // Crear tickets
            foreach ($ticketSamples as $index => $ticketData) {
                $user = $regularUsers[$index % $regularUsers->count()];
                $repliesCount = $ticketData['replies_count'];
                unset($ticketData['replies_count']);

                // Crear ticket
                $ticket = Ticket::create([
                    'user_id' => $user->id,
                    'title' => $ticketData['title'],
                    'type' => $ticketData['type'],
                    'priority' => $ticketData['priority'],
                    'status' => $ticketData['status'],
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(0, 15)),
                ]);

                $ticketsCreated++;

                // Crear respuesta inicial (descripción del ticket)
                TicketReply::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'content' => $ticketData['description'],
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->created_at,
                ]);

                $repliesCreated++;

                // Crear respuestas adicionales si se especificaron
                if ($repliesCount > 0 && $supportUsers->isNotEmpty()) {
                    for ($i = 0; $i < $repliesCount; $i++) {
                        $isFromSupport = $i % 2 === 0;
                        $replyUser = $isFromSupport
                            ? $supportUsers[$i % $supportUsers->count()]
                            : $user;

                        $replyContent = $this->generateReplyContent($ticketData['type'], $isFromSupport, $i);

                        TicketReply::create([
                            'ticket_id' => $ticket->id,
                            'user_id' => $replyUser->id,
                            'content' => $replyContent,
                            'created_at' => $ticket->created_at->addHours(($i + 1) * 3),
                            'updated_at' => $ticket->created_at->addHours(($i + 1) * 3),
                        ]);

                        $repliesCreated++;
                    }
                }
            }

            $this->command->info("✓ {$ticketsCreated} tickets creados");
            $this->command->info("✓ {$repliesCreated} respuestas creadas");
        });

        $this->command->info('✓ Datos de muestra generados exitosamente');
        $this->command->info('');
    }

    /**
     * Generar contenido de respuesta apropiado basado en el tipo de ticket
     */
    private function generateReplyContent(TicketType $type, bool $isFromSupport, int $replyIndex): string
    {
        if ($isFromSupport) {
            $supportReplies = [
                TicketType::Technical->value => [
                    'Gracias por reportar el problema técnico. Nuestro equipo está investigando el caso.',
                    'Hemos identificado la causa del problema. Estamos trabajando en la solución.',
                    'El problema ha sido resuelto. Por favor, confirma si ahora funciona correctamente.',
                ],
                TicketType::Academic->value => [
                    'Recibimos tu solicitud académica. Estamos procesándola.',
                    'Tu solicitud ha sido aprobada y está en proceso.',
                    'La solicitud ha sido completada. Por favor, verifica.',
                ],
                TicketType::Administrative->value => [
                    'Tu solicitud administrativa está siendo revisada por el área correspondiente.',
                    'Hemos procesado tu solicitud. Te enviaremos la documentación por correo.',
                ],
                TicketType::Inquiry->value => [
                    'Gracias por tu consulta. Te proporciono la siguiente información:',
                    'Para realizar eso, debes seguir estos pasos: 1) Ir al menú principal, 2) Seleccionar la opción correspondiente.',
                ],
            ];

            $replies = $supportReplies[$type->value];

            return $replies[$replyIndex % count($replies)];
        } else {
            $userReplies = [
                'Gracias por la respuesta. Entiendo.',
                'Perfecto, ya probé y funciona correctamente.',
                '¿Podrían darme más detalles sobre esto?',
                'Muchas gracias por la ayuda.',
                'El problema persiste, aún no funciona.',
            ];

            return $userReplies[$replyIndex % count($userReplies)];
        }
    }
}
