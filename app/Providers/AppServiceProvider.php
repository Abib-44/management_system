<?php

namespace App\Providers;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Modelli da NON tracciare (AuditLog è obbligatorio, evita cicli infiniti).
     */
    protected array $auditExcluded = [
        AuditLog::class,
    ];

    /**
     * Campi mai salvati nei log.
     */
    protected array $auditHiddenFields = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // I file temporanei di Livewire passano dal server (nginx + PHP),
        // non direttamente dal browser a MinIO.
        config(['livewire.temporary_file_upload.disk' => 'local']);

        $this->registerAuthAuditing();
        $this->registerModelAuditing();
    }

    protected function registerAuthAuditing(): void
    {
        Event::listen(Login::class, function (Login $event) {
            $this->writeAudit('login', $event->user->getAuthIdentifier());
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                $this->writeAudit('logout', $event->user->getAuthIdentifier());
            }
        });
    }

    protected function registerModelAuditing(): void
    {
        foreach (['created' => 'created', 'updated' => 'updated', 'deleted' => 'deleted'] as $eloquentEvent => $action) {
            Event::listen("eloquent.{$eloquentEvent}: *", function (string $eventName, array $payload) use ($action) {
                $model = $payload[0] ?? null;

                if (! $model instanceof Model) {
                    return;
                }

                foreach ($this->auditExcluded as $excluded) {
                    if ($model instanceof $excluded) {
                        return;
                    }
                }

                // Salta seeder, migrate, code ecc.
                if (app()->runningInConsole()) {
                    return;
                }

                [$old, $new] = $this->extractValues($model, $action);

                // Modifica senza cambiamenti reali (es. solo updated_at): ignora
                if ($action === 'updated' && empty($new)) {
                    return;
                }

                $this->writeAudit($action, Auth::id(), $model, $old, $new);
            });
        }
    }

    protected function extractValues(Model $model, string $action): array
    {
        $hidden = array_merge($this->auditHiddenFields, $model->getHidden());

        $clean = fn (array $data) => array_diff_key($data, array_flip($hidden));

        return match ($action) {
            'created' => [null, $clean($model->getAttributes())],
            'deleted' => [$clean($model->getOriginal()), null],
            'updated' => (function () use ($model, $clean) {
                $changes = $clean(array_diff_key($model->getChanges(), ['updated_at' => true]));
                $old = array_intersect_key($clean($model->getOriginal()), $changes);

                return [$old, $changes];
            })(),
            default => [null, null],
        };
    }

    protected function writeAudit(
        string $action,
        mixed $userId,
        ?Model $model = null,
        ?array $old = null,
        ?array $new = null,
    ): void {
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
            'auditable_type' => $model?->getMorphClass(),
            'auditable_id' => $model?->getKey(),
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }
}
