<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\Attendances\AttendanceResource;
use App\Filament\Resources\AuditLogs\AuditLogResource;
use App\Filament\Resources\ClassRooms\ClassRoomResource;
use App\Filament\Resources\DocumentArchives\DocumentArchiveResource;
use App\Filament\Resources\FinancialCategories\FinancialCategoryResource;
use App\Filament\Resources\FinancialTransactions\FinancialTransactionResource;
use App\Filament\Resources\Grades\GradeResource;
use App\Filament\Resources\Lessons\LessonResource;
use App\Filament\Resources\Materials\MaterialResource;
use App\Filament\Resources\Members\MemberResource;
use App\Filament\Resources\MembershipFees\MembershipFeeResource;
use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Pages\ViewRole;
use App\Filament\Resources\SchoolYears\SchoolYearResource;
use App\Filament\Resources\ServiceAssignments\ServiceAssignmentResource;
use App\Filament\Resources\StudentPayments\StudentPaymentResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Users\UserResource;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    use HasShieldFormComponents;

    protected static ?string $model = Role::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Ruolo';

    protected static ?string $pluralModelLabel = 'Ruoli';

    protected static ?string $navigationLabel = 'Ruoli';

    protected static string|UnitEnum|null $navigationGroup = 'Sistema';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $slug = 'roles';

    protected static ?int $navigationSort = 100;

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule): Unique => $rule
                                    ->where('guard_name', 'web'),
                            ),

                        Select::make('guard_name')
                            ->label('Guard')
                            ->options([
                                'web' => 'web',
                            ])
                            ->default('web')
                            ->required()
                            ->disabled(),
                    ])
                    ->columnSpanFull(),

                Section::make('Permessi')
                    ->schema(
                        static::getResourceEntitiesSchema() ?? [],
                    )
                    ->columnSpanFull(),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GROUPS
    |--------------------------------------------------------------------------
    */

    protected static function shieldGroupMap(): array
    {
        return [
            StudentResource::class => 'Didattica',
            ClassRoomResource::class => 'Didattica',
            LessonResource::class => 'Didattica',
            AttendanceResource::class => 'Didattica',
            GradeResource::class => 'Didattica',
            MaterialResource::class => 'Didattica',
            SchoolYearResource::class => 'Didattica',

            MemberResource::class => 'Soci',
            MembershipFeeResource::class => 'Soci',
            ActivityResource::class => 'Soci',

            FinancialCategoryResource::class => 'Amministrazione',
            FinancialTransactionResource::class => 'Amministrazione',
            StudentPaymentResource::class => 'Amministrazione',

            ServiceAssignmentResource::class => 'Servizi',
            DocumentArchiveResource::class => 'Documenti',

            UserResource::class => 'Sistema',
            self::class => 'Sistema',
            AuditLogResource::class => 'Sistema',
        ];
    }

    protected static function shieldGroupOrder(): array
    {
        return [
            'Didattica',
            'Soci',
            'Amministrazione',
            'Servizi',
            'Documenti',
            'Sistema',
        ];
    }

    protected static function resolveShieldGroup(string $resourceFqcn): string
    {
        $map = static::shieldGroupMap();

        if (isset($map[$resourceFqcn])) {
            return $map[$resourceFqcn];
        }

        $group = $resourceFqcn::getNavigationGroup();

        if ($group instanceof UnitEnum) {
            $group = method_exists($group, 'getLabel')
                ? $group->getLabel()
                : $group->name;
        }

        return filled($group)
            ? (string) $group
            : 'Altro';
    }

    /*
    |--------------------------------------------------------------------------
    | RESOURCE PERMISSIONS
    |--------------------------------------------------------------------------
    */

    public static function getResourceEntitiesSchema(): ?array
    {
        $groups = collect(FilamentShield::getResources())
            ->groupBy(
                fn (array $entity): string => static::resolveShieldGroup(
                    $entity['resourceFqcn'],
                ),
            );

        $orderedGroups = collect(static::shieldGroupOrder());

        $remainingGroups = $groups->keys()
            ->diff($orderedGroups)
            ->sort()
            ->values();

        $groupOrder = $orderedGroups
            ->merge($remainingGroups);

        return $groupOrder
            ->filter(
                fn (string $groupName): bool => $groups->has($groupName),
            )
            ->map(function (string $groupName) use ($groups): Section {
                $entities = $groups->get($groupName);

                return Section::make(mb_strtoupper($groupName))
                    ->schema(
                        $entities
                            ->map(function (array $entity): Section {
                                $sectionLabel = strval(
                                    static::shield()->hasLocalizedPermissionLabels()
                                        ? FilamentShield::getLocalizedResourceLabel(
                                            $entity['resourceFqcn'],
                                        )
                                        : $entity['model'],
                                );

                                return Section::make($sectionLabel)
                                    ->description(
                                        fn (): HtmlString => new HtmlString(
                                            '<span style="word-break: break-word;">'
                                            .Utils::showModelPath(
                                                $entity['modelFqcn'],
                                            )
                                            .'</span>',
                                        ),
                                    )
                                    ->compact()
                                    ->schema([
                                        static::getCheckBoxListComponentForResource(
                                            $entity,
                                        ),
                                    ])
                                    ->collapsible()
                                    ->collapsed();
                            })
                            ->values()
                            ->all(),
                    )
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed();
            })
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | CHECKBOX LIST
    |--------------------------------------------------------------------------
    */

    protected static function getCheckBoxListComponentForResource(
        array $entity,
    ): CheckboxList {
        $resourceClass = $entity['resourceFqcn'];

        $resourceName = class_basename($resourceClass);

        $statePath = 'permissions_'.Str::snake($resourceName);

        return CheckboxList::make($statePath)
            ->label('')

            ->options(
                static::getResourcePermissionOptions($entity),
            )

            /*
             * IMPORTANTISSIMO:
             *
             * Quando si apre la pagina Edit del ruolo,
             * prendiamo le permission già presenti sul ruolo
             * e selezioniamo automaticamente le checkbox.
             */
            ->afterStateHydrated(
                function (
                    CheckboxList $component,
                    mixed $state,
                ) use ($entity): void {
                    $record = $component
                        ->getContainer()
                        ->getLivewire()
                        ->getRecord();

                    if (! $record) {
                        return;
                    }

                    $permissions = $record->permissions
                        ->pluck('name')
                        ->toArray();

                    $resourcePermissions = collect(
                        $entity['permissions'] ?? []
                    )
                        ->map(function (mixed $permission): ?string {
                            if (is_string($permission)) {
                                return $permission;
                            }

                            if (is_array($permission)) {
                                return $permission['name']
                                    ?? $permission['permission']
                                    ?? $permission['key']
                                    ?? null;
                            }

                            return null;
                        })
                        ->filter()
                        ->values()
                        ->all();

                    /*
                     * Manteniamo solo le permission
                     * appartenenti a questa Resource.
                     */
                    $selected = array_values(
                        array_intersect(
                            $permissions,
                            $resourcePermissions,
                        ),
                    );

                    $component->state($selected);
                },
            )

            ->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
            ])

            ->gridDirection('row')

            ->bulkToggleable();
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSION OPTIONS
    |--------------------------------------------------------------------------
    */

    public static function getResourcePermissionOptions(array $entity): array
    {
        return collect($entity['permissions'] ?? [])
            ->mapWithKeys(function (mixed $permission): array {
                /*
                 * Shield può restituire:
                 *
                 * "ViewAny:Student"
                 *
                 * oppure un array contenente il nome.
                 */

                if (is_string($permission)) {
                    $name = $permission;
                } elseif (is_array($permission)) {
                    $name = $permission['name']
                        ?? $permission['permission']
                        ?? $permission['key']
                        ?? null;
                } else {
                    $name = null;
                }

                if (! is_string($name) || $name === '') {
                    return [];
                }

                /*
                 * Esempio:
                 *
                 * ViewAny:Student
                 *
                 * diventa:
                 *
                 * view_any
                 */

                [$action] = explode(':', $name, 2);

                $key = Str::snake($action);

                $translationKey =
                    "filament-shield::filament-shield.resource_permission_prefixes_labels.{$key}";

                $label = __($translationKey);

                /*
                 * Fallback italiano.
                 */

                if ($label === $translationKey) {
                    $label = match ($key) {
                        'view_any' => 'Visualizza elenco',
                        'view' => 'Visualizza',
                        'create' => 'Crea',
                        'update' => 'Modifica',
                        'delete' => 'Elimina',
                        'delete_any' => 'Elimina tutti',
                        'force_delete' => 'Elimina definitivamente',
                        'force_delete_any' => 'Elimina definitivamente tutti',
                        'restore' => 'Ripristina',
                        'restore_any' => 'Ripristina tutti',
                        'replicate' => 'Duplica',
                        'reorder' => 'Riordina',
                        default => Str::headline($key),
                    };
                }

                return [
                    $name => $label,
                ];
            })
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ruolo')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge(),

                TextColumn::make('permissions_count')
                    ->label('Permessi')
                    ->counts('permissions')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LABELS
    |--------------------------------------------------------------------------
    */

    public static function getNavigationLabel(): string
    {
        return 'Ruoli';
    }

    public static function getModelLabel(): string
    {
        return 'Ruolo';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Ruoli';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Sistema';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-shield-check';
    }
}
