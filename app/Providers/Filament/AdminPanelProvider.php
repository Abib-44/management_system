<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Backup;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\DocumentsDashboard;
use App\Filament\Pages\FinanceDashboard;
use App\Filament\Pages\SchoolFinanceDashboard;
use App\Filament\Pages\MembersDashboard;
use App\Filament\Pages\ServicesDashboard;
use App\Filament\Pages\TeachingAgenda;
use App\Filament\Pages\TeachingDashboard;
use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\Attendances\AttendanceResource;
use App\Filament\Resources\AuditLogs\AuditLogResource;
use App\Filament\Resources\ClassRooms\ClassRoomResource;
use App\Filament\Resources\DocumentArchives\DocumentArchiveResource;
use App\Filament\Resources\FinancialTransactions\FinancialTransactionResource;
use App\Filament\Resources\Grades\GradeResource;
use App\Filament\Resources\Lessons\LessonResource;
use App\Filament\Resources\Materials\MaterialResource;
use App\Filament\Resources\Members\MemberResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\SchoolYears\SchoolYearResource;
use App\Filament\Resources\ServiceAssignments\ServiceAssignmentResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\ServiceStatus;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AdminPanelProvider extends PanelProvider
{
    protected function navigationMap(): array
    {
        return [
            'Didattica' => [
                TeachingDashboard::class => [
                    'label' => 'Panoramica',
                    'icon' => 'heroicon-o-chart-bar-square',
                ],

                StudentResource::class => [
                    'label' => 'Studenti',
                    'icon' => 'heroicon-o-users',
                ],

                ClassRoomResource::class => [
                    'label' => 'Classi',
                    'icon' => 'heroicon-o-building-office-2',
                ],

                GradeResource::class => [
                    'label' => 'Voti',
                    'icon' => 'heroicon-o-star',
                ],

                LessonResource::class => [
                    'label' => 'Lezioni',
                    'icon' => 'heroicon-o-book-open',
                ],

                AttendanceResource::class => [
                    'label' => 'Presenze',
                    'icon' => 'heroicon-o-clipboard-document-check',
                ],

                SchoolYearResource::class => [
                    'label' => 'Anni scolastici',
                    'icon' => 'heroicon-o-calendar-days',
                ],

                MaterialResource::class => [
                    'label' => 'Materiali',
                    'icon' => 'heroicon-o-cube',
                ],

                TeachingAgenda::class => [
                    'label' => 'Agenda',
                    'icon' => 'heroicon-o-calendar',
                ],
            ],

            'Membri' => [
                MembersDashboard::class => [
                    'label' => 'Panoramica',
                    'icon' => 'heroicon-o-chart-bar-square',
                ],

                MemberResource::class => [
                    'label' => 'Membri',
                    'icon' => 'heroicon-o-user-group',
                ],
            ],

            'Servizi' => [
                ServicesDashboard::class => [
                    'label' => 'Panoramica',
                    'icon' => 'heroicon-o-chart-bar-square',
                ],

                ServiceAssignmentResource::class => [
                    'label' => 'Servizi',
                    'icon' => 'heroicon-o-wrench-screwdriver',
                ],
            ],

            'Finanze' => [
                FinanceDashboard::class => [
                    'label' => 'Panoramica',
                    'icon' => 'heroicon-o-chart-bar-square',
                ],

                SchoolFinanceDashboard::class => [
                    'label' => 'Panoramica scuola',
                    'icon' => 'heroicon-o-chart-bar-square',
                ],

                FinancialTransactionResource::class => [
                    'label' => 'Movimenti finanziari',
                    'icon' => 'heroicon-o-banknotes',
                ],
            ],

            'Documenti' => [
                DocumentsDashboard::class => [
                    'label' => 'Panoramica',
                    'icon' => 'heroicon-o-chart-bar-square',
                ],

                DocumentArchiveResource::class => [
                    'label' => 'Archivio documenti',
                    'icon' => 'heroicon-o-folder-open',
                ],
            ],

            'Sistema' => [
                RoleResource::class => [
                    'label' => 'Ruoli',
                    'icon' => 'heroicon-o-shield-check',
                ],

                UserResource::class => [
                    'label' => 'Utenti',
                    'icon' => 'heroicon-o-users',
                ],

                AuditLogResource::class => [
                    'label' => 'Registro attività',
                    'icon' => 'heroicon-o-document-magnifying-glass',
                ],

                ActivityResource::class => [
                    'label' => 'Attività',
                    'icon' => 'heroicon-o-bolt',
                ],

                Backup::class => [
                    'label' => 'Backup',
                    'icon' => 'heroicon-o-circle-stack',
                ],
            ],
        ];
    }

    protected function topLevelNavigationMap(): array
    {
        return [];
    }

    protected function buildNavigationGroups(): array
    {
        return collect($this->navigationMap())
            ->map(function (array $resources, string $groupName) {
                $items = collect($resources)
                    ->flatMap(
                        fn (array $config, string $class) => $this->labeledNavigationItems(
                            $class,
                            $config
                        )
                    )
                    ->values()
                    ->all();

                if ($items === []) {
                    return null;
                }

                return NavigationGroup::make($groupName)
                    ->collapsed()
                    ->items($items);
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function buildTopLevelNavigationItems(): array
    {
        return collect($this->topLevelNavigationMap())
            ->flatMap(
                fn (array $config, string $class) => $this->labeledNavigationItems(
                    $class,
                    $config
                )
            )
            ->values()
            ->all();
    }

    protected function labeledNavigationItems(
        string $class,
        array $config
    ): array {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        if (is_subclass_of($class, Resource::class)) {
            $resource = class_basename($class);

            if (str_ends_with($resource, 'Resource')) {
                $resource = substr($resource, 0, -8);
            }

            $permission = "ViewAny:{$resource}";
        } else {
            $permission = 'View:' . class_basename($class);
        }

        if (! $user->can($permission)) {
            return [];
        }

        return collect($class::getNavigationItems())
            ->filter(
                fn (NavigationItem $item): bool => $item->isVisible()
            )
            ->map(
                fn (NavigationItem $item) => $item
                    ->label($config['label'])
                    ->icon($config['icon'])
            )
            ->values()
            ->all();
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()

            ->plugins([
                FilamentShieldPlugin::make()
                    ->localizePermissionLabels()
                    ->gridColumns([
                        'default' => 1,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                    ]),

                FilamentFullCalendarPlugin::make(),
            ])

->renderHook(
    PanelsRenderHook::HEAD_END,
    fn (): HtmlString => new HtmlString(
        '<link rel="stylesheet" href="/css/login-mobile.css?v='
        . filemtime(public_path('css/login-mobile.css'))
        . '">'
    ),
)

            ->renderHook(
                'panels::auth.login.form.before',
                fn (): string => <<<'HTML'
                    <div
                        dir="rtl"
                        style="
                            text-align: center;
                            font-size: 2rem;
                            font-weight: 700;
                            line-height: 1.2;
                            margin-bottom: 1.5rem;
                        "
                    >
                        مرحباً ، Benvenuto
                    </div>
                HTML
            )

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(
                    '<style>
                        .fi-simple-header-heading {
                            display: none !important;
                        }

                        .fi-sidebar-header img,
                        .fi-topbar img {
                            max-height: 2.5rem !important;
                            width: auto !important;
                        }
                    </style>'
                ),
            )

            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('11rem')

            ->favicon(asset('images/logo.png'))

            ->colors([
                'primary' => Color::Amber,
            ])

            ->navigation(
                fn (NavigationBuilder $builder): NavigationBuilder => $builder
                    ->items([
                        NavigationItem::make('Infrastruttura')
                            ->icon('heroicon-o-home')
                            ->url(Dashboard::getUrl())
                            ->visible(
                                fn (): bool => auth()->user()?->can('View:Dashboard') ?? false
                            ),

                        ...$this->buildTopLevelNavigationItems(),
                    ])
                    ->groups(
                        $this->buildNavigationGroups()
                    )
            )

            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\Filament\Resources'
            )

            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\Filament\Pages'
            )

            ->pages([
                Dashboard::class,
            ])

            ->widgets([
                ServiceStatus::class,
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}