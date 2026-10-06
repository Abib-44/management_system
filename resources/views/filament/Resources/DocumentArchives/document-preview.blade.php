@php
    use App\Models\DocumentArchiveLink;

    /*
    |--------------------------------------------------------------------------
    | ATTACHMENTS
    |--------------------------------------------------------------------------
    */

    $attachments = $record->attachments()
        ->orderByDesc('version')
        ->orderByDesc('created_at')
        ->get();

    $latestAttachment = $attachments->first();


    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    $formatFileSize = function ($bytes) {
        if (!$bytes) {
            return '—';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    };

    $fileName = function ($attachment) {
        return $attachment?->original_name
            ?? $attachment?->file_name
            ?? $attachment?->name
            ?? 'Documento';
    };

    $fileExtension = function ($attachment) use ($fileName) {
        return strtoupper(
            pathinfo($fileName($attachment), PATHINFO_EXTENSION) ?: 'FILE'
        );
    };


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    $statusMap = [
        'active' => [
            'label' => 'Attivo',
            'class' => 'success',
        ],

        'archived' => [
            'label' => 'Archiviato',
            'class' => 'neutral',
        ],

        'expired' => [
            'label' => 'Scaduto',
            'class' => 'danger',
        ],
    ];

    $status = $statusMap[$record->status] ?? [
        'label' => ucfirst($record->status ?? '—'),
        'class' => 'neutral',
    ];


    /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

    $category = $record->category;


    /*
    |--------------------------------------------------------------------------
    | DOCUMENT LINKS
    |--------------------------------------------------------------------------
    */

    $documentLinks = DocumentArchiveLink::query()
        ->where('document_archive_id', $record->id)
        ->with([
            'member',
            'activity',
            'transaction',
            'student',
            'createdBy',
        ])
        ->orderByDesc('created_at')
        ->get();


    $entityTypeLabel = function ($link) {
        return match ($link->entity_type) {
            'member' => 'Socio',
            'student' => 'Studente',
            'activity' => 'Attività',
            'financial_transaction' => 'Movimento',
            'transaction' => 'Movimento',
            default => ucfirst(
                str_replace('_', ' ', $link->entity_type ?? '')
            ),
        };
    };


    $entityName = function ($link) {
        return match ($link->entity_type) {

            'member' =>
                $link->member?->full_name
                ?? $link->member?->name
                ?? trim(
                    ($link->member?->first_name ?? '') . ' ' .
                    ($link->member?->last_name ?? '')
                ),

            'student' =>
                $link->student?->full_name
                ?? $link->student?->name
                ?? trim(
                    ($link->student?->first_name ?? '') . ' ' .
                    ($link->student?->last_name ?? '')
                ),

            'activity' =>
                $link->activity?->name
                ?? $link->activity?->title
                ?? 'Attività',

            'financial_transaction',
            'transaction' =>
                $link->transaction?->description
                ?? $link->transaction?->title
                ?? 'Movimento',

            default => 'Elemento collegato',
        };
    };


    $linkedEntities = $documentLinks
        ->map(function ($link) use ($entityTypeLabel, $entityName) {
            return [
                'type' => $link->entity_type,
                'type_label' => $entityTypeLabel($link),
                'name' => $entityName($link),
                'created_by' => $link->createdBy,
            ];
        })
        ->filter(fn ($item) => filled($item['name']))
        ->values();


    /*
    |--------------------------------------------------------------------------
    | CREATOR
    |--------------------------------------------------------------------------
    */

    $createdBy = $documentLinks->first()?->createdBy;


    /*
    |--------------------------------------------------------------------------
    | FILE DATA
    |--------------------------------------------------------------------------
    */

    $extension = $fileExtension($latestAttachment);

    $latestFileName = $fileName($latestAttachment);

    $fileSize = $latestAttachment
        ? $formatFileSize(
            $latestAttachment->size
                ?? $latestAttachment->file_size
                ?? 0
        )
        : '—';

    $version = $latestAttachment?->version ?? 1;

    $versionCount = $attachments->count();

    $isPdf = strtolower($extension) === 'pdf';

    $imageExtensions = [
        'JPG',
        'JPEG',
        'PNG',
        'GIF',
        'WEBP',
        'BMP',
        'SVG',
    ];

    $isImage = in_array($extension, $imageExtensions);


    /*
    |--------------------------------------------------------------------------
    | URLS
    |--------------------------------------------------------------------------
    */

    $archiveIndexUrl =
        \App\Filament\Resources\DocumentArchives\DocumentArchiveResource::getUrl(
            'index'
        );
@endphp


<div class="document-profile">

    <style>
        /* ============================================================
           FIX FILAMENT CONTAINER
           ============================================================ */

        /*
         * Filament crea normalmente:
         *
         * .fi-section
         *   .fi-section-content
         *     .fi-grid
         *       nostra view
         *
         * Qui eliminiamo SOLO il contenitore che contiene
         * .document-profile.
         */

        .fi-section:has(.document-profile),
        .fi-section:has(.document-profile) > .fi-section-content,
        .fi-section:has(.document-profile) .fi-section-content,
        .fi-section:has(.document-profile) .fi-grid {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            gap: 0 !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .fi-section:has(.document-profile) .fi-section-content {
            display: block !important;
        }


        /* ============================================================
           ROOT
           ============================================================ */

        .document-profile {
            --doc-card: #17171a;
            --doc-card-2: #1d1d21;
            --doc-border: #35353d;
            --doc-border-soft: #2b2b31;

            --doc-text: #f5f5f7;
            --doc-muted: #9a9aa5;
            --doc-muted-2: #72727d;

            --doc-primary: #8b82ff;
            --doc-success: #20d897;
            --doc-danger: #ff626f;

            width: 100%;
            color: var(--doc-text);
        }

        .document-profile *,
        .document-profile *::before,
        .document-profile *::after {
            box-sizing: border-box;
        }

        .document-profile a {
            text-decoration: none;
        }


        /* ============================================================
           BACK
           ============================================================ */

        .document-profile__back {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            margin-bottom: 18px;

            color: #9ea0ad;

            font-size: 13px;
            font-weight: 500;

            transition: color .15s ease;
        }

        .document-profile__back:hover {
            color: white;
        }

        .document-profile__back svg {
            width: 17px;
            height: 17px;
        }


        /* ============================================================
           HERO
           ============================================================ */

        .document-profile__hero {
            width: 100%;

            border: 1px solid var(--doc-border);
            border-radius: 16px;

            background: #17171a;

            overflow: hidden;
        }

        .document-profile__hero-main {
            min-height: 130px;

            display: flex;
            align-items: center;

            gap: 18px;

            padding: 22px 24px;
        }

        .document-profile__file-icon {
            width: 64px;
            height: 64px;

            flex: 0 0 64px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 15px;

            background: #292650;

            color: var(--doc-primary);
        }

        .document-profile__file-icon svg {
            width: 34px;
            height: 34px;
        }

        .document-profile__identity {
            min-width: 0;
            flex: 1;
        }

        .document-profile__title-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .document-profile__title {
            margin: 0;

            color: #f5f5f7;

            font-size: 24px;
            line-height: 1.25;
            font-weight: 700;

            letter-spacing: -.02em;
        }

        .document-profile__subtitle {
            margin-top: 8px;

            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;

            color: #9b9ca7;

            font-size: 12px;
        }

        .document-profile__dot {
            width: 4px;
            height: 4px;

            border-radius: 50%;

            background: #62636d;
        }


        /* ============================================================
           STATUS
           ============================================================ */

        .document-profile__status {
            display: inline-flex;
            align-items: center;
            gap: 6px;

            padding: 5px 10px;

            border-radius: 999px;

            font-size: 11px;
            font-weight: 600;
        }

        .document-profile__status::before {
            content: "";

            width: 6px;
            height: 6px;

            border-radius: 50%;
        }

        .document-profile__status--success {
            color: var(--doc-success);

            border: 1px solid rgba(32, 216, 151, .3);

            background: rgba(32, 216, 151, .07);
        }

        .document-profile__status--success::before {
            background: var(--doc-success);
        }

        .document-profile__status--danger {
            color: var(--doc-danger);

            border: 1px solid rgba(255, 98, 111, .3);

            background: rgba(255, 98, 111, .07);
        }

        .document-profile__status--danger::before {
            background: var(--doc-danger);
        }

        .document-profile__status--neutral {
            color: #a9aab5;

            border: 1px solid #3d3e46;

            background: rgba(255,255,255,.035);
        }

        .document-profile__status--neutral::before {
            background: #858691;
        }


        /* ============================================================
           VERSION BADGE
           ============================================================ */

        .document-profile__current-version {
            display: inline-flex;
            align-items: center;

            padding: 4px 8px;

            border-radius: 7px;

            color: #9a91ff;

            background: rgba(96, 83, 225, .16);

            font-size: 10px;
            font-weight: 600;
        }


        /* ============================================================
           ACTIONS
           ============================================================ */

        .document-profile__actions {
            display: flex;
            align-items: center;
            gap: 7px;

            flex-shrink: 0;
        }

        .document-profile__button {
            height: 34px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 7px;

            padding: 0 12px;

            border-radius: 8px;

            font-size: 11px;
            font-weight: 600;

            transition: .15s ease;
        }

        .document-profile__button svg {
            width: 15px;
            height: 15px;
        }

        .document-profile__button--secondary {
            color: #e5e5e9;

            border: 1px solid #44454e;

            background: #191a1f;
        }

        .document-profile__button--secondary:hover {
            background: #23242a;
            border-color: #5b5c67;
        }

        .document-profile__button--primary {
            color: white;

            border: 1px solid #8b82ff;

            background: #7c72f2;
        }

        .document-profile__button--primary:hover {
            background: #8980ff;
        }


        /* ============================================================
           TWO INFO CARDS
           ============================================================ */

        .document-profile__cards {
            display: grid;

            grid-template-columns: repeat(2, minmax(0, 1fr));

            gap: 20px;

            margin-top: 20px;
        }

        .document-profile__card {
            min-width: 0;

            border: 1px solid var(--doc-border);
            border-radius: 15px;

            background: var(--doc-card);

            overflow: hidden;
        }

        .document-profile__card-header {
            min-height: 54px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 18px;

            border-bottom: 1px solid var(--doc-border);
        }

        .document-profile__card-title {
            display: flex;
            align-items: center;
            gap: 10px;

            color: #eeeeef;

            font-size: 13px;
            font-weight: 700;
        }

        .document-profile__card-title-icon {
            width: 31px;
            height: 31px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;

            background: #292650;

            color: var(--doc-primary);
        }

        .document-profile__card-title-icon svg {
            width: 16px;
            height: 16px;
        }


        /* ============================================================
           INFO GRID
           ============================================================ */

        .document-profile__details {
            display: grid;

            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .document-profile__detail {
            min-height: 82px;

            padding: 17px 20px;

            border-right: 1px solid var(--doc-border);
            border-bottom: 1px solid var(--doc-border);
        }

        .document-profile__detail:nth-child(2n) {
            border-right: 0;
        }

        .document-profile__detail:nth-last-child(-n + 2) {
            border-bottom: 0;
        }

        .document-profile__detail-label {
            color: #858691;

            font-size: 9px;
            font-weight: 600;

            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .document-profile__detail-value {
            margin-top: 6px;

            color: #f1f1f3;

            font-size: 13px;
            font-weight: 600;
        }


        /* ============================================================
           FILE DETAILS
           ============================================================ */

        .document-profile__file-details {
            padding: 18px 20px;
        }

        .document-profile__file-main {
            display: flex;
            align-items: center;
            gap: 14px;

            padding-bottom: 17px;

            border-bottom: 1px solid var(--doc-border);
        }

        .document-profile__file-main-icon {
            width: 48px;
            height: 48px;

            flex: 0 0 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            background: #25234b;

            color: var(--doc-primary);
        }

        .document-profile__file-main-icon svg {
            width: 23px;
            height: 23px;
        }

        .document-profile__file-main-name {
            color: white;

            font-size: 13px;
            font-weight: 600;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .document-profile__file-main-meta {
            margin-top: 4px;

            color: #777883;

            font-size: 11px;
        }

        .document-profile__file-grid {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            margin-top: 15px;
        }

        .document-profile__file-item {
            padding: 8px 0;
        }

        .document-profile__file-label {
            color: #858691;

            font-size: 9px;
            font-weight: 600;

            text-transform: uppercase;
        }

        .document-profile__file-value {
            margin-top: 5px;

            color: #eeeeef;

            font-size: 13px;
            font-weight: 600;
        }


        /* ============================================================
           PREVIEW + HISTORY
           ============================================================ */

        .document-profile__workspace {
            display: grid;

            grid-template-columns:
                minmax(0, 1.55fr)
                minmax(340px, .9fr);

            gap: 20px;

            margin-top: 20px;
        }


        /* ============================================================
           PREVIEW
           ============================================================ */

        .document-profile__preview {
            padding: 12px;
        }

        .document-profile__preview-frame {
            min-height: 500px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 1px solid #34353d;
            border-radius: 10px;

            background: #1d1e22;

            overflow: hidden;
        }

        .document-profile__preview-frame iframe {
            display: block;

            width: 100%;
            height: 560px;

            border: 0;

            background: white;
        }

        .document-profile__preview-image {
            display: block;

            max-width: 92%;
            max-height: 510px;

            object-fit: contain;

            border-radius: 7px;

            background: white;
        }

        .document-profile__empty-preview {
            padding: 50px 30px;

            text-align: center;

            color: #73747e;
        }

        .document-profile__empty-preview svg {
            width: 45px;
            height: 45px;

            margin-bottom: 12px;
        }

        .document-profile__empty-preview strong {
            display: block;

            color: #bfc0c8;

            font-size: 13px;
        }

        .document-profile__empty-preview span {
            display: block;

            margin-top: 5px;

            font-size: 11px;
        }


        /* ============================================================
           HISTORY
           ============================================================ */

        .document-profile__history-count {
            min-width: 25px;
            height: 25px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 999px;

            color: #a6a1ff;

            background: #29274d;

            font-size: 10px;
            font-weight: 700;
        }

        .document-profile__history {
            padding: 12px;
        }

        .document-profile__version {
            padding: 14px;

            margin-bottom: 10px;

            border: 1px solid #34353d;
            border-radius: 10px;

            background: #15161a;
        }

        .document-profile__version:last-child {
            margin-bottom: 0;
        }

        .document-profile__version--current {
            border-color: #7068e9;

            background:
                linear-gradient(
                    135deg,
                    rgba(99, 88, 255, .08),
                    #15161a
                );
        }

        .document-profile__version-top {
            display: flex;
            gap: 11px;
        }

        .document-profile__version-icon {
            width: 39px;
            height: 39px;

            flex: 0 0 39px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;

            background: #28264d;

            color: var(--doc-primary);
        }

        .document-profile__version-icon svg {
            width: 19px;
            height: 19px;
        }

        .document-profile__version-content {
            min-width: 0;
            flex: 1;
        }

        .document-profile__version-line {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .document-profile__version-number {
            color: white;

            font-size: 12px;
            font-weight: 700;
        }

        .document-profile__current-badge {
            padding: 3px 6px;

            border-radius: 5px;

            color: var(--doc-success);

            background: rgba(32, 216, 151, .1);

            font-size: 9px;
            font-weight: 700;
        }

        .document-profile__version-name {
            margin-top: 5px;

            color: #bdbec6;

            font-size: 11px;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .document-profile__version-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;

            margin-top: 4px;

            color: #70717b;

            font-size: 9px;
        }

        .document-profile__version-actions {
            display: flex;
            justify-content: flex-end;

            gap: 6px;

            margin-top: 12px;
        }

        .document-profile__version-button {
            height: 29px;

            display: inline-flex;
            align-items: center;
            gap: 5px;

            padding: 0 9px;

            border: 1px solid #41424b;
            border-radius: 6px;

            color: #cfd0d7;

            background: #191a1f;

            font-size: 10px;
            font-weight: 600;
        }

        .document-profile__version-button:hover {
            background: #222329;
        }

        .document-profile__version-button svg {
            width: 13px;
            height: 13px;
        }


        /* ============================================================
           LINKED
           ============================================================ */

        .document-profile__linked {
            margin-top: 20px;
        }

        .document-profile__linked-header {
            min-height: 65px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 18px;

            border-bottom: 1px solid var(--doc-border);
        }

        .document-profile__linked-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .document-profile__linked-title > svg {
            width: 22px;
            height: 22px;

            color: var(--doc-primary);
        }

        .document-profile__linked-title strong {
            display: block;

            color: #eeeeef;

            font-size: 13px;
        }

        .document-profile__linked-title span {
            display: block;

            margin-top: 3px;

            color: #777883;

            font-size: 10px;
        }

        .document-profile__linked-count {
            width: 28px;
            height: 28px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 999px;

            color: #999aa4;

            background: #24252b;

            font-size: 10px;
            font-weight: 700;
        }

        .document-profile__linked-body {
            padding: 12px;
        }

        .document-profile__linked-grid {
            display: grid;

            grid-template-columns: repeat(3, minmax(0, 1fr));

            gap: 9px;
        }

        .document-profile__linked-item {
            min-width: 0;

            display: flex;
            align-items: center;
            gap: 10px;

            padding: 12px;

            border: 1px solid #32333a;
            border-radius: 9px;

            background: #15161a;
        }

        .document-profile__linked-icon {
            width: 37px;
            height: 37px;

            flex: 0 0 37px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;

            background: #28264d;

            color: var(--doc-primary);
        }

        .document-profile__linked-icon svg {
            width: 18px;
            height: 18px;
        }

        .document-profile__linked-content {
            min-width: 0;
        }

        .document-profile__linked-type {
            color: #777883;

            font-size: 9px;
            font-weight: 600;

            text-transform: uppercase;
        }

        .document-profile__linked-name {
            margin-top: 3px;

            color: #e7e7eb;

            font-size: 11px;
            font-weight: 600;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .document-profile__linked-creator {
            margin-top: 3px;

            color: #62636d;

            font-size: 9px;
        }

        .document-profile__linked-empty {
            padding: 25px;

            text-align: center;

            border: 1px dashed #35363e;
            border-radius: 9px;

            color: #72737d;

            font-size: 11px;
        }


        /* ============================================================
           EMPTY
           ============================================================ */

        .document-profile__empty {
            padding: 70px 30px;

            text-align: center;

            border: 1px solid var(--doc-border);
            border-radius: 15px;

            background: var(--doc-card);
        }

        .document-profile__empty-icon {
            width: 60px;
            height: 60px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: 0 auto 15px;

            border-radius: 15px;

            background: #292650;

            color: var(--doc-primary);
        }

        .document-profile__empty-icon svg {
            width: 30px;
            height: 30px;
        }

        .document-profile__empty h3 {
            margin: 0;

            color: white;

            font-size: 16px;
        }

        .document-profile__empty p {
            margin: 7px 0 0;

            color: #777883;

            font-size: 12px;
        }


        /* ============================================================
           RESPONSIVE
           ============================================================ */

        @media (max-width: 1100px) {

            .document-profile__stats {
                grid-template-columns: repeat(3, 1fr);
            }

            .document-profile__workspace {
                grid-template-columns: 1fr;
            }

            .document-profile__linked-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }


        @media (max-width: 800px) {

            .document-profile__hero-main {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .document-profile__actions {
                width: 100%;
                margin-left: 82px;
            }

            .document-profile__cards {
                grid-template-columns: 1fr;
            }

            .document-profile__stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }


        @media (max-width: 600px) {

            .document-profile__hero-main {
                padding: 17px;
            }

            .document-profile__file-icon {
                width: 55px;
                height: 55px;
                flex-basis: 55px;
            }

            .document-profile__file-icon svg {
                width: 29px;
                height: 29px;
            }

            .document-profile__title {
                font-size: 19px;
            }

            .document-profile__actions {
                margin-left: 0;
            }

            .document-profile__stats {
                grid-template-columns: 1fr;
            }

            .document-profile__details {
                grid-template-columns: 1fr;
            }

            .document-profile__detail,
            .document-profile__detail:nth-child(2n) {
                border-right: 0;
                border-bottom: 1px solid var(--doc-border);
            }

            .document-profile__detail:last-child {
                border-bottom: 0;
            }

            .document-profile__linked-grid {
                grid-template-columns: 1fr;
            }

            .document-profile__preview-frame {
                min-height: 400px;
            }

            .document-profile__preview-frame iframe {
                height: 450px;
            }
        }
    </style>


    {{-- ============================================================
         BACK
         ============================================================ --}}



    {{-- ============================================================
         HERO
         ============================================================ --}}

    <section class="document-profile__hero">

        <div class="document-profile__hero-main">

            <div class="document-profile__file-icon">

                @if ($isImage)
                    <x-heroicon-o-photo />
                @elseif ($isPdf)
                    <x-heroicon-o-document-text />
                @else
                    <x-heroicon-o-document />
                @endif

            </div>


            <div class="document-profile__identity">

                <div class="document-profile__title-row">

                    <h1 class="document-profile__title">
                        {{ $record->title }}
                    </h1>

                    <span
                        class="
                            document-profile__status
                            document-profile__status--{{ $status['class'] }}
                        "
                    >
                        {{ $status['label'] }}
                    </span>

                </div>


                <div class="document-profile__subtitle">

                    @if ($category)

                        <span>
                            {{ $category->name }}
                        </span>

                        <span class="document-profile__dot"></span>

                    @endif

                    <span>
                        {{ $extension }}
                    </span>

                    <span class="document-profile__dot"></span>

                    <span>
                        {{ $fileSize }}
                    </span>

                    @if ($latestAttachment?->created_at)

                        <span class="document-profile__dot"></span>

                        <span>
                            Aggiornato
                            {{ $latestAttachment->created_at->format('d/m/Y H:i') }}
                        </span>

                    @endif

                    <span class="document-profile__current-version">
                        Versione {{ $version }}
                    </span>

                </div>

            </div>


            @if ($latestAttachment)

                <div class="document-profile__actions">

                    <a
                        href="{{ route('attachments.show', $latestAttachment) }}"
                        target="_blank"
                        class="document-profile__button document-profile__button--secondary"
                    >
                        <x-heroicon-o-arrow-top-right-on-square />

                        <span>
                            Apri
                        </span>
                    </a>


                    <a
                        href="{{ route('attachments.show', [
                            'attachment' => $latestAttachment,
                            'download' => 1,
                        ]) }}"
                        class="document-profile__button document-profile__button--primary"
                    >
                        <x-heroicon-o-arrow-down-tray />

                        <span>
                            Scarica
                        </span>
                    </a>

                </div>

            @endif

        </div>

    </section>


    @if ($latestAttachment)

        {{-- ========================================================
             INFORMATION CARDS
             ======================================================== --}}

        <div class="document-profile__cards">


            {{-- ====================================================
                 DOCUMENT INFO
                 ==================================================== --}}

            <section class="document-profile__card">

                <div class="document-profile__card-header">

                    <div class="document-profile__card-title">

                        <span class="document-profile__card-title-icon">
                            <x-heroicon-o-information-circle />
                        </span>

                        <span>
                            Informazioni
                        </span>

                    </div>

                </div>


                <div class="document-profile__details">

                    <div class="document-profile__detail">

                        <div class="document-profile__detail-label">
                            Categoria
                        </div>

                        <div class="document-profile__detail-value">
                            {{ $category?->name ?? 'N/D' }}
                        </div>

                    </div>


                    <div class="document-profile__detail">

                        <div class="document-profile__detail-label">
                            Stato
                        </div>

                        <div class="document-profile__detail-value">
                            {{ $status['label'] }}
                        </div>

                    </div>


                    <div class="document-profile__detail">

                        <div class="document-profile__detail-label">
                            Data documento
                        </div>

                        <div class="document-profile__detail-value">

                            {{
                                $record->document_date
                                    ? \Carbon\Carbon::parse($record->document_date)->format('d/m/Y')
                                    : '—'
                            }}

                        </div>

                    </div>


                    <div class="document-profile__detail">

                        <div class="document-profile__detail-label">
                            Scadenza
                        </div>

                        <div class="document-profile__detail-value">

                            {{
                                $record->expires_at
                                    ? \Carbon\Carbon::parse($record->expires_at)->format('d/m/Y')
                                    : '—'
                            }}

                        </div>

                    </div>


                    <div class="document-profile__detail">

                        <div class="document-profile__detail-label">
                            Creato da
                        </div>

                        <div class="document-profile__detail-value">

                            @if ($createdBy)

                                {{ $createdBy->name ?? $createdBy->email ?? 'Utente' }}

                            @else

                                N/D

                            @endif

                        </div>

                    </div>


                    <div class="document-profile__detail">

                        <div class="document-profile__detail-label">
                            Versioni
                        </div>

                        <div class="document-profile__detail-value">
                            {{ $versionCount }}
                        </div>

                    </div>

                </div>

            </section>


            {{-- ====================================================
                 FILE INFO
                 ==================================================== --}}

            <section class="document-profile__card">

                <div class="document-profile__card-header">

                    <div class="document-profile__card-title">

                        <span class="document-profile__card-title-icon">
                            <x-heroicon-o-document />
                        </span>

                        <span>
                            File
                        </span>

                    </div>

                </div>


                <div class="document-profile__file-details">

                    <div class="document-profile__file-main">

                        <div class="document-profile__file-main-icon">

                            @if ($isImage)
                                <x-heroicon-o-photo />
                            @elseif ($isPdf)
                                <x-heroicon-o-document-text />
                            @else
                                <x-heroicon-o-document />
                            @endif

                        </div>


                        <div style="min-width:0;">

                            <div
                                class="document-profile__file-main-name"
                                title="{{ $latestFileName }}"
                            >
                                {{ $latestFileName }}
                            </div>

                            <div class="document-profile__file-main-meta">
                                {{ $extension }} · {{ $fileSize }}
                            </div>

                        </div>

                    </div>


                    <div class="document-profile__file-grid">

                        <div class="document-profile__file-item">

                            <div class="document-profile__file-label">
                                Versione
                            </div>

                            <div class="document-profile__file-value">
                                v{{ $version }}
                            </div>

                        </div>


                        <div class="document-profile__file-item">

                            <div class="document-profile__file-label">
                                Formato
                            </div>

                            <div class="document-profile__file-value">
                                {{ $extension }}
                            </div>

                        </div>


                        <div class="document-profile__file-item">

                            <div class="document-profile__file-label">
                                Dimensione
                            </div>

                            <div class="document-profile__file-value">
                                {{ $fileSize }}
                            </div>

                        </div>


                        <div class="document-profile__file-item">

                            <div class="document-profile__file-label">
                                Tipo
                            </div>

                            <div class="document-profile__file-value">

                                @if ($isImage)
                                    Immagine
                                @elseif ($isPdf)
                                    PDF
                                @else
                                    Documento
                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </section>

        </div>


        {{-- ========================================================
             PREVIEW + HISTORY
             ======================================================== --}}

        <div class="document-profile__workspace">


            {{-- ====================================================
                 PREVIEW
                 ==================================================== --}}

            <section class="document-profile__card">

                <div class="document-profile__card-header">

                    <div class="document-profile__card-title">

                        <span class="document-profile__card-title-icon">
                            <x-heroicon-o-photo />
                        </span>

                        <span>
                            Anteprima
                        </span>

                    </div>

                    <span style="
                        color:#777883;
                        font-size:10px;
                    ">
                        {{ $latestFileName }}
                    </span>

                </div>


                <div class="document-profile__preview">

                    <div class="document-profile__preview-frame">

                        @if ($isPdf)

                            <iframe
                                src="{{ route('attachments.show', $latestAttachment) }}"
                                title="{{ $record->title }}"
                            ></iframe>

                        @elseif ($isImage)

                            <img
                                src="{{ route('attachments.show', $latestAttachment) }}"
                                alt="{{ $record->title }}"
                                class="document-profile__preview-image"
                            >

                        @else

                            <div class="document-profile__empty-preview">

                                <x-heroicon-o-document-magnifying-glass />

                                <strong>
                                    Anteprima non disponibile
                                </strong>

                                <span>
                                    Il formato {{ $extension }}
                                    non può essere visualizzato direttamente.
                                </span>

                            </div>

                        @endif

                    </div>

                </div>

            </section>


            {{-- ====================================================
                 HISTORY
                 ==================================================== --}}

            <section class="document-profile__card">

                <div class="document-profile__card-header">

                    <div class="document-profile__card-title">

                        <span class="document-profile__card-title-icon">
                            <x-heroicon-o-clock />
                        </span>

                        <span>
                            Cronologia versioni
                        </span>

                    </div>


                    <span class="document-profile__history-count">
                        {{ $versionCount }}
                    </span>

                </div>


                <div class="document-profile__history">

                    @foreach ($attachments as $attachment)

                        @php

                            $attachmentExtension =
                                $fileExtension($attachment);

                            $attachmentSize =
                                $formatFileSize(
                                    $attachment->size
                                        ?? $attachment->file_size
                                        ?? 0
                                );

                            $attachmentName =
                                $fileName($attachment);

                            $isCurrent =
                                $latestAttachment &&
                                $attachment->id === $latestAttachment->id;

                        @endphp


                        <div
                            class="
                                document-profile__version
                                {{ $isCurrent
                                    ? 'document-profile__version--current'
                                    : ''
                                }}
                            "
                        >

                            <div class="document-profile__version-top">

                                <div class="document-profile__version-icon">

                                    @if (
                                        in_array(
                                            $attachmentExtension,
                                            [
                                                'JPG',
                                                'JPEG',
                                                'PNG',
                                                'GIF',
                                                'WEBP',
                                                'BMP',
                                                'SVG'
                                            ]
                                        )
                                    )

                                        <x-heroicon-o-photo />

                                    @elseif ($attachmentExtension === 'PDF')

                                        <x-heroicon-o-document-text />

                                    @else

                                        <x-heroicon-o-document />

                                    @endif

                                </div>


                                <div class="document-profile__version-content">

                                    <div class="document-profile__version-line">

                                        <span class="document-profile__version-number">
                                            v{{ $attachment->version ?? 1 }}
                                        </span>


                                        @if ($isCurrent)

                                            <span class="document-profile__current-badge">
                                                Corrente
                                            </span>

                                        @endif

                                    </div>


                                    <div
                                        class="document-profile__version-name"
                                        title="{{ $attachmentName }}"
                                    >
                                        {{ $attachmentName }}
                                    </div>


                                    <div class="document-profile__version-meta">

                                        @if ($attachment->created_at)

                                            <span>
                                                {{ $attachment->created_at->format('d/m/Y') }}
                                            </span>

                                            <span>•</span>

                                            <span>
                                                {{ $attachment->created_at->format('H:i') }}
                                            </span>

                                            <span>•</span>

                                        @endif

                                        <span>
                                            {{ $attachmentSize }}
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <div class="document-profile__version-actions">

                                <a
                                    href="{{ route('attachments.show', $attachment) }}"
                                    target="_blank"
                                    class="document-profile__version-button"
                                >

                                    <x-heroicon-o-eye />

                                    <span>
                                        Visualizza
                                    </span>

                                </a>


                                <a
                                    href="{{ route('attachments.show', [
                                        'attachment' => $attachment,
                                        'download' => 1,
                                    ]) }}"
                                    class="document-profile__version-button"
                                >

                                    <x-heroicon-o-arrow-down-tray />

                                    <span>
                                        Scarica
                                    </span>

                                </a>

                            </div>

                        </div>

                    @endforeach

                </div>

            </section>

        </div>


        {{-- ========================================================
             LINKED DOCUMENTS
             ======================================================== --}}

        <section class="document-profile__card document-profile__linked">

            <div class="document-profile__linked-header">

                <div class="document-profile__linked-title">

                    <x-heroicon-o-link />

                    <div>

                        <strong>
                            Documento collegato a
                        </strong>

                        <span>
                            Persone, attività e movimenti associati a questo documento
                        </span>

                    </div>

                </div>


                <span class="document-profile__linked-count">
                    {{ $linkedEntities->count() }}
                </span>

            </div>


            <div class="document-profile__linked-body">

                @if ($linkedEntities->isNotEmpty())

                    <div class="document-profile__linked-grid">

                        @foreach ($linkedEntities as $entity)

                            <div class="document-profile__linked-item">

                                <div class="document-profile__linked-icon">

                                    @switch($entity['type'])

                                        @case('member')

                                            <x-heroicon-o-user />

                                            @break


                                        @case('student')

                                            <x-heroicon-o-academic-cap />

                                            @break


                                        @case('activity')

                                            <x-heroicon-o-calendar-days />

                                            @break


                                        @case('financial_transaction')
                                        @case('transaction')

                                            <x-heroicon-o-banknotes />

                                            @break


                                        @default

                                            <x-heroicon-o-link />

                                    @endswitch

                                </div>


                                <div class="document-profile__linked-content">

                                    <div class="document-profile__linked-type">
                                        {{ $entity['type_label'] }}
                                    </div>


                                    <div
                                        class="document-profile__linked-name"
                                        title="{{ $entity['name'] }}"
                                    >
                                        {{ $entity['name'] }}
                                    </div>


                                    @if ($entity['created_by'])

                                        <div class="document-profile__linked-creator">

                                            Collegato da
                                            {{ $entity['created_by']->name
                                                ?? $entity['created_by']->email
                                                ?? 'Utente'
                                            }}

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="document-profile__linked-empty">
                        Nessun elemento collegato a questo documento.
                    </div>

                @endif

            </div>

        </section>


    @else


        {{-- ========================================================
             NO FILE
             ======================================================== --}}

        <div class="document-profile__empty">

            <div class="document-profile__empty-icon">

                <x-heroicon-o-document />

            </div>


            <h3>
                Nessun file associato
            </h3>


            <p>
                Questo archivio documentale non contiene ancora alcun allegato.
            </p>

        </div>

    @endif

</div>