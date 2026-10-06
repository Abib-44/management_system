<div>
    @php
        $student = $this->record;

        /* STUDENTE */
        $fullName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));

        $initials = collect([$student->first_name, $student->last_name])
            ->filter()
            ->map(fn ($name) => mb_strtoupper(mb_substr($name, 0, 1)))
            ->implode('');

        /* PAGAMENTO */
        $paymentStatus = match ($student->payment_status) {
            'paid' => ['label' => 'Pagato', 'class' => 'success'],
            'partially_paid' => ['label' => 'Parzialmente pagato', 'class' => 'warning'],
            'not_paid' => ['label' => 'Non pagato', 'class' => 'danger'],
            default => ['label' => 'N/D', 'class' => 'neutral'],
        };

        $paymentPlan = match ($student->installment_plan) {
            'no_interest' => 'Pagamento unico',
            'installment_1' => '1 rata',
            'installment_2' => '2 rate',
            default => 'Non specificato',
        };

        $totalAmount = (float) ($student->total_fee ?? 0);
        $paidAmount = (float) ($student->paid_amount ?? 0);
        $remainingAmount = (float) ($student->remaining_amount ?? 0);

        $paymentPercentage = $totalAmount > 0
            ? round(min(($paidAmount / $totalAmount) * 100, 100), 1)
            : 0;

        /* DATE */
        $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '—';
        $formatDateTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y · H:i') : '—';

        /* SCHEDA VALUTATIVA
           Imposta qui la route/URL della scheda valutativa.
           Finché è null il pulsante viene mostrato disabilitato. */
$evaluationUrl = \App\Filament\Resources\Students\StudentResource::getUrl('academic-record', ['record' => $student]);
        /*
        | DOCUMENTI
        | students -> document_archive_links -> document_archives -> attachments
        */
        $documentArchiveIds = \App\Models\DocumentArchiveLink::query()
            ->where('entity_type', 'student')
            ->where('student_id', $student->id)
            ->pluck('document_archive_id')
            ->filter()
            ->unique()
            ->values();

        $attachments = $documentArchiveIds->isNotEmpty()
            ? \App\Models\Attachment::query()
                ->where('attachable_type', \App\Models\DocumentArchive::class)
                ->whereIn('attachable_id', $documentArchiveIds)
                ->orderBy('attachable_id')
                ->orderBy('version')
                ->orderBy('id')
                ->get()
            : collect();

        /* FILTRAGGIO IMMAGINI / ALTRI FILE */
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif'];

        $isImage = function ($attachment) use ($imageExtensions) {
            $mimeType = strtolower((string) ($attachment->mime_type ?? ''));
            $fileName = strtolower((string) ($attachment->file_name ?? $attachment->name ?? ''));
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);

            return str_starts_with($mimeType, 'image/') || in_array($extension, $imageExtensions, true);
        };

        [$imageAttachments, $otherAttachments] = $attachments->partition($isImage);

        // Dati passati ad Alpine (galleria + lightbox)
        $galleryImages = $imageAttachments->values()->map(fn ($attachment) => [
            'url' => route('attachments.show', $attachment),
            'name' => $attachment->file_name ?? $attachment->name ?? 'Immagine',
        ])->all();
    @endphp

    <div class="student-profile">

        <script>
            window.studentGallery = window.studentGallery || function (images) {
                return {
                    images: images,
                    current: 0,
                    open: false,

                    go(index) {
                        this.current = (index + this.images.length) % this.images.length;
                    },

                    select(index) {
                        this.go(index);
                        this.$nextTick(() => {
                            const active = this.$refs.thumbs ? this.$refs.thumbs.querySelector('.is-active') : null;
                            if (active) {
                                active.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'smooth' });
                            }
                        });
                    },

                    onKey(event) {
                        if (!this.open) return;
                        if (event.key === 'Escape') this.open = false;
                        if (event.key === 'ArrowLeft') this.select(this.current - 1);
                        if (event.key === 'ArrowRight') this.select(this.current + 1);
                    },
                };
            };

            window.attendanceCalendar = window.attendanceCalendar || function () {
                const priority = { absent: 3, late: 2, present: 1, other: 0 };

                function detect(el) {
                    const text = ((el.className || '') + ' ' + (el.textContent || '')).toLowerCase();
                    if (/absent|assent/.test(text)) return 'absent';
                    if (/\blate\b|ritard/.test(text)) return 'late';
                    if (/present|presen/.test(text)) return 'present';
                    return 'other';
                }

                function eventColor(el) {
                    const style = getComputedStyle(el);
                    const colors = [style.backgroundColor, style.borderTopColor];
                    return colors.find(c => c && c !== 'transparent' && c !== 'rgba(0, 0, 0, 0)') || null;
                }

                return {
                    timer: null,

                    init() {
                        this.paint();
                        new MutationObserver(() => {
                            clearTimeout(this.timer);
                            this.timer = setTimeout(() => this.paint(), 60);
                        }).observe(this.$el, { childList: true, subtree: true });
                    },

                    paint() {
                        this.$el.querySelectorAll('td.fc-daygrid-day').forEach(cell => {
                            const events = cell.querySelectorAll('.fc-event, .fc-bg-event');

                            if (!events.length) {
                                cell.removeAttribute('data-attendance');
                                cell.style.removeProperty('--attendance-color');
                                return;
                            }

                            let best = null;
                            let bestEl = null;

                            events.forEach(el => {
                                const status = detect(el);
                                if (best === null || priority[status] > priority[best]) {
                                    best = status;
                                    bestEl = el;
                                }
                            });

                            const custom = best === 'other' ? eventColor(bestEl) : null;

                            if (custom) {
                                cell.style.setProperty('--attendance-color', custom);
                                cell.setAttribute('data-attendance', 'custom');
                            } else {
                                cell.style.removeProperty('--attendance-color');
                                cell.setAttribute('data-attendance', best);
                            }
                        });
                    },
                };
            };
        </script>

        <style>
            /* =========================================================
               BASE
            ========================================================= */
            .student-profile {
                --student-bg: #ffffff;
                --student-bg-subtle: #f8fafc;
                --student-bg-muted: #f1f5f9;
                --student-border: #e2e8f0;
                --student-border-soft: #edf1f5;
                --student-text: #0f172a;
                --student-text-secondary: #334155;
                --student-text-muted: #64748b;
                --student-text-placeholder: #94a3b8;
                --student-primary: #4f46e5;
                --student-primary-bg: #eef2ff;
                --student-success: #059669;
                --student-success-bg: #ecfdf5;
                --student-success-border: #a7f3d0;
                --student-warning: #c2410c;
                --student-warning-bg: #fff7ed;
                --student-warning-border: #fed7aa;
                --student-danger: #dc2626;
                --student-danger-bg: #fef2f2;
                --student-danger-border: #fecaca;
                --student-shadow: rgba(15, 23, 42, 0.05);

                --cell-present: rgba(34, 197, 94, .14);
                --cell-present-line: rgba(34, 197, 94, .40);
                --cell-absent: rgba(239, 68, 68, .18);
                --cell-absent-line: rgba(239, 68, 68, .50);
                --cell-late: rgba(249, 115, 22, .18);
                --cell-late-line: rgba(249, 115, 22, .50);
                --cell-other: rgba(59, 130, 246, .14);
                --cell-other-line: rgba(59, 130, 246, .40);

                width: 100%;
                margin-top: 24px;
                color: var(--student-text);
            }

            .dark .student-profile {
                --student-bg: #18181b;
                --student-bg-subtle: #202023;
                --student-bg-muted: #27272a;
                --student-border: #3f3f46;
                --student-border-soft: #303037;
                --student-text: #f4f4f5;
                --student-text-secondary: #d4d4d8;
                --student-text-muted: #a1a1aa;
                --student-text-placeholder: #71717a;
                --student-primary: #818cf8;
                --student-primary-bg: rgba(99, 102, 241, .15);
                --student-success: #34d399;
                --student-success-bg: rgba(16, 185, 129, .12);
                --student-success-border: rgba(52, 211, 153, .25);
                --student-warning: #fb923c;
                --student-warning-bg: rgba(249, 115, 22, .12);
                --student-warning-border: rgba(251, 146, 60, .25);
                --student-danger: #f87171;
                --student-danger-bg: rgba(239, 68, 68, .12);
                --student-danger-border: rgba(248, 113, 113, .25);
                --student-shadow: rgba(0, 0, 0, .25);

                --cell-present: rgba(34, 197, 94, .18);
                --cell-present-line: rgba(34, 197, 94, .45);
                --cell-absent: rgba(239, 68, 68, .24);
                --cell-absent-line: rgba(239, 68, 68, .55);
                --cell-late: rgba(249, 115, 22, .24);
                --cell-late-line: rgba(249, 115, 22, .55);
                --cell-other: rgba(59, 130, 246, .20);
                --cell-other-line: rgba(59, 130, 246, .45);
            }

            .student-profile *,
            .student-lightbox * { box-sizing: border-box; }

            /* =========================================================
               HERO
            ========================================================= */
            .student-hero {
                overflow: hidden;
                margin-bottom: 24px;
                border: 1px solid var(--student-border);
                border-radius: 16px;
                background: var(--student-bg);
                box-shadow: 0 2px 6px var(--student-shadow);
            }

            .student-hero-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 16px 24px;
                border-bottom: 1px solid var(--student-border-soft);
            }

            .student-hero-actions {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-shrink: 0;
            }

            .student-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                min-height: 36px;
                padding: 0 13px;
                border: 1px solid var(--student-border);
                border-radius: 9px;
                color: var(--student-text-secondary);
                background: var(--student-bg);
                font-size: 12px;
                font-weight: 650;
                text-decoration: none;
                box-shadow: 0 1px 2px var(--student-shadow);
                transition: color .15s, background .15s, border-color .15s, transform .15s;
            }

            a.student-btn:hover {
                color: var(--student-primary);
                background: var(--student-primary-bg);
                border-color: var(--student-primary);
                transform: translateY(-1px);
            }

            .student-btn.is-primary {
                color: #fff;
                background: var(--student-primary);
                border-color: var(--student-primary);
            }

            a.student-btn.is-primary:hover {
                color: #fff;
                background: var(--student-primary);
                filter: brightness(1.1);
            }

            .student-btn.is-disabled {
                opacity: .5;
                cursor: not-allowed;
            }

            .student-btn svg { width: 16px; height: 16px; flex-shrink: 0; }

            .student-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                color: var(--student-text-muted);
                font-size: 13px;
                font-weight: 600;
                text-decoration: none;
                transition: color .15s;
            }

            .student-back:hover { color: var(--student-primary); }
            .student-back svg { width: 17px; height: 17px; }

            .student-hero-content {
                display: flex;
                align-items: center;
                gap: 18px;
                padding: 24px;
            }

            .student-avatar {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 64px;
                height: 64px;
                flex-shrink: 0;
                border-radius: 14px;
                background: var(--student-primary-bg);
                color: var(--student-primary);
                font-size: 20px;
                font-weight: 750;
            }

            .student-identity { min-width: 0; flex: 1; }

            .student-identity h1 {
                margin: 0;
                color: var(--student-text);
                font-size: 22px;
                line-height: 1.3;
                font-weight: 700;
            }

            .student-email {
                display: inline-block;
                max-width: 100%;
                margin-top: 5px;
                overflow: hidden;
                color: var(--student-text-muted);
                font-size: 13px;
                text-decoration: none;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            a.student-email:hover { color: var(--student-primary); }

            .student-header-details { display: flex; align-items: stretch; flex-shrink: 0; }

            .student-header-detail {
                min-width: 130px;
                padding: 0 18px;
                border-left: 1px solid var(--student-border-soft);
            }

            .student-header-detail span {
                display: block;
                margin-bottom: 5px;
                color: var(--student-text-muted);
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .05em;
            }

            .student-header-detail strong { color: var(--student-text); font-size: 13px; font-weight: 650; }

            .student-header-status {
                display: flex;
                align-items: center;
                margin-left: 18px;
                padding-left: 18px;
                border-left: 1px solid var(--student-border-soft);
            }

            /* STATUS */
            .student-hero-status {
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

            .status-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

            .student-hero-status.success { color: var(--student-success); background: var(--student-success-bg); border-color: var(--student-success-border); }
            .student-hero-status.warning { color: var(--student-warning); background: var(--student-warning-bg); border-color: var(--student-warning-border); }
            .student-hero-status.danger  { color: var(--student-danger);  background: var(--student-danger-bg);  border-color: var(--student-danger-border); }
            .student-hero-status.neutral { color: var(--student-text-muted); background: var(--student-bg-muted); border-color: var(--student-border); }

            /* =========================================================
               GRID + CARD
            ========================================================= */
            .student-grid,
            .student-bottom-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 24px;
                align-items: start;
            }

            .student-grid { margin-bottom: 24px; align-items: stretch; }

            .student-card {
                overflow: hidden;
                border: 1px solid var(--student-border);
                border-radius: 16px;
                background: var(--student-bg);
                box-shadow: 0 2px 6px var(--student-shadow);
            }

            .student-card-header {
                display: flex;
                align-items: center;
                gap: 10px;
                min-height: 62px;
                padding: 14px 20px;
                border-bottom: 1px solid var(--student-border-soft);
            }

            .student-card-header-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                flex-shrink: 0;
                border-radius: 8px;
                background: var(--student-bg-muted);
                color: var(--student-text-secondary);
            }

            .student-card-header-icon svg { width: 17px; height: 17px; }

            .student-card-header h2 { margin: 0; color: var(--student-text); font-size: 14px; font-weight: 700; }

            .student-card-body { padding: 20px; }

            /* CONTATTI */
            .contact-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 22px 28px; }
            .contact-item { display: flex; align-items: flex-start; gap: 12px; min-width: 0; }
            .contact-item:last-child { grid-column: 1 / -1; }

            .contact-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 36px;
                height: 36px;
                flex-shrink: 0;
                border-radius: 9px;
                background: var(--student-bg-muted);
                color: var(--student-text-secondary);
            }

            .contact-icon svg { width: 17px; height: 17px; }

            .contact-content { min-width: 0; display: flex; flex-direction: column; gap: 4px; }

            .contact-label {
                color: var(--student-text-muted);
                font-size: 10px;
                font-weight: 650;
                text-transform: uppercase;
                letter-spacing: .05em;
            }

            .contact-content strong {
                color: var(--student-text);
                font-size: 13px;
                line-height: 1.5;
                font-weight: 550;
                overflow-wrap: anywhere;
            }

            .contact-content a { color: inherit; text-decoration: none; }
            .contact-content a:hover { color: var(--student-primary); text-decoration: underline; }

            .empty-value { color: var(--student-text-placeholder) !important; font-weight: 400 !important; }

            /* FINANZE */
            .financial-body { display: flex; flex-direction: column; gap: 18px; }

            .financial-total {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-bottom: 17px;
                border-bottom: 1px solid var(--student-border-soft);
            }

            .financial-total span { color: var(--student-text-muted); font-size: 12px; font-weight: 500; }
            .financial-total strong { color: var(--student-text); font-size: 23px; line-height: 1; font-weight: 750; }

            .financial-values { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }

            .financial-value {
                padding: 14px;
                border: 1px solid var(--student-border-soft);
                border-radius: 10px;
                background: var(--student-bg-subtle);
            }

            .financial-value span { display: block; margin-bottom: 6px; color: var(--student-text-muted); font-size: 11px; }
            .financial-value strong { color: var(--student-text); font-size: 16px; font-weight: 700; }
            .financial-value strong.paid { color: var(--student-success); }
            .financial-value.remaining strong { color: var(--student-danger); }

            .financial-progress-row { display: flex; align-items: center; gap: 12px; }

            .financial-progress { height: 8px; flex: 1; overflow: hidden; border-radius: 999px; background: var(--student-bg-muted); }
            .financial-progress-fill { height: 100%; border-radius: inherit; background: var(--student-success); transition: width .3s ease; }

            .financial-progress-row > strong {
                min-width: 40px;
                color: var(--student-text-secondary);
                font-size: 12px;
                font-weight: 700;
                text-align: right;
            }

            .financial-plan {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 12px 14px;
                border-radius: 10px;
                background: var(--student-bg-subtle);
            }

            .financial-plan-label { color: var(--student-text-muted); font-size: 11px; }
            .financial-plan-value { color: var(--student-text); font-size: 12px; font-weight: 650; }

            .financial-meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; padding-top: 2px; }
            .financial-meta-item { display: flex; flex-direction: column; gap: 4px; }

            .financial-meta-item span {
                color: var(--student-text-muted);
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .04em;
            }

            .financial-meta-item strong { color: var(--student-text-secondary); font-size: 11px; font-weight: 500; }

            /* =========================================================
               DOCUMENTI
            ========================================================= */
            .documents-gallery { padding: 20px; }

            .document-image-gallery {
                display: grid;
                grid-template-columns: minmax(0, 1fr) 108px;
                gap: 14px;
                height: 520px;
            }

            .document-main-image {
                position: relative;
                overflow: hidden;
                min-width: 0;
                height: 100%;
                border: 1px solid var(--student-border);
                border-radius: 14px;
                background: var(--student-bg-subtle);
                cursor: zoom-in;
                transition: border-color .2s, box-shadow .2s;
            }

            .document-main-image:hover,
            .document-main-image:focus-visible {
                outline: none;
                border-color: var(--student-primary);
                box-shadow: 0 8px 24px var(--student-shadow);
            }

            .document-main-image img { display: block; width: 100%; height: 100%; object-fit: contain; }

            .main-image-overlay {
                position: absolute;
                right: 14px;
                bottom: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 10px;
                color: #fff;
                background: rgba(0, 0, 0, .60);
                backdrop-filter: blur(6px);
                opacity: 0;
                transition: opacity .2s;
            }

            .document-main-image:hover .main-image-overlay,
            .document-main-image:focus-visible .main-image-overlay { opacity: 1; }

            .main-image-overlay svg { width: 18px; height: 18px; }

            .document-thumbnails {
                display: flex;
                flex-direction: column;
                gap: 10px;
                min-width: 0;
                height: 100%;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 2px 3px 2px 2px;
                scrollbar-width: thin;
            }

            .document-thumbnail {
                position: relative;
                display: block;
                width: 100%;
                height: 82px;
                flex-shrink: 0;
                padding: 0;
                overflow: hidden;
                border: 2px solid transparent;
                border-radius: 10px;
                background: var(--student-bg-subtle);
                cursor: pointer;
                transition: border-color .18s, opacity .18s;
            }

            .document-thumbnail:hover { opacity: .85; }

            .document-thumbnail.is-active {
                border-color: var(--student-primary);
                box-shadow: 0 0 0 1px var(--student-primary);
            }

            .document-thumbnail img { display: block; width: 100%; height: 100%; object-fit: cover; }

            .document-thumbnail-index {
                position: absolute;
                right: 5px;
                bottom: 5px;
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 20px;
                height: 20px;
                padding: 0 5px;
                border-radius: 5px;
                color: #fff;
                background: rgba(0, 0, 0, .65);
                font-size: 9px;
                font-weight: 700;
            }

            .other-documents { margin-top: 18px; }
            .other-documents:first-child { margin-top: 0; }

            .other-documents-title {
                margin-bottom: 10px;
                color: var(--student-text-muted);
                font-size: 11px;
                font-weight: 650;
                text-transform: uppercase;
                letter-spacing: .05em;
            }

            .documents-list { display: flex; flex-direction: column; gap: 8px; margin: 0; padding: 0; list-style: none; }

            .document-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 11px 12px;
                border: 1px solid var(--student-border-soft);
                border-radius: 9px;
                background: var(--student-bg-subtle);
            }

            .document-item-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 34px;
                height: 34px;
                flex-shrink: 0;
                border-radius: 8px;
                background: var(--student-bg-muted);
                color: var(--student-text-muted);
            }

            .document-item-icon svg { width: 17px; height: 17px; }

            .document-item-info { min-width: 0; flex: 1; }

            .document-item a {
                display: block;
                overflow: hidden;
                color: var(--student-primary);
                font-size: 13px;
                font-weight: 600;
                text-decoration: none;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .document-item a:hover { text-decoration: underline; }

            /* NOTE */
            .notes-content {
                padding: 20px;
                color: var(--student-text-secondary);
                font-size: 13px;
                line-height: 1.7;
                white-space: pre-line;
                overflow-wrap: anywhere;
            }

            .student-empty { color: var(--student-text-placeholder); font-size: 13px; }

            /* =========================================================
               CALENDARIO
            ========================================================= */
            .student-calendar-section { width: 100%; margin: 30px auto 40px; }

            .student-calendar-card {
                width: 100%;
                overflow: hidden;
                border: 1px solid var(--student-border);
                border-radius: 18px;
                background: var(--student-bg);
                box-shadow: 0 1px 2px var(--student-shadow), 0 10px 30px var(--student-shadow);
            }

            .student-calendar-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 24px;
                padding: 20px 24px;
                border-bottom: 1px solid var(--student-border-soft);
                background: linear-gradient(180deg, var(--student-bg), var(--student-bg-subtle));
            }

            .student-calendar-heading { display: flex; align-items: center; gap: 13px; min-width: 0; }

            .student-calendar-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                flex-shrink: 0;
                border: 1px solid rgba(99, 102, 241, .16);
                border-radius: 12px;
                color: var(--student-primary);
                background: var(--student-primary-bg);
            }

            .student-calendar-icon svg { width: 21px; height: 21px; }

            .student-calendar-title { margin: 0; color: var(--student-text); font-size: 17px; line-height: 1.3; font-weight: 750; letter-spacing: -.01em; }
            .student-calendar-subtitle { margin: 4px 0 0; color: var(--student-text-muted); font-size: 12px; line-height: 1.4; }

            .student-calendar-legend { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; justify-content: flex-end; }

            .student-calendar-legend-item {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 7px 9px;
                border: 1px solid var(--student-border-soft);
                border-radius: 999px;
                color: var(--student-text-muted);
                background: var(--student-bg);
                font-size: 10px;
                font-weight: 650;
                white-space: nowrap;
            }

            .student-calendar-dot { width: 7px; height: 7px; flex-shrink: 0; border-radius: 50%; }
            .student-calendar-dot.present { background: #22c55e; box-shadow: 0 0 0 3px rgba(34, 197, 94, .10); }
            .student-calendar-dot.absent  { background: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, .10); }
            .student-calendar-dot.late    { background: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, .10); }

            .student-calendar-body { position: relative; padding: 22px 24px 24px; background: var(--student-bg); }

            /* FULLCALENDAR */
            .student-calendar-body .fc { width: 100%; color: var(--student-text); font-family: inherit; font-size: 13px; }

            .student-calendar-body .fc .fc-toolbar { align-items: center; min-height: 42px; margin-bottom: 18px; gap: 12px; }

            .student-calendar-body .fc .fc-toolbar-title {
                color: var(--student-text);
                font-size: 18px;
                line-height: 1.3;
                font-weight: 750;
                letter-spacing: -.015em;
            }

            .student-calendar-body .fc .fc-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 34px;
                min-height: 34px;
                padding: 0 10px;
                border: 1px solid var(--student-border) !important;
                border-radius: 9px !important;
                color: var(--student-text-secondary) !important;
                background: var(--student-bg) !important;
                font-family: inherit;
                font-size: 11px;
                font-weight: 650;
                box-shadow: 0 1px 2px var(--student-shadow) !important;
                transition: color .15s, background .15s, border-color .15s, transform .15s;
            }

            .student-calendar-body .fc .fc-button:hover {
                color: var(--student-primary) !important;
                background: var(--student-primary-bg) !important;
                border-color: var(--student-primary) !important;
                transform: translateY(-1px);
            }

            .student-calendar-body .fc .fc-button:focus { outline: none !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, .15) !important; }

            .student-calendar-body .fc .fc-button-primary:not(:disabled).fc-button-active {
                color: #fff !important;
                background: var(--student-primary) !important;
                border-color: var(--student-primary) !important;
            }

            .student-calendar-body .fc .fc-scrollgrid {
                overflow: hidden;
                border: 1px solid var(--student-border) !important;
                border-radius: 14px;
                background: var(--student-bg);
            }

            .student-calendar-body .fc td,
            .student-calendar-body .fc th { border-color: var(--student-border-soft) !important; }

            .student-calendar-body .fc .fc-col-header-cell { background: var(--student-bg-subtle); }

            .student-calendar-body .fc .fc-col-header-cell-cushion {
                display: block;
                padding: 11px 4px;
                color: var(--student-text-muted);
                font-size: 9px;
                font-weight: 750;
                text-transform: uppercase;
                text-decoration: none;
                letter-spacing: .08em;
            }

            .student-calendar-body .fc .fc-daygrid-day { min-height: 74px; background: var(--student-bg); transition: background .15s; }
            .student-calendar-body .fc .fc-daygrid-day:hover { background: var(--student-bg-subtle); }
            .student-calendar-body .fc .fc-daygrid-day-frame { min-height: 74px; padding: 4px; }

            .student-calendar-body .fc .fc-daygrid-day-number {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 27px;
                height: 27px;
                margin: 4px;
                border-radius: 50%;
                color: var(--student-text-secondary);
                font-size: 11px;
                font-weight: 650;
                text-decoration: none;
            }

            .student-calendar-body .fc .fc-day-today { background: color-mix(in srgb, var(--student-primary-bg) 55%, var(--student-bg)) !important; }

            .student-calendar-body .fc .fc-day-today .fc-daygrid-day-number {
                color: #fff;
                background: var(--student-primary);
                font-weight: 750;
                box-shadow: 0 2px 6px rgba(79, 70, 229, .25);
            }

            .student-calendar-body .fc .fc-day-other { background: var(--student-bg-subtle); }
            .student-calendar-body .fc .fc-day-other .fc-daygrid-day-number { color: var(--student-text-placeholder); }

            /* Caselle colorate (data-attendance impostato da Alpine) */
            .student-calendar-body .fc td.fc-daygrid-day[data-attendance="present"] { background-color: var(--cell-present) !important; box-shadow: inset 0 0 0 1px var(--cell-present-line); }
            .student-calendar-body .fc td.fc-daygrid-day[data-attendance="absent"]  { background-color: var(--cell-absent)  !important; box-shadow: inset 0 0 0 1px var(--cell-absent-line); }
            .student-calendar-body .fc td.fc-daygrid-day[data-attendance="late"]    { background-color: var(--cell-late)    !important; box-shadow: inset 0 0 0 1px var(--cell-late-line); }
            .student-calendar-body .fc td.fc-daygrid-day[data-attendance="other"]   { background-color: var(--cell-other)   !important; box-shadow: inset 0 0 0 1px var(--cell-other-line); }

            .student-calendar-body .fc td.fc-daygrid-day[data-attendance="custom"] {
                background-color: color-mix(in srgb, var(--attendance-color) 18%, transparent) !important;
                box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--attendance-color) 45%, transparent);
            }

            .student-calendar-body .fc td.fc-daygrid-day[data-attendance]:hover { filter: brightness(.97); }
            .student-calendar-body .fc td.fc-daygrid-day[data-attendance] .fc-daygrid-day-frame { background: transparent; }

            /* Eventi */
            .student-calendar-body .fc .fc-daygrid-event {
                margin: 3px 4px;
                padding: 4px 7px;
                border: 0 !important;
                border-radius: 7px;
                font-size: 9px;
                line-height: 1.3;
                font-weight: 700;
                box-shadow: none !important;
                transition: transform .15s, filter .15s;
            }

            .student-calendar-body .fc .fc-daygrid-event:hover { transform: translateY(-1px); filter: brightness(.98); }
            .student-calendar-body .fc .fc-event-main { color: inherit; }

            .student-calendar-body .fc .attendance-present { color: #166534 !important; background: #dcfce7 !important; }
            .student-calendar-body .fc .attendance-absent  { color: #991b1b !important; background: #fee2e2 !important; }
            .student-calendar-body .fc .attendance-late    { color: #9a3412 !important; background: #ffedd5 !important; }
            .student-calendar-body .fc .fc-daygrid-event:not(.attendance-present):not(.attendance-absent):not(.attendance-late) { color: #1d4ed8 !important; background: #eff6ff !important; }

            .dark .student-calendar-body .fc .attendance-present { color: #86efac !important; background: rgba(34, 197, 94, .14) !important; }
            .dark .student-calendar-body .fc .attendance-absent  { color: #fca5a5 !important; background: rgba(239, 68, 68, .14) !important; }
            .dark .student-calendar-body .fc .attendance-late    { color: #fdba74 !important; background: rgba(249, 115, 22, .14) !important; }
            .dark .student-calendar-body .fc .fc-daygrid-event:not(.attendance-present):not(.attendance-absent):not(.attendance-late) { color: #93c5fd !important; background: rgba(59, 130, 246, .14) !important; }

            .student-calendar-body .fc .fc-daygrid-more-link { color: var(--student-primary); font-size: 9px; font-weight: 700; text-decoration: none; }

            .student-calendar-body .fc .fc-popover {
                overflow: hidden;
                border: 1px solid var(--student-border);
                border-radius: 12px;
                background: var(--student-bg);
                box-shadow: 0 12px 35px rgba(15, 23, 42, .14);
            }

            .student-calendar-body .fc .fc-popover-header { padding: 10px 12px; color: var(--student-text); background: var(--student-bg-subtle); font-size: 11px; font-weight: 700; }

            /* =========================================================
               LIGHTBOX (teleportato nel <body>: colori non dipendono dalle variabili)
            ========================================================= */
            .student-lightbox {
                position: fixed;
                inset: 0;
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 30px;
                background: rgba(0, 0, 0, .88);
                backdrop-filter: blur(8px);
            }

            .lightbox-content { display: flex; align-items: center; justify-content: center; width: min(1400px, 100%); height: 90vh; }

            .lightbox-image { max-width: 100%; max-height: 90vh; object-fit: contain; border-radius: 8px; box-shadow: 0 20px 70px rgba(0, 0, 0, .45); }

            .lightbox-close,
            .lightbox-prev,
            .lightbox-next {
                position: fixed;
                z-index: 2;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 50%;
                color: #fff;
                background: rgba(255, 255, 255, .12);
                cursor: pointer;
                backdrop-filter: blur(8px);
                transition: background .15s;
            }

            .lightbox-close:hover,
            .lightbox-prev:hover,
            .lightbox-next:hover { background: rgba(255, 255, 255, .22); }

            .lightbox-close { top: 20px; right: 24px; width: 44px; height: 44px; }
            .lightbox-prev, .lightbox-next { top: 50%; width: 48px; height: 48px; margin-top: -24px; }
            .lightbox-prev { left: 24px; }
            .lightbox-next { right: 24px; }

            .lightbox-close svg, .lightbox-prev svg, .lightbox-next svg { width: 21px; height: 21px; }

            .lightbox-counter {
                position: fixed;
                bottom: 22px;
                left: 50%;
                transform: translateX(-50%);
                padding: 7px 12px;
                border-radius: 999px;
                color: #fff;
                background: rgba(0, 0, 0, .55);
                font-size: 12px;
                font-weight: 600;
                backdrop-filter: blur(8px);
            }

            /* =========================================================
               RESPONSIVE
            ========================================================= */
            @media (max-width: 1100px) {
                .student-header-details { display: none; }
                .student-header-status { margin-left: 0; padding-left: 0; border-left: 0; }
            }

            @media (max-width: 900px) {
                .student-grid, .student-bottom-grid { grid-template-columns: 1fr; }
            }

            @media (max-width: 768px) {
                .student-calendar-section { margin: 20px auto 28px; }
                .student-calendar-header { align-items: flex-start; flex-direction: column; gap: 14px; padding: 18px; }
                .student-calendar-legend { width: 100%; justify-content: flex-start; }
                .student-calendar-body { padding: 14px; }
                .student-calendar-body .fc .fc-toolbar { flex-wrap: wrap; gap: 10px; margin-bottom: 14px; }
                .student-calendar-body .fc .fc-toolbar-title { width: 100%; order: -1; text-align: center; font-size: 16px; }
                .student-calendar-body .fc .fc-daygrid-day,
                .student-calendar-body .fc .fc-daygrid-day-frame { min-height: 58px; }
                .student-calendar-body .fc .fc-daygrid-event { padding: 3px 4px; font-size: 8px; }
                .student-calendar-body .fc .fc-col-header-cell-cushion { padding: 8px 2px; font-size: 8px; }
            }

            @media (max-width: 640px) {
                .student-profile { margin-top: 16px; }
                .student-hero-top { padding: 12px 16px; }
                .student-btn { min-height: 34px; padding: 0 10px; }
                .student-btn span { display: none; }
                .student-hero-content { align-items: flex-start; flex-wrap: wrap; padding: 18px 16px; }
                .student-avatar { width: 54px; height: 54px; font-size: 17px; }
                .student-identity { width: calc(100% - 72px); flex: none; }
                .student-identity h1 { font-size: 18px; }
                .student-header-status { width: 100%; margin: 4px 0 0; padding-left: 72px; border-left: 0; }
                .student-card-header { min-height: 56px; padding: 12px 16px; }
                .student-card-body { padding: 16px; }
                .contact-list { grid-template-columns: 1fr; gap: 18px; }
                .contact-item:last-child { grid-column: auto; }
                .financial-values, .financial-meta { grid-template-columns: 1fr; }
                .document-image-gallery { grid-template-columns: 1fr; grid-template-rows: 360px 86px; height: auto; gap: 12px; }
                .document-thumbnails { flex-direction: row; width: 100%; height: 86px; overflow-x: auto; overflow-y: hidden; padding: 2px 2px 3px; }
                .document-thumbnail { width: 82px; height: 78px; }
                .student-calendar-header { padding: 16px; }
                .student-calendar-body { padding: 10px; }
                .student-calendar-heading { width: 100%; }
                .student-calendar-icon { width: 38px; height: 38px; }
                .student-calendar-title { font-size: 16px; }
                .student-calendar-legend { gap: 5px; }
                .student-calendar-legend-item { padding: 6px 8px; font-size: 9px; }
                .student-calendar-body .fc .fc-daygrid-day,
                .student-calendar-body .fc .fc-daygrid-day-frame { min-height: 50px; }
                .student-calendar-body .fc .fc-daygrid-day-frame { padding: 2px; }
                .student-calendar-body .fc .fc-daygrid-day-number { width: 24px; height: 24px; margin: 2px; font-size: 10px; }
                .student-calendar-body .fc .fc-daygrid-event { margin: 2px; padding: 2px 3px; border-radius: 5px; font-size: 7px; }
                .student-lightbox { padding: 15px; }
                .lightbox-prev { left: 10px; }
                .lightbox-next { right: 10px; }
                .lightbox-close { top: 10px; right: 10px; }
            }
        </style>


        {{-- HERO --}}
        <div class="student-hero">

            <div class="student-hero-top">

                <a href="{{ \App\Filament\Resources\Students\StudentResource::getUrl('index') }}" class="student-back">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Studenti</span>
                </a>

                <div class="student-hero-actions">

                    {{-- Scheda valutativa --}}
<a href="{{ $evaluationUrl }}" class="student-btn is-primary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M9 11l3 3L22 4" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
    <span>Scheda valutativa</span>
</a>

                    <a
                        href="{{ \App\Filament\Resources\Students\StudentResource::getUrl('edit', ['record' => $student]) }}"
                        class="student-btn"
                        title="Modifica studente"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 20h9" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Modifica</span>
                    </a>

                </div>

            </div>

            <div class="student-hero-content">

                <div class="student-avatar">{{ $initials ?: 'S' }}</div>

                <div class="student-identity">
                    <h1>{{ $fullName ?: 'Studente senza nome' }}</h1>

                    @if ($student->email)
                        <a href="mailto:{{ $student->email }}" class="student-email">{{ $student->email }}</a>
                    @endif
                </div>

                <div class="student-header-details">

                    <div class="student-header-detail">
                        <span>Data di nascita</span>
                        <strong>{{ $formatDate($student->birth_date) }}</strong>
                    </div>

                    <div class="student-header-detail">
                        <span>Classe</span>
                        <strong class="{{ $student->classRoom ? '' : 'empty-value' }}">
                            {{ $student->classRoom?->name ?? 'Non assegnata' }}
                        </strong>
                    </div>

                </div>

                <div class="student-header-status">
                    <div class="student-hero-status {{ $paymentStatus['class'] }}">
                        <span class="status-dot"></span>
                        {{ $paymentStatus['label'] }}
                    </div>
                </div>

            </div>

        </div>


        {{-- MAIN GRID --}}
        <div class="student-grid">

            {{-- CONTATTI --}}
            <section class="student-card">

                <div class="student-card-header">
                    <div class="student-card-header-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h2>Genitori e contatti</h2>
                </div>

                <div class="student-card-body">
                    <div class="contact-list">

                        @php
                            $personIcon = '<circle cx="12" cy="8" r="4" /><path d="M4 21a8 8 0 0 1 16 0" stroke-linecap="round" />';
                            $phoneIcon = '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke-linecap="round" stroke-linejoin="round" />';
                            $pinIcon = '<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0z" stroke-linecap="round" stroke-linejoin="round" /><circle cx="12" cy="10" r="2.5" />';

                            $contacts = [
                                ['label' => 'Padre', 'value' => $student->father_name, 'empty' => 'Non specificato', 'icon' => $personIcon, 'tel' => false],
                                ['label' => 'Madre', 'value' => $student->mother_name, 'empty' => 'Non specificata', 'icon' => $personIcon, 'tel' => false],
                                ['label' => 'Telefono padre', 'value' => $student->father_phone, 'empty' => 'Non specificato', 'icon' => $phoneIcon, 'tel' => true],
                                ['label' => 'Telefono madre', 'value' => $student->mother_phone, 'empty' => 'Non specificato', 'icon' => $phoneIcon, 'tel' => true],
                                ['label' => 'Indirizzo', 'value' => $student->address, 'empty' => 'Non specificato', 'icon' => $pinIcon, 'tel' => false],
                            ];
                        @endphp

                        @foreach ($contacts as $contact)
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">{!! $contact['icon'] !!}</svg>
                                </div>
                                <div class="contact-content">
                                    <span class="contact-label">{{ $contact['label'] }}</span>
                                    <strong class="{{ $contact['value'] ? '' : 'empty-value' }}">
                                        @if ($contact['value'] && $contact['tel'])
                                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $contact['value']) }}">{{ $contact['value'] }}</a>
                                        @else
                                            {{ $contact['value'] ?: $contact['empty'] }}
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

            </section>


            {{-- FINANZE --}}
            <section class="student-card">

                <div class="student-card-header">
                    <div class="student-card-header-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 10h18" stroke-linecap="round" />
                            <path d="M5 10v8" stroke-linecap="round" />
                            <path d="M19 10v8" stroke-linecap="round" />
                            <path d="M3 18h18" stroke-linecap="round" />
                            <path d="M12 3l9 7H3l9-7z" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h2>Situazione finanziaria</h2>
                </div>

                <div class="student-card-body financial-body">

                    <div class="financial-total">
                        <span>Quota totale</span>
                        <strong>{{ number_format($totalAmount, 2, ',', '.') }} €</strong>
                    </div>

                    <div class="financial-values">
                        <div class="financial-value">
                            <span>Pagato</span>
                            <strong class="paid">{{ number_format($paidAmount, 2, ',', '.') }} €</strong>
                        </div>

                        <div class="financial-value remaining">
                            <span>Da pagare</span>
                            <strong>{{ number_format($remainingAmount, 2, ',', '.') }} €</strong>
                        </div>
                    </div>

                    <div class="financial-progress-row">
                        <div
                            class="financial-progress"
                            role="progressbar"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            aria-valuenow="{{ round($paymentPercentage) }}"
                        >
                            <div class="financial-progress-fill" style="width: {{ str_replace(',', '.', (string) $paymentPercentage) }}%;"></div>
                        </div>
                        <strong>{{ number_format($paymentPercentage, 0) }}%</strong>
                    </div>

                    <div class="financial-plan">
                        <span class="financial-plan-label">Piano di pagamento</span>
                        <span class="financial-plan-value">{{ $paymentPlan }}</span>
                    </div>

                    <div class="financial-meta">
                        <div class="financial-meta-item">
                            <span>Creato il</span>
                            <strong>{{ $formatDateTime($student->created_at) }}</strong>
                        </div>

                        <div class="financial-meta-item">
                            <span>Ultimo aggiornamento</span>
                            <strong>{{ $formatDateTime($student->updated_at) }}</strong>
                        </div>
                    </div>

                </div>

            </section>

        </div>


        {{-- DOCUMENTI + NOTE --}}
        <div class="student-bottom-grid">

            {{-- DOCUMENTI (galleria + lightbox gestiti da Alpine: nessun <script> da rieseguire con Livewire) --}}
            <section
                class="student-card"
                x-data="studentGallery(@js($galleryImages))"
                x-effect="document.body.style.overflow = open ? 'hidden' : ''"
                x-on:keydown.window="onKey($event)"
            >

                <div class="student-card-header">
                    <div class="student-card-header-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h2>Documenti</h2>
                </div>

                <div class="documents-gallery">

                    @if ($imageAttachments->isNotEmpty())

                        <div class="document-image-gallery">

                            <div
                                class="document-main-image"
                                role="button"
                                tabindex="0"
                                x-bind:aria-label="'Apri ' + images[current].name"
                                x-on:click="open = true"
                                x-on:keydown.enter.prevent="open = true"
                                x-on:keydown.space.prevent="open = true"
                            >
                                <img x-bind:src="images[current].url" x-bind:alt="images[current].name" src="{{ $galleryImages[0]['url'] }}" alt="{{ $galleryImages[0]['name'] }}">

                                <div class="main-image-overlay" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M15 3h6v6" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M10 14L21 3" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                            </div>

                            <div class="document-thumbnails" x-ref="thumbs">
                                @foreach ($galleryImages as $index => $image)
                                    <button
                                        type="button"
                                        class="document-thumbnail"
                                        x-bind:class="{ 'is-active': current === {{ $index }} }"
                                        x-on:click="select({{ $index }})"
                                        aria-label="Visualizza {{ $image['name'] }}"
                                    >
                                        <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                                        <span class="document-thumbnail-index">{{ $index + 1 }}</span>
                                    </button>
                                @endforeach
                            </div>

                        </div>

                    @endif


                    @if ($otherAttachments->isNotEmpty())

                        <div class="other-documents">

                            <div class="other-documents-title">
                                {{ $imageAttachments->isNotEmpty() ? 'Altri documenti' : 'Documenti' }}
                            </div>

                            <ul class="documents-list">
                                @foreach ($otherAttachments as $attachment)
                                    @php
                                        $documentUrl = route('attachments.show', $attachment);
                                        $fileName = $attachment->file_name ?? $attachment->name ?? 'Documento';
                                    @endphp

                                    <li class="document-item">
                                        <div class="document-item-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>

                                        <div class="document-item-info">
                                            <a href="{{ $documentUrl }}" target="_blank" rel="noopener noreferrer" title="{{ $fileName }}">{{ $fileName }}</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                        </div>

                    @endif


                    @if ($attachments->isEmpty())
                        <span class="student-empty">Nessun documento connesso.</span>
                    @endif

                </div>


                {{-- LIGHTBOX --}}
                @if ($imageAttachments->isNotEmpty())
                    <template x-teleport="body">
                        <div
                            class="student-lightbox"
                            x-show="open"
                            x-cloak
                            x-transition.opacity.duration.150ms
                            x-on:click.self="open = false"
                            role="dialog"
                            aria-modal="true"
                            aria-label="Anteprima documento"
                        >

                            <button type="button" class="lightbox-close" x-on:click="open = false" aria-label="Chiudi">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 6l12 12" stroke-linecap="round" />
                                    <path d="M18 6L6 18" stroke-linecap="round" />
                                </svg>
                            </button>

                            @if ($imageAttachments->count() > 1)
                                <button type="button" class="lightbox-prev" x-on:click.stop="select(current - 1)" aria-label="Immagine precedente">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>

                                <button type="button" class="lightbox-next" x-on:click.stop="select(current + 1)" aria-label="Immagine successiva">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 18l6-6-6-6" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            @endif

                            <div class="lightbox-content" x-on:click.self="open = false">
                                <img class="lightbox-image" x-bind:src="open ? images[current].url : ''" x-bind:alt="images[current].name" alt="">
                            </div>

                            @if ($imageAttachments->count() > 1)
                                <div class="lightbox-counter">
                                    <span x-text="current + 1"></span> / {{ $imageAttachments->count() }}
                                </div>
                            @endif

                        </div>
                    </template>
                @endif

            </section>


            {{-- NOTE --}}
            <section class="student-card">

                <div class="student-card-header">
                    <div class="student-card-header-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 4h16v16H4z" stroke-linejoin="round" />
                            <path d="M8 9h8M8 13h8M8 17h5" stroke-linecap="round" />
                        </svg>
                    </div>
                    <h2>Note</h2>
                </div>

                <div class="notes-content">
                    @if ($student->notes)
                        {{ $student->notes }}
                    @else
                        <span class="student-empty">Nessuna nota disponibile.</span>
                    @endif
                </div>

            </section>

        </div>


        {{-- CALENDARIO PRESENZE --}}
        <div class="student-calendar-section">

            <div class="student-calendar-card">

                <div class="student-calendar-header">

                    <div class="student-calendar-heading">
                        <div class="student-calendar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="3" y="4" width="18" height="17" rx="3" />
                                <path d="M8 2v4M16 2v4M3 9h18" stroke-linecap="round" />
                                <path d="M8 13h.01M12 13h.01M16 13h.01M8 17h.01M12 17h.01" stroke-linecap="round" stroke-width="2.5" />
                            </svg>
                        </div>

                        <div>
                            <h2 class="student-calendar-title">Calendario presenze</h2>
                            <p class="student-calendar-subtitle">{{ $fullName ?: 'Studente' }}</p>
                        </div>
                    </div>

                    <div class="student-calendar-legend">
                        <span class="student-calendar-legend-item"><span class="student-calendar-dot present"></span>Presente</span>
                        <span class="student-calendar-legend-item"><span class="student-calendar-dot absent"></span>Assente</span>
                        <span class="student-calendar-legend-item"><span class="student-calendar-dot late"></span>Ritardo</span>
                    </div>

                </div>

                {{-- Colorazione caselle: Alpine + MutationObserver, si riattiva ad ogni re-render di FullCalendar/Livewire --}}
                <div
                    class="student-calendar-body"
                    x-data="attendanceCalendar()"
                >
                    @livewire(
                        \App\Filament\Widgets\AttendanceCalendarWidget::class,
                        ['record' => $student],
                        key('attendance-calendar-' . $student->id)
                    )
                </div>

            </div>

        </div>

    </div>
</div>