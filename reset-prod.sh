#!/usr/bin/env bash
# Elimina tutti i container, la rete e i volumi (DATI INCLUSI) del progetto
# e li ricrea da zero simulando una nuova installazione in produzione.
set -euo pipefail

DC="docker compose --env-file .env.production -f compose.prod.yaml"

# Attende che una porta TCP risponda, controllandola dal container laravel.
wait_for() {
  local host="$1" port="$2"
  until $DC exec -T laravel php -r "exit(@fsockopen('$host', $port, \$e, \$m, 1) ? 0 : 1);"; do
    echo "In attesa di $host:$port ..."
    sleep 2
  done
}

echo "==> Elimino container, rete e volumi"
$DC down -v --remove-orphans

echo "==> Ricostruisco e avvio"
$DC up -d --build

echo "==> Attendo i servizi"
wait_for mysql 3306
wait_for minio 9000

echo "==> Creo il bucket su MinIO (se manca)"
PHP_CODE='$c = Storage::disk("s3")->getClient(); $b = config("filesystems.disks.s3.bucket"); if (! $c->doesBucketExistV2($b)) { $c->createBucket(["Bucket" => $b]); echo "bucket creato\n"; } else { echo "bucket gia presente\n"; }'
$DC exec -T -e HOME=/tmp laravel php artisan tinker --execute="$PHP_CODE"

echo "==> Migrazioni e seed"
$DC exec -T laravel php artisan migrate:fresh --seed --force

echo "==> Pulisco le cache"
$DC exec -T laravel php artisan optimize:clear

echo "==> Fatto"
$DC ps