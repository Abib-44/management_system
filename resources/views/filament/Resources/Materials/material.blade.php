@php
    $material = $this->record;

    $subjectName = $material->subject?->name ?? 'Materia non specificata';

    $typeLabel = match ($material->type) {
        'book' => 'Libro',
        'equipment' => 'Attrezzatura',
        'consumable' => 'Consumabile',
        'document' => 'Documento',
        default => $material->type ?: 'Non specificato',
    };

    $quantity = (int) ($material->quantity ?? 0);
    $availableQuantity = (int) ($material->available_quantity ?? 0);

    $usedQuantity = max($quantity - $availableQuantity, 0);

    $availabilityPercentage = $quantity > 0
        ? min(($availableQuantity / $quantity) * 100, 100)
        : 0;

    $availabilityLabel = match (true) {
        $quantity <= 0 => 'Nessuna quantità',
        $availableQuantity <= 0 => 'Esaurito',
        $availableQuantity < $quantity => 'Parzialmente disponibile',
        default => 'Disponibile',
    };

    $availabilityClass = match (true) {
        $quantity <= 0 => 'danger',
        $availableQuantity <= 0 => 'danger',
        $availableQuantity < $quantity => 'warning',
        default => 'success',
    };

    /*
     * Recuperiamo gli allegati solo se il modello Material
     * possiede realmente la relazione attachments().
     */
    $attachments = collect();

    if (method_exists($material, 'attachments')) {
        $attachments = $material->attachments;
    }

    /*
     * Mostriamo nella galleria solamente le immagini.
     */
    $images = $attachments->filter(function ($attachment) {
        return str_starts_with(
            strtolower($attachment->mime_type ?? ''),
            'image/'
        );
    });

    $documents = $attachments->filter(function ($attachment) {
        return !str_starts_with(
            strtolower($attachment->mime_type ?? ''),
            'image/'
        );
    });

    $formatDateTime = function ($date) {
        return $date
            ? \Carbon\Carbon::parse($date)->format('d/m/Y · H:i')
            : '—';
    };
@endphp

<div class="material-view">

    <style>
        .material-view {
            --mv-bg: #ffffff;
            --mv-bg-soft: #f8fafc;
            --mv-bg-muted: #f1f5f9;

            --mv-border: #e2e8f0;
            --mv-border-soft: #edf2f7;

            --mv-text: #0f172a;
            --mv-text-secondary: #334155;
            --mv-text-muted: #64748b;
            --mv-text-placeholder: #94a3b8;

            --mv-primary: #4f46e5;
            --mv-primary-soft: #eef2ff;

            --mv-success: #059669;
            --mv-success-soft: #ecfdf5;
            --mv-success-border: #a7f3d0;

            --mv-warning: #d97706;
            --mv-warning-soft: #fffbeb;
            --mv-warning-border: #fde68a;

            --mv-danger: #dc2626;
            --mv-danger-soft: #fef2f2;
            --mv-danger-border: #fecaca;

            --mv-shadow: 0 1px 2px rgba(15, 23, 42, .04);

            width: 100%;
            color: var(--mv-text);
        }

        .dark .material-view,
        html.dark .material-view {
            --mv-bg: #18181b;
            --mv-bg-soft: #1f1f23;
            --mv-bg-muted: #27272a;

            --mv-border: #3f3f46;
            --mv-border-soft: #303037;

            --mv-text: #f4f4f5;
            --mv-text-secondary: #d4d4d8;
            --mv-text-muted: #a1a1aa;
            --mv-text-placeholder: #71717a;

            --mv-primary: #818cf8;
            --mv-primary-soft: rgba(99, 102, 241, .15);

            --mv-success: #34d399;
            --mv-success-soft: rgba(16, 185, 129, .12);
            --mv-success-border: rgba(52, 211, 153, .25);

            --mv-warning: #fbbf24;
            --mv-warning-soft: rgba(245, 158, 11, .12);
            --mv-warning-border: rgba(251, 191, 36, .25);

            --mv-danger: #f87171;
            --mv-danger-soft: rgba(239, 68, 68, .12);
            --mv-danger-border: rgba(248, 113, 113, .25);

            --mv-shadow: 0 1px 2px rgba(0, 0, 0, .25);
        }

        /* ---------------------------------------------------------
           MAIN
        --------------------------------------------------------- */
        #fi-main-content {
            margin-top: 35px;
        }

        .mv-container {
            margin-top: 20px;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ---------------------------------------------------------
           HERO
        --------------------------------------------------------- */

        .mv-hero {
            position: relative;
            overflow: hidden;
            margin-bottom: 24px;
            border: 1px solid var(--mv-border);
            border-radius: 16px;
            background: var(--mv-bg);
            box-shadow: var(--mv-shadow);
        }

        .mv-hero-inner {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 26px 28px;
        }

        .mv-hero-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 64px;
            height: 64px;
            flex-shrink: 0;

            border-radius: 16px;
            background: var(--mv-primary-soft);
            color: var(--mv-primary);
        }

        .mv-hero-icon svg {
            width: 30px;
            height: 30px;
        }

        .mv-hero-content {
            min-width: 0;
            flex: 1;
        }

        .mv-breadcrumb {
            display: flex;
            align-items: center;
            gap: 7px;

            margin-bottom: 7px;

            color: var(--mv-text-muted);
            font-size: 12px;
            font-weight: 500;
        }

        .mv-breadcrumb svg {
            width: 14px;
            height: 14px;
        }

        .mv-title {
            margin: 0;
            color: var(--mv-text);
            font-size: 25px;
            line-height: 1.25;
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .mv-subtitle {
            margin-top: 7px;

            color: var(--mv-text-muted);
            font-size: 13px;
        }

        .mv-hero-badges {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 8px;
        }

        .mv-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            padding: 7px 11px;

            border: 1px solid var(--mv-border);
            border-radius: 999px;

            background: var(--mv-bg-soft);

            color: var(--mv-text-secondary);
            font-size: 12px;
            font-weight: 600;
        }

        .mv-badge svg {
            width: 14px;
            height: 14px;
        }

        .mv-badge-success {
            color: var(--mv-success);
            background: var(--mv-success-soft);
            border-color: var(--mv-success-border);
        }

        .mv-badge-warning {
            color: var(--mv-warning);
            background: var(--mv-warning-soft);
            border-color: var(--mv-warning-border);
        }

        .mv-badge-danger {
            color: var(--mv-danger);
            background: var(--mv-danger-soft);
            border-color: var(--mv-danger-border);
        }

        /* ---------------------------------------------------------
           GRID
        --------------------------------------------------------- */

        .mv-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, .65fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .mv-card {
            overflow: hidden;

            border: 1px solid var(--mv-border);
            border-radius: 14px;

            background: var(--mv-bg);
            box-shadow: var(--mv-shadow);
        }

        .mv-card-header {
            display: flex;
            align-items: center;
            gap: 11px;

            padding: 16px 20px;

            border-bottom: 1px solid var(--mv-border-soft);
        }

        .mv-card-header-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 32px;
            height: 32px;

            border-radius: 8px;

            background: var(--mv-bg-muted);
            color: var(--mv-text-secondary);
        }

        .mv-card-header-icon svg {
            width: 17px;
            height: 17px;
        }

        .mv-card-header h2 {
            margin: 0;

            color: var(--mv-text);
            font-size: 14px;
            font-weight: 650;
        }

        .mv-card-body {
            padding: 20px;
        }

        /* ---------------------------------------------------------
           INFORMATION
        --------------------------------------------------------- */

        .mv-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mv-info-item {
            min-width: 0;
            padding: 18px 20px;

            border-bottom: 1px solid var(--mv-border-soft);
        }

        .mv-info-item:nth-child(odd) {
            border-right: 1px solid var(--mv-border-soft);
        }

        .mv-info-item:nth-last-child(-n + 2) {
            border-bottom: 0;
        }

        .mv-info-label {
            display: block;
            margin-bottom: 7px;

            color: var(--mv-text-muted);
            font-size: 10px;
            font-weight: 600;

            text-transform: uppercase;
            letter-spacing: .055em;
        }

        .mv-info-value {
            display: flex;
            align-items: center;
            gap: 7px;

            color: var(--mv-text);
            font-size: 14px;
            line-height: 1.5;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .mv-info-value svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;

            color: var(--mv-text-muted);
        }

        /* ---------------------------------------------------------
           QUANTITY
        --------------------------------------------------------- */

        .mv-quantity-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .mv-quantity-body {
            display: flex;
            flex-direction: column;
            gap: 20px;

            padding: 20px;
            flex: 1;
        }

        .mv-quantity-main {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .mv-quantity-number {
            color: var(--mv-text);
            font-size: 36px;
            line-height: 1;
            font-weight: 750;
            letter-spacing: -.04em;
        }

        .mv-quantity-label {
            margin-top: 6px;

            color: var(--mv-text-muted);
            font-size: 12px;
        }

        .mv-quantity-available {
            text-align: right;
        }

        .mv-quantity-available strong {
            display: block;

            color: var(--mv-success);
            font-size: 23px;
            line-height: 1.1;
            font-weight: 700;
        }

        .mv-quantity-available span {
            display: block;
            margin-top: 5px;

            color: var(--mv-text-muted);
            font-size: 11px;
        }

        .mv-progress {
            width: 100%;
            height: 9px;

            overflow: hidden;

            border-radius: 999px;
            background: var(--mv-bg-muted);
        }

        .mv-progress-fill {
            height: 100%;
            border-radius: inherit;

            background: var(--mv-success);

            transition: width .3s ease;
        }

        .mv-quantity-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .mv-stat {
            padding: 12px;

            border: 1px solid var(--mv-border-soft);
            border-radius: 9px;

            background: var(--mv-bg-soft);
        }

        .mv-stat span {
            display: block;

            color: var(--mv-text-muted);
            font-size: 10px;
        }

        .mv-stat strong {
            display: block;
            margin-top: 5px;

            color: var(--mv-text);
            font-size: 15px;
            font-weight: 650;
        }

        .mv-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;

            padding: 11px 13px;

            border-radius: 9px;

            font-size: 12px;
            font-weight: 600;
        }

        .mv-status.success {
            color: var(--mv-success);
            background: var(--mv-success-soft);
        }

        .mv-status.warning {
            color: var(--mv-warning);
            background: var(--mv-warning-soft);
        }

        .mv-status.danger {
            color: var(--mv-danger);
            background: var(--mv-danger-soft);
        }

        .mv-status-dot {
            width: 7px;
            height: 7px;

            border-radius: 50%;
            background: currentColor;
        }

        .mv-status-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ---------------------------------------------------------
           DESCRIPTION
        --------------------------------------------------------- */

        .mv-description {
            margin-bottom: 24px;
        }

        .mv-description-body {
            padding: 20px;

            color: var(--mv-text-secondary);
            font-size: 14px;
            line-height: 1.75;

            white-space: pre-line;
        }

        .mv-empty {
            color: var(--mv-text-placeholder);
        }

        /* ---------------------------------------------------------
           IMAGES
        --------------------------------------------------------- */

        .mv-gallery {
            margin-bottom: 24px;
        }

        .mv-gallery-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;

            padding: 16px 20px;

            border-bottom: 1px solid var(--mv-border-soft);
        }

        .mv-gallery-title {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .mv-gallery-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            min-width: 25px;
            height: 25px;
            padding: 0 7px;

            border-radius: 999px;

            background: var(--mv-primary-soft);
            color: var(--mv-primary);

            font-size: 11px;
            font-weight: 700;
        }

        .mv-images {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;

            padding: 20px;
        }

        .mv-image {
            position: relative;

            overflow: hidden;

            aspect-ratio: 16 / 10;

            border-radius: 10px;

            background: var(--mv-bg-muted);

            cursor: pointer;
        }

        .mv-image img {
            display: block;

            width: 100%;
            height: 100%;

            object-fit: cover;

            transition:
                transform .25s ease,
                filter .25s ease;
        }

        .mv-image:hover img {
            transform: scale(1.035);
            filter: brightness(.78);
        }

        .mv-image-overlay {
            position: absolute;
            inset: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            opacity: 0;

            background: rgba(0, 0, 0, .25);

            color: white;

            transition: opacity .2s ease;
        }

        .mv-image:hover .mv-image-overlay {
            opacity: 1;
        }

        .mv-image-overlay svg {
            width: 27px;
            height: 27px;
        }

        /* ---------------------------------------------------------
           DOCUMENTS
        --------------------------------------------------------- */

        .mv-documents {
            margin-bottom: 24px;
        }

        .mv-documents-list {
            display: flex;
            flex-direction: column;
            gap: 8px;

            padding: 16px 20px;
        }

        .mv-document {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 11px 12px;

            border: 1px solid var(--mv-border-soft);
            border-radius: 9px;

            background: var(--mv-bg-soft);

            text-decoration: none;
            transition:
                border-color .15s ease,
                background .15s ease;
        }

        .mv-document:hover {
            border-color: var(--mv-primary);
            background: var(--mv-primary-soft);
        }

        .mv-document-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 34px;
            height: 34px;
            flex-shrink: 0;

            border-radius: 8px;

            background: var(--mv-bg-muted);
            color: var(--mv-text-muted);
        }

        .mv-document-icon svg {
            width: 17px;
            height: 17px;
        }

        .mv-document-info {
            min-width: 0;
            flex: 1;
        }

        .mv-document-name {
            display: block;

            overflow: hidden;

            color: var(--mv-text);
            font-size: 13px;
            font-weight: 600;

            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .mv-document-type {
            display: block;
            margin-top: 2px;

            color: var(--mv-text-muted);
            font-size: 10px;
        }

        /* ---------------------------------------------------------
           META
        --------------------------------------------------------- */

        .mv-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;

            margin-bottom: 24px;
        }

        .mv-meta-item {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 15px;

            border: 1px solid var(--mv-border);
            border-radius: 10px;

            background: var(--mv-bg);
        }

        .mv-meta-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 34px;
            height: 34px;
            flex-shrink: 0;

            border-radius: 8px;

            background: var(--mv-bg-muted);
            color: var(--mv-text-muted);
        }

        .mv-meta-icon svg {
            width: 16px;
            height: 16px;
        }

        .mv-meta-text span {
            display: block;

            color: var(--mv-text-muted);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .mv-meta-text strong {
            display: block;
            margin-top: 3px;

            color: var(--mv-text-secondary);
            font-size: 12px;
            font-weight: 550;
        }

        /* ---------------------------------------------------------
           IMAGE LIGHTBOX
        --------------------------------------------------------- */

        .mv-lightbox {
            position: fixed;
            inset: 0;
            z-index: 99999;

            display: none;
            align-items: center;
            justify-content: center;

            padding: 30px;

            background: rgba(0, 0, 0, .88);
        }

        .mv-lightbox.active {
            display: flex;
        }

        .mv-lightbox-image {
            max-width: min(1200px, 92vw);
            max-height: 88vh;

            border-radius: 10px;

            object-fit: contain;

            box-shadow: 0 25px 70px rgba(0, 0, 0, .5);
        }

        .mv-lightbox-close {
            position: absolute;
            top: 22px;
            right: 22px;

            display: flex;
            align-items: center;
            justify-content: center;

            width: 42px;
            height: 42px;

            border: 0;
            border-radius: 50%;

            background: rgba(255,255,255,.12);
            color: white;

            cursor: pointer;
        }

        .mv-lightbox-close:hover {
            background: rgba(255,255,255,.2);
        }

        .mv-lightbox-close svg {
            width: 21px;
            height: 21px;
        }

        /* ---------------------------------------------------------
           RESPONSIVE
        --------------------------------------------------------- */

        @media (max-width: 1000px) {
            .mv-grid {
                grid-template-columns: 1fr;
            }

            .mv-images {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .mv-hero-inner {
                align-items: flex-start;
                flex-direction: column;
            }

            .mv-hero-badges {
                justify-content: flex-start;
            }

            .mv-info-grid {
                grid-template-columns: 1fr;
            }

            .mv-info-item:nth-child(odd) {
                border-right: 0;
            }

            .mv-info-item:nth-last-child(-n + 2) {
                border-bottom: 1px solid var(--mv-border-soft);
            }

            .mv-info-item:last-child {
                border-bottom: 0;
            }

            .mv-images {
                grid-template-columns: 1fr;
            }

            .mv-meta {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 500px) {
            .mv-hero-inner {
                padding: 20px;
            }

            .mv-title {
                font-size: 21px;
            }

            .mv-card-body,
            .mv-description-body {
                padding: 16px;
            }

            .mv-quantity-main {
                align-items: flex-start;
            }

            .mv-quantity-number {
                font-size: 30px;
            }

            .mv-images {
                padding: 14px;
            }

            .mv-lightbox {
                padding: 15px;
            }
        }
    </style>

    <div class="mv-container">

        {{-- =====================================================
             HERO
        ====================================================== --}}

        <section class="mv-hero">

            <div class="mv-hero-inner">

                <div class="mv-hero-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M3.27 6.96L12 12.01l8.73-5.05"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M12 22.08V12"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <div class="mv-hero-content">

                    <div class="mv-breadcrumb">

                        <span>
                            Materiali
                        </span>

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="m9 18 6-6-6-6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        <span>
                            {{ $subjectName }}
                        </span>

                    </div>

                    <h1 class="mv-title">
                        {{ $material->name ?: 'Materiale senza nome' }}
                    </h1>

                    <div class="mv-subtitle">
                        Dettaglio del materiale
                    </div>

                </div>

                <div class="mv-hero-badges">

                    <div class="mv-badge">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                d="M20 7h-9"
                                stroke-linecap="round"
                            />

                            <path
                                d="M20 12H4"
                                stroke-linecap="round"
                            />

                            <path
                                d="M20 17H8"
                                stroke-linecap="round"
                            />
                        </svg>

                        {{ $typeLabel }}

                    </div>

                    <div class="mv-badge {{ $material->active ? 'mv-badge-success' : 'mv-badge-danger' }}">

                        <span
                            style="
                                width: 7px;
                                height: 7px;
                                border-radius: 50%;
                                background: currentColor;
                            "
                        ></span>

                        {{ $material->active ? 'Attivo' : 'Non attivo' }}

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             MAIN GRID
        ====================================================== --}}

        <div class="mv-grid">

            {{-- INFO --}}

            <section class="mv-card">

                <div class="mv-card-header">

                    <div class="mv-card-header-icon">

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
                                d="M12 11v5"
                                stroke-linecap="round"
                            />

                            <path
                                d="M12 8h.01"
                                stroke-linecap="round"
                            />
                        </svg>

                    </div>

                    <h2>
                        Informazioni
                    </h2>

                </div>

                <div class="mv-info-grid">

                    <div class="mv-info-item">

                        <span class="mv-info-label">
                            Materia
                        </span>

                        <div class="mv-info-value">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"
                                    stroke-linecap="round"
                                />

                                <path
                                    d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            {{ $subjectName }}

                        </div>

                    </div>

                    <div class="mv-info-item">

                        <span class="mv-info-label">
                            Tipo
                        </span>

                        <div class="mv-info-value">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    d="M20.59 13.41 11 3.83V3H4v7h.83l9.58 9.59a2 2 0 0 0 2.83 0l3.35-3.35a2 2 0 0 0 0-2.83z"
                                    stroke-linejoin="round"
                                />

                                <circle
                                    cx="7.5"
                                    cy="6.5"
                                    r="1"
                                />
                            </svg>

                            {{ $typeLabel }}

                        </div>

                    </div>

                    <div class="mv-info-item">

                        <span class="mv-info-label">
                            Posizione
                        </span>

                        <div class="mv-info-value">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0z"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />

                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2.5"
                                />
                            </svg>

                            {{ $material->location ?: 'Non specificata' }}

                        </div>

                    </div>

                    <div class="mv-info-item">

                        <span class="mv-info-label">
                            Stato
                        </span>

                        <div class="mv-info-value">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    d="M20 6 9 17l-5-5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            {{ $material->active ? 'Materiale attivo' : 'Materiale non attivo' }}

                        </div>

                    </div>

                </div>

            </section>


            {{-- QUANTITA --}}

            <section class="mv-card mv-quantity-card">

                <div class="mv-card-header">

                    <div class="mv-card-header-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8z"
                                stroke-linejoin="round"
                            />

                            <path
                                d="m3.27 6.96 8.73 5.05 8.73-5.05"
                                stroke-linejoin="round"
                            />
                        </svg>

                    </div>

                    <h2>
                        Disponibilità
                    </h2>

                </div>

                <div class="mv-quantity-body">

                    <div class="mv-quantity-main">

                        <div>

                            <div class="mv-quantity-number">
                                {{ $quantity }}
                            </div>

                            <div class="mv-quantity-label">
                                Quantità totale
                            </div>

                        </div>

                        <div class="mv-quantity-available">

                            <strong>
                                {{ $availableQuantity }}
                            </strong>

                            <span>
                                disponibili
                            </span>

                        </div>

                    </div>

                    <div class="mv-progress">

                        <div
                            class="mv-progress-fill"
                            style="width: {{ $availabilityPercentage }}%;"
                        ></div>

                    </div>

                    <div class="mv-quantity-stats">

                        <div class="mv-stat">

                            <span>
                                Utilizzati
                            </span>

                            <strong>
                                {{ $usedQuantity }}
                            </strong>

                        </div>

                        <div class="mv-stat">

                            <span>
                                Disponibilità
                            </span>

                            <strong>
                                {{ number_format($availabilityPercentage, 0) }}%
                            </strong>

                        </div>

                    </div>

                    <div class="mv-status {{ $availabilityClass }}">

                        <div class="mv-status-left">

                            <span class="mv-status-dot"></span>

                            {{ $availabilityLabel }}

                        </div>

                        <span>
                            {{ $availableQuantity }}/{{ $quantity }}
                        </span>

                    </div>

                </div>

            </section>

        </div>


        {{-- =====================================================
             DESCRIZIONE
        ====================================================== --}}

        <section class="mv-card mv-description">

            <div class="mv-card-header">

                <div class="mv-card-header-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5z"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M8 8h8M8 12h8M8 16h5"
                            stroke-linecap="round"
                        />
                    </svg>

                </div>

                <h2>
                    Descrizione
                </h2>

            </div>

            <div class="mv-description-body">

                @if ($material->description)

                    {{ $material->description }}

                @else

                    <span class="mv-empty">
                        Nessuna descrizione disponibile.
                    </span>

                @endif

            </div>

        </section>


        {{-- =====================================================
             GALLERIA IMMAGINI
        ====================================================== --}}

        @if ($images->isNotEmpty())

            <section class="mv-card mv-gallery">

                <div class="mv-gallery-header">

                    <div class="mv-gallery-title">

                        <div class="mv-card-header-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect
                                    x="3"
                                    y="3"
                                    width="18"
                                    height="18"
                                    rx="2"
                                />

                                <circle
                                    cx="8.5"
                                    cy="8.5"
                                    r="1.5"
                                />

                                <path
                                    d="m21 15-5-5L5 21"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                        </div>

                        <h2>
                            Immagini
                        </h2>

                        <span class="mv-gallery-count">
                            {{ $images->count() }}
                        </span>

                    </div>

                </div>

                <div class="mv-images">

                    @foreach ($images as $attachment)

                        @php
                            $imageUrl = route(
                                'attachments.show',
                                $attachment
                            );
                        @endphp

                        <div
                            class="mv-image"
                            onclick="openMaterialImage(@js($imageUrl))"
                        >

                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $attachment->file_name ?? 'Immagine materiale' }}"
                                loading="lazy"
                            >

                            <div class="mv-image-overlay">

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
                                        d="M9 21H3v-6"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />

                                    <path
                                        d="M21 3 14 10"
                                        stroke-linecap="round"
                                    />

                                    <path
                                        d="m3 21 7-7"
                                        stroke-linecap="round"
                                    />
                                </svg>

                            </div>

                        </div>

                    @endforeach

                </div>

            </section>

        @endif


        {{-- =====================================================
             DOCUMENTI
        ====================================================== --}}

        @if ($documents->isNotEmpty())

            <section class="mv-card mv-documents">

                <div class="mv-card-header">

                    <div class="mv-card-header-icon">

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
                                d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                    </div>

                    <h2>
                        Documenti
                    </h2>

                </div>

                <div class="mv-documents-list">

                    @foreach ($documents as $attachment)

                        @php
                            $documentUrl = route(
                                'attachments.show',
                                $attachment
                            );

                            $extension = pathinfo(
                                $attachment->file_name ?? '',
                                PATHINFO_EXTENSION
                            );
                        @endphp

                        <a
                            href="{{ $documentUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mv-document"
                        >

                            <div class="mv-document-icon">

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
                                        d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>

                            </div>

                            <div class="mv-document-info">

                                <span class="mv-document-name">
                                    {{ $attachment->file_name ?? 'Documento' }}
                                </span>

                                <span class="mv-document-type">
                                    {{ strtoupper($extension ?: 'FILE') }}
                                </span>

                            </div>

                            <svg
                                width="17"
                                height="17"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    d="M7 17 17 7"
                                    stroke-linecap="round"
                                />

                                <path
                                    d="M7 7h10v10"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                        </a>

                    @endforeach

                </div>

            </section>

        @endif


        {{-- =====================================================
             META
        ====================================================== --}}

        <div class="mv-meta">

            <div class="mv-meta-item">

                <div class="mv-meta-icon">

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
                            height="18"
                            rx="2"
                        />

                        <path
                            d="M16 2v4M8 2v4M3 10h18"
                            stroke-linecap="round"
                        />
                    </svg>

                </div>

                <div class="mv-meta-text">

                    <span>
                        Creato il
                    </span>

                    <strong>
                        {{ $formatDateTime($material->created_at) }}
                    </strong>

                </div>

            </div>


            <div class="mv-meta-item">

                <div class="mv-meta-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            d="M20 11a8.1 8.1 0 0 0-14.9-4"
                            stroke-linecap="round"
                        />

                        <path
                            d="M4 4v5h5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M4 13a8.1 8.1 0 0 0 14.9 4"
                            stroke-linecap="round"
                        />

                        <path
                            d="M20 20v-5h-5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                </div>

                <div class="mv-meta-text">

                    <span>
                        Ultima modifica
                    </span>

                    <strong>
                        {{ $formatDateTime($material->updated_at) }}
                    </strong>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         LIGHTBOX IMMAGINE
    ========================================================== --}}

    <div
        id="material-lightbox"
        class="mv-lightbox"
        onclick="closeMaterialImage(event)"
    >

        <button
            type="button"
            class="mv-lightbox-close"
            onclick="closeMaterialImage()"
            aria-label="Chiudi immagine"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    d="M18 6 6 18"
                    stroke-linecap="round"
                />

                <path
                    d="m6 6 12 12"
                    stroke-linecap="round"
                />
            </svg>

        </button>

        <img
            id="material-lightbox-image"
            class="mv-lightbox-image"
            src=""
            alt="Immagine materiale"
        >

    </div>


    <script>
        function openMaterialImage(url) {
            const lightbox = document.getElementById('material-lightbox');
            const image = document.getElementById('material-lightbox-image');

            image.src = url;

            lightbox.classList.add('active');

            document.body.style.overflow = 'hidden';
        }

        function closeMaterialImage(event = null) {

            if (
                event &&
                event.target &&
                !event.target.classList.contains('mv-lightbox')
            ) {
                return;
            }

            const lightbox = document.getElementById('material-lightbox');
            const image = document.getElementById('material-lightbox-image');

            lightbox.classList.remove('active');

            image.src = '';

            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function (event) {

            if (event.key === 'Escape') {
                closeMaterialImage();
            }

        });
    </script>

</div>