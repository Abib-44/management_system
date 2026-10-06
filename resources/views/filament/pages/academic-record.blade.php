<x-filament-panels::page>

    <style>
        .ar {
            --bg-card: #ffffff;
            --bg-soft: #f4f4f5;
            --border: #e4e4e7;
            --text: #18181b;
            --text-muted: #71717a;
            --accent: #6366f1;
            --accent-bg: #eef2ff;
            --green: #059669;
            --green-bg: #ecfdf5;
            --orange: #ea580c;
            --orange-bg: #fff7ed;
            --red: #dc2626;
            --red-bg: #fef2f2;

            display: flex;
            flex-direction: column;
            gap: 20px;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text);
        }

        .dark .ar {
            --bg-card: #161618;
            --bg-soft: #1f1f22;
            --border: #2a2a2e;
            --text: #fafafa;
            --text-muted: #8b8b93;
            --accent: #818cf8;
            --accent-bg: #25254a;
            --green: #34d399;
            --green-bg: rgba(52, 211, 153, .1);
            --orange: #fb923c;
            --orange-bg: rgba(251, 146, 60, .12);
            --red: #f87171;
            --red-bg: rgba(248, 113, 113, .1);
        }

        .ar * { box-sizing: border-box; }

        .ar-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
        }

        .ar-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        /* HEADER */
        .ar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 24px;
        }

        .ar-header-left {
            display: flex;
            align-items: center;
            gap: 18px;
            min-width: 0;
        }

        .ar-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            flex-shrink: 0;
            border-radius: 16px;
            background: var(--accent-bg);
            color: var(--accent);
            font-size: 20px;
            font-weight: 700;
        }

        .ar-name {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.25;
            color: var(--text);
        }

        .ar-sub {
            margin-top: 4px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .ar-header-right {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .ar-meta {
            padding: 0 24px;
            border-left: 1px solid var(--border);
        }

        .ar-meta-value {
            margin-top: 6px;
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }

        .ar-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 24px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .ar-pill::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .ar-pill.green  { color: var(--green);  background: var(--green-bg);  border-color: color-mix(in srgb, var(--green) 35%, transparent); }
        .ar-pill.orange { color: var(--orange); background: var(--orange-bg); border-color: color-mix(in srgb, var(--orange) 35%, transparent); }
        .ar-pill.red    { color: var(--red);    background: var(--red-bg);    border-color: color-mix(in srgb, var(--red) 35%, transparent); }

        /* STATS */
        .ar-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .ar-stat {
            padding: 18px 20px;
        }

        .ar-stat-top {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ar-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            border-radius: 10px;
            background: var(--bg-soft);
            color: var(--text-muted);
        }

        .ar-icon svg { width: 17px; height: 17px; }

        .ar-stat-value {
            margin-top: 14px;
            font-size: 28px;
            font-weight: 700;
            line-height: 1.1;
            color: var(--text);
        }

        .ar-stat-value small {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .ar-stat-value.green { color: var(--green); }

        /* SUBJECTS */
        .ar-subjects {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .ar-subject-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
        }

        .ar-subject-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ar-subject-name {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }

        .ar-subject-count {
            margin-top: 2px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .ar-avg {
            min-width: 170px;
            text-align: right;
        }

        .ar-avg-value {
            margin-top: 2px;
            font-size: 22px;
            font-weight: 700;
        }

        .ar-avg-value.green  { color: var(--green); }
        .ar-avg-value.orange { color: var(--orange); }
        .ar-avg-value.red    { color: var(--red); }

        .ar-bar {
            height: 6px;
            margin-top: 8px;
            border-radius: 999px;
            background: var(--bg-soft);
            overflow: hidden;
        }

        .ar-bar > span {
            display: block;
            height: 100%;
            border-radius: 999px;
        }

        .ar-bar > span.green  { background: var(--green); }
        .ar-bar > span.orange { background: var(--orange); }
        .ar-bar > span.red    { background: var(--red); }

        .ar-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
        }

        .ar-row:last-child { border-bottom: 0; }

        .ar-row-content { flex: 1; min-width: 0; }

        .ar-row-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .ar-date {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .ar-badge {
            padding: 3px 8px;
            border-radius: 6px;
            background: var(--bg-soft);
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 600;
        }

        .ar-topic {
            margin-top: 8px;
            font-size: 14px;
            line-height: 1.5;
            color: var(--text-muted);
        }

        .ar-notes {
            margin-top: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            background: var(--bg-soft);
        }

        .ar-notes-text {
            margin-top: 3px;
            font-size: 13px;
            line-height: 1.5;
            color: var(--text);
        }

        .ar-grade {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 60px;
            height: 46px;
            padding: 0 12px;
            border: 1px solid;
            border-radius: 12px;
            font-size: 19px;
            font-weight: 700;
        }

        .ar-grade.green  { color: var(--green);  background: var(--green-bg);  border-color: color-mix(in srgb, var(--green) 30%, transparent); }
        .ar-grade.orange { color: var(--orange); background: var(--orange-bg); border-color: color-mix(in srgb, var(--orange) 30%, transparent); }
        .ar-grade.red    { color: var(--red);    background: var(--red-bg);    border-color: color-mix(in srgb, var(--red) 30%, transparent); }

        .ar-empty {
            padding: 48px 24px;
            text-align: center;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-muted);
            border-style: dashed;
        }

        @media (max-width: 1024px) {
            .ar-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 768px) {
            .ar-header { flex-direction: column; align-items: flex-start; }
            .ar-header-right { flex-wrap: wrap; row-gap: 12px; }
            .ar-meta:first-child { padding-left: 0; border-left: 0; }
            .ar-pill { margin-left: 0; }
        }

        @media (max-width: 640px) {
            .ar-stats { grid-template-columns: 1fr; }
            .ar-subject-head { flex-direction: column; align-items: stretch; }
            .ar-avg { text-align: left; min-width: 0; }
            .ar-row { flex-direction: column; }
        }
    </style>

    @php
        $tone = fn ($value) => $value === null ? 'orange' : ($value >= 6 ? 'green' : ($value >= 5 ? 'orange' : 'red'));
    @endphp

    <div class="ar">

        {{-- HEADER --}}
        <div class="ar-card ar-header">

            <div class="ar-header-left">
                <div class="ar-avatar">
                    {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                </div>

                <div style="min-width:0">
                    <h2 class="ar-name">{{ $student->first_name }} {{ $student->last_name }}</h2>
                    <div class="ar-sub">Scheda valutativa</div>
                </div>
            </div>

            <div class="ar-header-right">

                @if ($student->classRoom)
                    <div class="ar-meta">
                        <div class="ar-label">Classe</div>
                        <div class="ar-meta-value">{{ $student->classRoom->name }}</div>
                    </div>
                @endif

                <div class="ar-meta">
                    <div class="ar-label">Materie</div>
                    <div class="ar-meta-value">{{ $subjectCount }}</div>
                </div>

                @if ($generalAverage !== null)
                    <span class="ar-pill {{ $tone($generalAverage) }}">
                        Media {{ number_format($generalAverage, 1, ',', '') }}/10
                    </span>
                @endif

            </div>

        </div>


        {{-- STATS --}}
        <div class="ar-stats">

            <div class="ar-card ar-stat">
                <div class="ar-stat-top">
                    <div class="ar-icon"><x-heroicon-o-chart-bar /></div>
                    <div class="ar-label">Media generale</div>
                </div>
                <div class="ar-stat-value {{ $generalAverage !== null ? $tone($generalAverage) : '' }}"
                     @if ($generalAverage !== null) style="color: var(--{{ $tone($generalAverage) }})" @endif>
                    @if ($generalAverage !== null)
                        {{ number_format($generalAverage, 1, ',', '') }} <small>/10</small>
                    @else
                        —
                    @endif
                </div>
            </div>

            <div class="ar-card ar-stat">
                <div class="ar-stat-top">
                    <div class="ar-icon"><x-heroicon-o-clipboard-document-check /></div>
                    <div class="ar-label">Valutazioni</div>
                </div>
                <div class="ar-stat-value">{{ $totalGrades }}</div>
            </div>

            <div class="ar-card ar-stat">
                <div class="ar-stat-top">
                    <div class="ar-icon"><x-heroicon-o-book-open /></div>
                    <div class="ar-label">Materie</div>
                </div>
                <div class="ar-stat-value">{{ $subjectCount }}</div>
            </div>

            <div class="ar-card ar-stat">
                <div class="ar-stat-top">
                    <div class="ar-icon"><x-heroicon-o-trophy /></div>
                    <div class="ar-label">Voto massimo</div>
                </div>
                <div class="ar-stat-value green">
                    @if ($highestGrade !== null)
                        {{ number_format($highestGrade, 1, ',', '') }}
                    @else
                        —
                    @endif
                </div>
            </div>

        </div>


        {{-- SUBJECTS --}}
        @if ($subjects->isEmpty())

            <div class="ar-card ar-empty">
                Nessuna valutazione disponibile per questo studente.
            </div>

        @else

            <div class="ar-subjects">

                @foreach ($subjects as $subjectName => $subject)

                    @php $subjectTone = $tone($subject['average']); @endphp

                    <div class="ar-card">

                        <div class="ar-subject-head">

                            <div class="ar-subject-title">
                                <div class="ar-icon"><x-heroicon-o-book-open /></div>
                                <div>
                                    <h3 class="ar-subject-name">{{ $subjectName }}</h3>
                                    <div class="ar-subject-count">
                                        {{ $subject['count'] }}
                                        {{ $subject['count'] === 1 ? 'valutazione' : 'valutazioni' }}
                                    </div>
                                </div>
                            </div>

                            <div class="ar-avg">
                                <div class="ar-label">Media</div>
                                <div class="ar-avg-value {{ $subjectTone }}">
                                    {{ number_format($subject['average'], 1, ',', '') }}/10
                                </div>
                                <div class="ar-bar">
                                    <span class="{{ $subjectTone }}"
                                          style="width: {{ max(0, min(100, $subject['average'] * 10)) }}%"></span>
                                </div>
                            </div>

                        </div>

                        <div>
                            @foreach ($subject['grades'] as $grade)

                                <div class="ar-row">

                                    <div class="ar-row-content">

                                        <div class="ar-row-meta">
                                            <span class="ar-date">{{ $grade->grade_date?->format('d/m/Y') }}</span>

                                            @if ($grade->lesson)
                                                <span class="ar-badge">Lezione</span>
                                            @endif
                                        </div>

                                        @if ($grade->lesson?->topics)
                                            <div class="ar-topic">{{ $grade->lesson->topics }}</div>
                                        @endif

                                        @if ($grade->notes)
                                            <div class="ar-notes">
                                                <div class="ar-label">Note</div>
                                                <div class="ar-notes-text">{{ $grade->notes }}</div>
                                            </div>
                                        @endif

                                    </div>

                                    <div class="ar-grade {{ $tone((float) $grade->grade) }}">
                                        {{ number_format((float) $grade->grade, 1, ',', '') }}
                                    </div>

                                </div>

                            @endforeach
                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </div>

</x-filament-panels::page>