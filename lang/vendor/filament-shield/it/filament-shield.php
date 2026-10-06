<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nome',
    'column.guard_name' => 'Nome Guard',
    'column.roles' => 'Ruoli',
    'column.permissions' => 'Permessi',
    'column.updated_at' => 'Aggiornato a',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nome',
    'field.guard_name' => 'Nome Guard',
    'field.permissions' => 'Permessi',
    'field.select_all.name' => 'Seleziona Tutto',
    'field.select_all.message' => 'Abilita tutti i Permessi attualmente <span class="text-primary font-medium">Abilitati</span> per questo ruolo',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Ruoli',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Ruolo',
    'resource.label.roles' => 'Ruoli',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entità',
    'resources' => 'Risorse',
    'widgets' => 'Widget',
    'pages' => 'Pagine',
    'custom' => 'Permessi Personalizzati',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Non hai i permessi di accesso',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Visualizza',
        'view_any' => 'Visualizza elenco',
        'create' => 'Crea',
        'update' => 'Modifica',
        'delete' => 'Elimina',
        'delete_any' => 'Elimina tutti',
        'force_delete' => 'Elimina definitivamente',
        'force_delete_any' => 'Elimina definitivamente tutti',
        'restore' => 'Ripristina',
        'replicate' => 'Duplica',
        'reorder' => 'Riordina',
        'restore_any' => 'Ripristina tutti',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pages & Widgets Labels
    |--------------------------------------------------------------------------
    */

    'view_backup' => 'Gestione backup',
    'view_dashboard' => 'Dashboard',
    'view_document_archive_statistics' => 'Statistiche archivio documentale',
    'view_documents_dashboard' => 'Dashboard documenti',
    'view_finance_dashboard' => 'Dashboard finanziaria',
    'view_members_dashboard' => 'Dashboard soci',
    'view_service_status' => 'Stato servizi',
    'view_services_dashboard' => 'Dashboard servizi',
    'view_teaching_dashboard' => 'Dashboard didattica',
];