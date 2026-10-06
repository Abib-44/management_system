@php
    use App\Models\DocumentArchiveLink;

    $member = $this->record;

    /*
    |--------------------------------------------------------------------------
    | DATI SOCIO
    |--------------------------------------------------------------------------
    */

    $fullName = trim(
        ($member->first_name ?? '') . ' ' . ($member->last_name ?? '')
    );

    $initials = collect([
        $member->first_name,
        $member->last_name,
    ])
        ->filter()
        ->map(fn ($name) => mb_strtoupper(mb_substr($name, 0, 1)))
        ->implode('');

    $status = match ($member->status ?? null) {
        'active' => ['label' => 'Attivo', 'class' => 'success'],
        'inactive' => ['label' => 'Inattivo', 'class' => 'neutral'],
        'suspended' => ['label' => 'Sospeso', 'class' => 'warning'],
        'expired' => ['label' => 'Scaduto', 'class' => 'danger'],
        default => ['label' => ucfirst($member->status ?? 'N/D'), 'class' => 'neutral'],
    };

    $assemblyStatus = match ($member->assembly_status ?? null) {
        'active' => ['label' => 'Attivo', 'class' => 'success'],
        'inactive' => ['label' => 'Inattivo', 'class' => 'neutral'],
        default => null,
    };

    $annualFee = (float) (
        $member->annual_fee
        ?? $member->membership_fee
        ?? $member->fee
        ?? 0
    );

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    $formatDate = function ($date) {
        return $date
            ? \Carbon\Carbon::parse($date)->format('d/m/Y')
            : '—';
    };

    $formatDateTime = function ($date) {
        return $date
            ? \Carbon\Carbon::parse($date)->format('d/m/Y · H:i')
            : '—';
    };

    $formatFileSize = function ($bytes) {
        $bytes = (int) ($bytes ?? 0);

        if ($bytes <= 0) {
            return 'N/D';
        }

        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        if ($bytes < 1024 * 1024 * 1024) {
            return number_format($bytes / 1024 / 1024, 1) . ' MB';
        }

        return number_format($bytes / 1024 / 1024 / 1024, 1) . ' GB';
    };

    $isImage = function ($attachment) {
        $mime = strtolower($attachment->mime_type ?? '');
        $name = strtolower(
            $attachment->file_name
            ?? $attachment->name
            ?? ''
        );

        $extension = pathinfo($name, PATHINFO_EXTENSION);

        return str_starts_with($mime, 'image/')
            || in_array($extension, [
                'jpg', 'jpeg', 'png', 'gif',
                'webp', 'svg', 'bmp', 'avif',
            ], true);
    };

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTI DA ARCHIVIO
    |--------------------------------------------------------------------------
    |
    | Ogni archivio collegato al socio viene trasformato in un elemento
    | documentale con il proprio titolo, categoria e allegato più recente.
    |
    */

    $archiveLinks = DocumentArchiveLink::query()
        ->where('entity_type', 'member')
        ->where('member_id', $member->id)
        ->with([
            'documentArchive.attachments',
        ])
        ->get();

    $archiveDocuments = $archiveLinks
        ->map(function ($link) {
            $archive = $link->documentArchive;

            if (! $archive) {
                return null;
            }

            $attachment = $archive->attachments
                ->sortByDesc('version')
                ->sortByDesc('created_at')
                ->first();

            return [
                'id' => 'archive-' . $archive->id,
                'title' => $archive->title ?: 'Documento',
                'category' => $archive->category?->name
                    ?? 'Documento socio',
                'attachment' => $attachment,
                'date' => $archive->document_date,
                'expires_at' => $archive->expires_at,
            ];
        })
        ->filter()
        ->values();

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTI COMPLESSIVI
    |--------------------------------------------------------------------------
    |
    | Prima gli archivi collegati, poi gli allegati diretti.
    | Evitiamo di duplicare lo stesso attachment.
    |
    */

    $linkedDocuments = $archiveDocuments
        ->filter(fn ($document) => $document['attachment'] !== null)
        ->unique(function ($document) {
            return $document['attachment']->id;
        })
        ->values();

    /*
    |--------------------------------------------------------------------------
    | IMMAGINI / ALTRI FILE
    |--------------------------------------------------------------------------
    */

    $imageDocuments = $linkedDocuments
        ->filter(fn ($document) => $isImage($document['attachment']))
        ->values();

    $otherDocuments = $linkedDocuments
        ->filter(fn ($document) => ! $isImage($document['attachment']))
        ->values();
@endphp


<div class="member-profile">

    <style>
        .member-profile {
            --member-bg: #ffffff;
            --member-bg-subtle: #f8fafc;
            --member-bg-muted: #f1f5f9;

            --member-border: #e2e8f0;
            --member-border-soft: #edf1f5;

            --member-text: #0f172a;
            --member-text-secondary: #334155;
            --member-text-muted: #64748b;
            --member-text-placeholder: #94a3b8;

            --member-primary: #4f46e5;
            --member-primary-hover: #4338ca;
            --member-primary-bg: #eef2ff;

            --member-success: #059669;
            --member-success-bg: #ecfdf5;
            --member-success-border: #a7f3d0;

            --member-warning: #c2410c;
            --member-warning-bg: #fff7ed;
            --member-warning-border: #fed7aa;

            --member-danger: #dc2626;
            --member-danger-bg: #fef2f2;
            --member-danger-border: #fecaca;

            --member-shadow: rgba(15, 23, 42, .05);

            width: 100%;
            margin-top: 24px;
            color: var(--member-text);

            font-family: inherit;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        html.dark .member-profile,
        .dark .member-profile {
            --member-bg: #18181b;
            --member-bg-subtle: #202023;
            --member-bg-muted: #27272a;

            --member-border: #3f3f46;
            --member-border-soft: #303037;

            --member-text: #f4f4f5;
            --member-text-secondary: #d4d4d8;
            --member-text-muted: #a1a1aa;
            --member-text-placeholder: #71717a;

            --member-primary: #818cf8;
            --member-primary-hover: #a5b4fc;
            --member-primary-bg: rgba(99, 102, 241, .15);

            --member-success: #34d399;
            --member-success-bg: rgba(16, 185, 129, .12);
            --member-success-border: rgba(52, 211, 153, .25);

            --member-warning: #fb923c;
            --member-warning-bg: rgba(249, 115, 22, .12);
            --member-warning-border: rgba(251, 146, 60, .25);

            --member-danger: #f87171;
            --member-danger-bg: rgba(239, 68, 68, .12);
            --member-danger-border: rgba(248, 113, 113, .25);

            --member-shadow: rgba(0, 0, 0, .25);
        }

        .member-hero,
        .member-card {
            border: 1px solid var(--member-border);
            border-radius: 16px;
            background: var(--member-bg);
            box-shadow: 0 2px 6px var(--member-shadow);
        }

        .member-hero {
            overflow: hidden;
            margin-bottom: 24px;
        }

        .member-hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;

            padding: 16px 24px;

            border-bottom: 1px solid var(--member-border-soft);
        }

        .member-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            color: var(--member-text-muted);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;

            transition: color .15s ease;
        }

        .member-back:hover {
            color: var(--member-primary);
        }

        .member-back svg {
            width: 17px;
            height: 17px;
        }

        .member-hero-actions {
            display: flex;
            align-items: center;
        }

        .member-edit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            min-height: 36px;
            padding: 0 13px;

            border: 1px solid var(--member-border);
            border-radius: 9px;

            color: var(--member-text-secondary);
            background: var(--member-bg);

            font-size: 12px;
            font-weight: 650;
            text-decoration: none;

            box-shadow: 0 1px 2px var(--member-shadow);

            transition:
                color .15s ease,
                background .15s ease,
                border-color .15s ease,
                transform .15s ease;
        }

        .member-edit:hover {
            color: var(--member-primary);
            background: var(--member-primary-bg);
            border-color: var(--member-primary);
            transform: translateY(-1px);
        }

        .member-edit svg {
            width: 16px;
            height: 16px;
        }

        .member-hero-content {
            display: flex;
            align-items: center;
            gap: 18px;

            padding: 24px;
        }

        .member-avatar {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 64px;
            height: 64px;
            flex-shrink: 0;

            border-radius: 14px;

            background: var(--member-primary-bg);
            color: var(--member-primary);

            font-size: 20px;
            font-weight: 750;
        }

        .member-identity {
            min-width: 0;
            flex: 1;
        }

        .member-identity h1 {
            margin: 0;

            color: var(--member-text);

            font-size: 22px;
            line-height: 1.3;
            font-weight: 700;
            letter-spacing: -.015em;
        }

        .member-email {
            margin-top: 5px;

            overflow: hidden;

            color: var(--member-text-muted);

            font-size: 13px;

            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .member-phone {
            margin-top: 3px;

            color: var(--member-text-muted);
            font-size: 12px;
        }

        .member-header-details {
            display: flex;
            align-items: stretch;
            flex-shrink: 0;
        }

        .member-header-detail {
            min-width: 125px;
            padding: 0 18px;
            border-left: 1px solid var(--member-border-soft);
        }

        .member-header-detail span {
            display: block;

            margin-bottom: 5px;

            color: var(--member-text-muted);

            font-size: 10px;
            font-weight: 600;

            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .member-header-detail strong {
            color: var(--member-text);
            font-size: 13px;
            font-weight: 650;
        }

        .member-header-status {
            display: flex;
            align-items: center;

            margin-left: 18px;
            padding-left: 18px;

            border-left: 1px solid var(--member-border-soft);
        }

        .member-status {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            padding: 8px 12px;

            border: 1px solid transparent;
            border-radius: 999px;

            font-size: 12px;
            font-weight: 650;
            white-space: nowrap;
        }

        .member-status-dot {
            width: 7px;
            height: 7px;
            flex-shrink: 0;
            border-radius: 50%;
            background: currentColor;
        }

        .member-status.success {
            color: var(--member-success);
            background: var(--member-success-bg);
            border-color: var(--member-success-border);
        }

        .member-status.warning {
            color: var(--member-warning);
            background: var(--member-warning-bg);
            border-color: var(--member-warning-border);
        }

        .member-status.danger {
            color: var(--member-danger);
            background: var(--member-danger-bg);
            border-color: var(--member-danger-border);
        }

        .member-status.neutral {
            color: var(--member-text-muted);
            background: var(--member-bg-muted);
            border-color: var(--member-border);
        }

        .member-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .member-bottom-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .member-card {
            overflow: hidden;
        }

        .member-card-header {
            display: flex;
            align-items: center;
            gap: 10px;

            min-height: 62px;
            padding: 14px 20px;

            border-bottom: 1px solid var(--member-border-soft);
        }

        .member-card-header-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 32px;
            height: 32px;
            flex-shrink: 0;

            border-radius: 8px;

            background: var(--member-bg-muted);
            color: var(--member-text-secondary);
        }

        .member-card-header-icon svg {
            width: 17px;
            height: 17px;
        }

        .member-card-header h2 {
            margin: 0;

            color: var(--member-text);

            font-size: 14px;
            font-weight: 700;
        }

        .member-card-body {
            padding: 20px;
        }

        .member-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .member-detail {
            min-width: 0;
            padding: 16px 18px;
        }

        .member-detail:nth-child(odd) {
            border-right: 1px solid var(--member-border-soft);
        }

        .member-detail:nth-child(-n + 2) {
            border-bottom: 1px solid var(--member-border-soft);
        }

        .member-detail-label {
            display: block;
            margin-bottom: 6px;

            color: var(--member-text-muted);

            font-size: 10px;
            font-weight: 650;

            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .member-detail-value {
            color: var(--member-text);
            font-size: 13px;
            line-height: 1.5;
            font-weight: 550;
            overflow-wrap: anywhere;
        }

        .member-empty {
            color: var(--member-text-placeholder) !important;
            font-weight: 400 !important;
        }

        .member-fee {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .member-fee-total {
            display: flex;
            align-items: center;
            justify-content: space-between;

            padding-bottom: 17px;

            border-bottom: 1px solid var(--member-border-soft);
        }

        .member-fee-total span {
            color: var(--member-text-muted);
            font-size: 12px;
        }

        .member-fee-total strong {
            color: var(--member-text);
            font-size: 23px;
            font-weight: 750;
        }

        .member-fee-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .member-fee-box {
            padding: 14px;

            border: 1px solid var(--member-border-soft);
            border-radius: 10px;

            background: var(--member-bg-subtle);
        }

        .member-fee-box span {
            display: block;
            margin-bottom: 6px;

            color: var(--member-text-muted);
            font-size: 11px;
        }

        .member-fee-box strong {
            color: var(--member-text);
            font-size: 13px;
            font-weight: 650;
        }

        /* =========================================================
           DOCUMENTI - STESSO STILE DELLO STUDENT
        ========================================================= */

        .documents-gallery {
            padding: 20px;
        }

        .image-gallery {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .image-gallery.single {
            grid-template-columns: minmax(0, 1fr);
        }

        .image-gallery-item {
            position: relative;
            overflow: hidden;

            aspect-ratio: 4 / 3;

            border: 1px solid var(--member-border);
            border-radius: 12px;

            background: var(--member-bg-subtle);

            cursor: pointer;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                transform .2s ease;
        }

        .image-gallery-item:hover {
            border-color: #a5b4fc;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
            transform: translateY(-1px);
        }

        .image-gallery-item img {
            display: block;

            width: 100%;
            height: 100%;

            object-fit: contain;

            background: var(--member-bg-subtle);
        }

        .image-overlay {
            position: absolute;

            inset: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #ffffff;
            background: rgba(15, 23, 42, .42);

            opacity: 0;

            transition: opacity .18s ease;
        }

        .image-gallery-item:hover .image-overlay {
            opacity: 1;
        }

        .image-overlay svg {
            width: 22px;
            height: 22px;
        }

        .other-documents {
            margin-top: 18px;
        }

        .other-documents-title {
            margin-bottom: 10px;

            color: var(--member-text-muted);

            font-size: 11px;
            font-weight: 650;

            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .documents-list {
            display: flex;
            flex-direction: column;
            gap: 8px;

            margin: 0;
            padding: 0;

            list-style: none;
        }

        .document-item {
            display: flex;
            align-items: center;

            gap: 12px;

            padding: 11px 12px;

            border: 1px solid var(--member-border-soft);
            border-radius: 9px;

            background: var(--member-bg-subtle);
        }

        .document-item-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 34px;
            height: 34px;
            flex-shrink: 0;

            border-radius: 8px;

            background: var(--member-bg-muted);
            color: var(--member-text-muted);
        }

        .document-item-icon svg {
            width: 17px;
            height: 17px;
        }

        .document-item-info {
            min-width: 0;
            flex: 1;
        }

        .document-item-title {
            display: block;

            overflow: hidden;

            color: var(--member-primary);

            font-size: 13px;
            font-weight: 600;

            text-decoration: none;

            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .document-item-title:hover {
            text-decoration: underline;
        }

        .document-item-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 5px 10px;

            margin-top: 3px;

            color: var(--member-text-muted);

            font-size: 10px;
        }

        .document-item-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .document-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            width: 30px;
            height: 30px;

            border: 1px solid var(--member-border);
            border-radius: 8px;

            color: var(--member-text-muted);
            background: var(--member-bg);

            text-decoration: none;

            transition:
                color .15s ease,
                background .15s ease,
                border-color .15s ease;
        }

        .document-action:hover {
            color: var(--member-primary);
            background: var(--member-primary-bg);
            border-color: var(--member-primary);
        }

        .document-action svg {
            width: 15px;
            height: 15px;
        }

        .documents-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            min-height: 190px;

            color: var(--member-text-placeholder);

            text-align: center;
        }

        .documents-empty svg {
            width: 34px;
            height: 34px;
            margin-bottom: 10px;
        }

        .documents-empty span {
            font-size: 13px;
        }

        .notes-content {
            min-height: 190px;
            padding: 20px;

            color: var(--member-text-secondary);

            font-size: 13px;
            line-height: 1.7;

            white-space: pre-line;
        }

        /* =========================================================
           LIGHTBOX
        ========================================================= */

        .member-lightbox {
            position: fixed;
            inset: 0;
            z-index: 99999;

            display: none;
            align-items: center;
            justify-content: center;

            padding: 30px;

            background: rgba(0, 0, 0, .88);
            backdrop-filter: blur(8px);
        }

        .member-lightbox.is-open {
            display: flex;
        }

        .member-lightbox-content {
            position: relative;

            display: flex;
            align-items: center;
            justify-content: center;

            width: min(1400px, 100%);
            height: min(90vh, 900px);
        }

        .member-lightbox-image {
            max-width: 100%;
            max-height: 90vh;

            object-fit: contain;

            border-radius: 8px;

            box-shadow: 0 20px 70px rgba(0, 0, 0, .45);
        }

        .member-lightbox-close {
            position: fixed;

            top: 20px;
            right: 24px;

            z-index: 2;

            display: flex;
            align-items: center;
            justify-content: center;

            width: 40px;
            height: 40px;

            border: 0;
            border-radius: 10px;

            color: #ffffff;
            background: rgba(255,255,255,.12);

            cursor: pointer;
        }

        .member-lightbox-close:hover {
            background: rgba(255,255,255,.2);
        }

        .member-lightbox-close svg {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 1024px) {
            .member-grid,
            .member-bottom-grid {
                gap: 18px;
            }

            .member-hero-content {
                flex-wrap: wrap;
            }

            .member-header-details {
                width: 100%;
                padding-top: 4px;
            }

            .image-gallery {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .member-profile {
                margin-top: 12px;
            }

            .member-hero,
            .member-card {
                border-radius: 14px;
            }

            .member-hero-top {
                padding: 12px 16px;
            }

            .member-hero-content {
                padding: 18px 16px;
            }

            .member-card-header {
                padding: 12px 16px;
            }

            .member-card-body,
            .documents-gallery,
            .notes-content {
                padding: 16px;
            }

            .member-grid,
            .member-bottom-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .member-header-details {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .member-header-detail {
                min-width: 0;
                padding: 0;
                border-left: 0;
            }

            .member-header-status {
                margin-left: 0;
                padding-left: 0;
                border-left: 0;
            }

            .member-details {
                grid-template-columns: minmax(0, 1fr);
            }

            .member-detail:nth-child(odd) {
                border-right: 0;
            }

            .member-detail:nth-child(-n + 2) {
                border-bottom: 0;
            }

            .member-detail:not(:last-child) {
                border-bottom: 1px solid var(--member-border-soft);
            }

            .image-gallery {
                grid-template-columns: minmax(0, 1fr);
            }

            .document-item {
                align-items: flex-start;
            }

            .document-item-actions {
                margin-left: auto;
            }
        }
    </style>


    {{-- =========================================================
         HERO
    ========================================================== --}}

    <section class="member-hero">

        <div class="member-hero-top">

            <a
                href="{{ \App\Filament\Resources\Members\MemberResource::getUrl('index') }}"
                class="member-back"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        d="M19 12H5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                    <path
                        d="M12 19l-7-7 7-7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

                <span>Soci</span>
            </a>


            <div class="member-hero-actions">

                <a
                    href="{{ \App\Filament\Resources\Members\MemberResource::getUrl('edit', ['record' => $member]) }}"
                    class="member-edit"
                    title="Modifica socio"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            d="M12 20h9"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                    <span>Modifica</span>
                </a>

            </div>

        </div>


        <div class="member-hero-content">

            <div class="member-avatar">
                {{ $initials ?: 'S' }}
            </div>


            <div class="member-identity">

                <h1>
                    {{ $fullName ?: 'Socio senza nome' }}
                </h1>

                @if ($member->email)
                    <div class="member-email">
                        {{ $member->email }}
                    </div>
                @endif

                @if ($member->phone)
                    <div class="member-phone">
                        {{ $member->phone }}
                    </div>
                @endif

            </div>


            <div class="member-header-details">

                <div class="member-header-detail">

                    <span>
                        Iscrizione
                    </span>

                    <strong>
                        {{ $formatDate($member->registration_date) }}
                    </strong>

                </div>


                <div class="member-header-detail">

                    <span>
                        Rinnovo
                    </span>

                    <strong class="{{ $member->renewal_date ? '' : 'member-empty' }}">
                        {{ $formatDate($member->renewal_date) }}
                    </strong>

                </div>

            </div>


            <div class="member-header-status">

                <div class="member-status {{ $status['class'] }}">

                    <span class="member-status-dot"></span>

                    {{ $status['label'] }}

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         MAIN GRID
    ========================================================== --}}

    <div class="member-grid">


        {{-- =====================================================
             ISCRIZIONE
        ====================================================== --}}

        <section class="member-card">

            <div class="member-card-header">

                <div class="member-card-header-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <rect
                            x="3"
                            y="4"
                            width="18"
                            height="17"
                            rx="2"
                        />
                        <path
                            d="M16 2v4M8 2v4M3 10h18"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <h2>
                    Iscrizione
                </h2>

            </div>


            <div class="member-details">

                <div class="member-detail">

                    <span class="member-detail-label">
                        Data iscrizione
                    </span>

                    <div class="member-detail-value">
                        {{ $formatDate($member->registration_date) }}
                    </div>

                </div>


                <div class="member-detail">

                    <span class="member-detail-label">
                        Data rinnovo
                    </span>

                    <div class="member-detail-value {{ $member->renewal_date ? '' : 'member-empty' }}">
                        {{ $formatDate($member->renewal_date) }}
                    </div>

                </div>


                <div class="member-detail">

                    <span class="member-detail-label">
                        Stato assemblea
                    </span>

                    <div class="member-detail-value {{ $assemblyStatus ? '' : 'member-empty' }}">
                        {{ $assemblyStatus['label'] ?? 'N/D' }}
                    </div>

                </div>


                <div class="member-detail">

                    <span class="member-detail-label">
                        Ultimo aggiornamento
                    </span>

                    <div class="member-detail-value">
                        {{ $formatDateTime($member->updated_at) }}
                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             QUOTA
        ====================================================== --}}

        <section class="member-card">

            <div class="member-card-header">

                <div class="member-card-header-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                        />
                        <path
                            d="M15 8.5c-.7-.7-1.7-1-3-1-1.7 0-3 .9-3 2.2 0 1.3 1.2 1.8 3 2.3 1.8.5 3 1 3 2.3 0 1.3-1.3 2.2-3 2.2-1.3 0-2.3-.3-3-1"
                            stroke-linecap="round"
                        />
                        <path
                            d="M12 6v12"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <h2>
                    Quota
                </h2>

            </div>


            <div class="member-card-body">

                <div class="member-fee">

                    <div class="member-fee-total">

                        <span>
                            Quota annuale
                        </span>

                        <strong>
                            {{ number_format($annualFee, 2, ',', '.') }} €
                        </strong>

                    </div>


                    <div class="member-fee-meta">

                        <div class="member-fee-box">

                            <span>
                                Creato
                            </span>

                            <strong>
                                {{ $formatDateTime($member->created_at) }}
                            </strong>

                        </div>


                        <div class="member-fee-box">

                            <span>
                                Aggiornato
                            </span>

                            <strong>
                                {{ $formatDateTime($member->updated_at) }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>


    {{-- =========================================================
         DOCUMENTI + NOTE
    ========================================================== --}}

    <div class="member-bottom-grid">


        {{-- =====================================================
             DOCUMENTI
        ====================================================== --}}

        <section class="member-card">

            <div class="member-card-header">

                <div class="member-card-header-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M14 3v5h5"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M8.5 13h7M8.5 16.5h7"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <h2>
                    Documenti
                </h2>

            </div>


            <div class="documents-gallery">

                {{-- =================================================
                     IMMAGINI
                ================================================== --}}

                @if ($imageDocuments->isNotEmpty())

                    <div
                        class="image-gallery {{ $imageDocuments->count() === 1 ? 'single' : '' }}"
                    >

                        @foreach ($imageDocuments as $index => $document)

                            @php
                                $attachment = $document['attachment'];

                                $documentUrl = route(
                                    'attachments.show',
                                    $attachment
                                );

                                $fileName = $attachment->file_name
                                    ?? $attachment->name
                                    ?? $document['title'];
                            @endphp


                            <div
                                class="image-gallery-item"
                                data-member-lightbox
                                data-image-url="{{ $documentUrl }}"
                                data-image-name="{{ $fileName }}"
                                tabindex="0"
                                role="button"
                                aria-label="Apri {{ $document['title'] }}"
                            >

                                <img
                                    src="{{ $documentUrl }}"
                                    alt="{{ $fileName }}"
                                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                >


                                <div class="image-overlay">

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="M15 3h6v6"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M10 14L21 3"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif


                {{-- =================================================
                     ALTRI DOCUMENTI
                ================================================== --}}

                @if ($otherDocuments->isNotEmpty())

                    <div class="other-documents">

                        <div class="other-documents-title">
                            Altri documenti
                        </div>


                        <ul class="documents-list">

                            @foreach ($otherDocuments as $document)

                                @php
                                    $attachment = $document['attachment'];

                                    $documentUrl = route(
                                        'attachments.show',
                                        $attachment
                                    );

                                    $downloadUrl = route(
                                        'attachments.show',
                                        [
                                            $attachment,
                                            'download' => 1,
                                        ]
                                    );

                                    $fileName = $attachment->file_name
                                        ?? $attachment->name
                                        ?? 'Documento';

                                    $category = $document['category']
                                        ?? 'Documento';
                                @endphp


                                <li class="document-item">

                                    <div class="document-item-icon">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <path
                                                d="M14 3v4a1 1 0 0 0 1 1h4"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M5 3h9l5 5v13H5z"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M8 13h8M8 16.5h6"
                                                stroke-linecap="round"
                                            />
                                        </svg>

                                    </div>


                                    <div class="document-item-info">

                                        <a
                                            href="{{ $documentUrl }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="document-item-title"
                                        >
                                            {{ $document['title'] }}
                                        </a>


                                        <div class="document-item-meta">

                                            <span>
                                                {{ $category }}
                                            </span>

                                            @if ($fileName)
                                                <span>
                                                    {{ $fileName }}
                                                </span>
                                            @endif

                                            @if ($attachment->file_size)
                                                <span>
                                                    {{ $formatFileSize($attachment->file_size) }}
                                                </span>
                                            @endif

                                        </div>

                                    </div>


                                    <div class="document-item-actions">

                                        <a
                                            href="{{ $documentUrl }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="document-action"
                                            title="Apri"
                                            aria-label="Apri documento"
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    d="M15 3h6v6"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                                <path
                                                    d="M10 14L21 3"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                                <path
                                                    d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        </a>


                                        <a
                                            href="{{ $downloadUrl }}"
                                            class="document-action"
                                            title="Scarica"
                                            aria-label="Scarica documento"
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    d="M12 3v12"
                                                    stroke-linecap="round"
                                                />
                                                <path
                                                    d="m7 10 5 5 5-5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                                <path
                                                    d="M5 21h14"
                                                    stroke-linecap="round"
                                                />
                                            </svg>
                                        </a>

                                    </div>

                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                @if ($linkedDocuments->isEmpty())

                    <div class="documents-empty">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path
                                d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M14 3v5h5"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M9 13h6M9 16h4"
                                stroke-linecap="round"
                            />
                        </svg>

                        <span>
                            Nessun documento disponibile
                        </span>

                    </div>

                @endif

            </div>

        </section>


        {{-- =====================================================
             NOTE
        ====================================================== --}}

        <section class="member-card">

            <div class="member-card-header">

                <div class="member-card-header-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"
                        />
                        <path
                            d="M8 8h8M8 12h8M8 16h5"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <h2>
                    Note
                </h2>

            </div>


            <div class="notes-content">

                @if (filled($member->activity_notes))

                    {{ $member->activity_notes }}

                @else

                    <span class="member-empty">
                        Nessuna nota disponibile.
                    </span>

                @endif

            </div>

        </section>

    </div>


    {{-- =========================================================
         LIGHTBOX
    ========================================================== --}}

    @if ($imageDocuments->isNotEmpty())

        <div
            class="member-lightbox"
            data-member-lightbox-container
            aria-hidden="true"
        >

            <button
                type="button"
                class="member-lightbox-close"
                data-member-lightbox-close
                aria-label="Chiudi"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        d="M6 6l12 12M18 6L6 18"
                        stroke-linecap="round"
                    />
                </svg>
            </button>


            <div class="member-lightbox-content">

                <img
                    class="member-lightbox-image"
                    data-member-lightbox-image
                    src=""
                    alt=""
                >

            </div>

        </div>

    @endif


    <script>
        (() => {
            const root = document.querySelector('.member-profile');

            if (!root) {
                return;
            }

            const lightbox = root.querySelector(
                '[data-member-lightbox-container]'
            );

            const lightboxImage = root.querySelector(
                '[data-member-lightbox-image]'
            );

            const closeButton = root.querySelector(
                '[data-member-lightbox-close]'
            );

            const items = root.querySelectorAll(
                '[data-member-lightbox]'
            );

            if (!lightbox || !lightboxImage || !items.length) {
                return;
            }

            const openLightbox = (item) => {
                const imageUrl = item.dataset.imageUrl;
                const imageName = item.dataset.imageName || 'Documento';

                if (!imageUrl) {
                    return;
                }

                lightboxImage.src = imageUrl;
                lightboxImage.alt = imageName;

                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');

                document.body.style.overflow = 'hidden';
            };

            const closeLightbox = () => {
                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');

                lightboxImage.src = '';
                lightboxImage.alt = '';

                document.body.style.overflow = '';
            };

            items.forEach((item) => {
                item.addEventListener('click', () => {
                    openLightbox(item);
                });

                item.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        openLightbox(item);
                    }
                });
            });

            closeButton?.addEventListener('click', closeLightbox);

            lightbox.addEventListener('click', (event) => {
                if (event.target === lightbox) {
                    closeLightbox();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && lightbox.classList.contains('is-open')) {
                    closeLightbox();
                }
            });
        })();
    </script>

</div>
