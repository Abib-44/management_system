<x-filament-panels::page>

    <style>
        .lesson-calendar {
            --calendar-bg: #ffffff;
            --calendar-border: #e5e7eb;
            --calendar-text: #111827;
            --calendar-muted: #6b7280;
            --calendar-soft: #f8fafc;
            --calendar-hover: #f1f5f9;
            --calendar-primary: #4f46e5;
            --calendar-primary-soft: #eef2ff;
            --calendar-primary-border: #c7d2fe;
            --calendar-today: #f5f7ff;
        }

        .dark .lesson-calendar {
            --calendar-bg: #18181b;
            --calendar-border: #3f3f46;
            --calendar-text: #f4f4f5;
            --calendar-muted: #a1a1aa;
            --calendar-soft: #27272a;
            --calendar-hover: #2f2f33;
            --calendar-primary: #818cf8;
            --calendar-primary-soft: rgba(99, 102, 241, 0.14);
            --calendar-primary-border: rgba(129, 140, 248, 0.35);
            --calendar-today: rgba(99, 102, 241, 0.08);
        }

        .lesson-calendar {
            width: 100%;
        }

        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 20px;
            padding: 22px 24px;
            border: 1px solid var(--calendar-border);
            border-radius: 16px;
            background: var(--calendar-bg);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .calendar-title {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .calendar-title-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            border-radius: 12px;
            background: var(--calendar-primary-soft);
            color: var(--calendar-primary);
        }

        .calendar-title-icon svg {
            width: 22px;
            height: 22px;
        }

        .calendar-title-content {
            min-width: 0;
        }

        .calendar-title-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .calendar-title h2 {
            margin: 0;
            color: var(--calendar-text);
            font-size: 20px;
            font-weight: 700;
            line-height: 1.3;
            text-transform: capitalize;
        }

        .calendar-total {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border-radius: 999px;
            background: var(--calendar-soft);
            color: var(--calendar-muted);
            font-size: 12px;
            font-weight: 600;
        }

        .calendar-subtitle {
            margin: 3px 0 0;
            color: var(--calendar-muted);
            font-size: 13px;
        }

        .calendar-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .calendar-filter {
            min-width: 190px;
        }

        .calendar-navigation {
            display: flex;
            align-items: center;
            gap: 2px;
            padding: 3px;
            border: 1px solid var(--calendar-border);
            border-radius: 10px;
            background: var(--calendar-soft);
        }

        .calendar-navigation button {
            min-width: 38px;
        }

        .calendar-subjects {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding: 14px 18px;
            border: 1px solid var(--calendar-border);
            border-radius: 14px;
            background: var(--calendar-bg);
        }

        .calendar-subjects-label {
            margin-right: 4px;
            color: var(--calendar-muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .calendar-subject {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 10px;
            border: 1px solid var(--calendar-border);
            border-radius: 999px;
            background: var(--calendar-soft);
            color: var(--calendar-text);
            font-size: 12px;
            font-weight: 500;
        }

        .calendar-subject-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--calendar-primary);
        }

        .calendar-subject-count {
            color: var(--calendar-muted);
            font-weight: 600;
        }

        .calendar-container {
            overflow-x: auto;
            border: 1px solid var(--calendar-border);
            border-radius: 16px;
            background: var(--calendar-bg);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .calendar-grid {
            min-width: 1000px;
        }

        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            border-bottom: 1px solid var(--calendar-border);
            background: var(--calendar-soft);
        }

        .calendar-weekday {
            padding: 12px 10px;
            border-right: 1px solid var(--calendar-border);
            color: var(--calendar-muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-align: center;
            text-transform: uppercase;
        }

        .calendar-weekday:last-child {
            border-right: 0;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }

        .calendar-day {
            position: relative;
            min-height: 175px;
            padding: 10px;
            border-right: 1px solid var(--calendar-border);
            border-bottom: 1px solid var(--calendar-border);
            background: var(--calendar-bg);
            transition: background 150ms ease;
        }

        .calendar-day:nth-child(7n) {
            border-right: 0;
        }

        .calendar-day:hover {
            background: var(--calendar-hover);
        }

        .calendar-day.is-outside {
            background: var(--calendar-soft);
        }

        .calendar-day.is-today {
            background: var(--calendar-today);
        }

        .calendar-day-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 28px;
            margin-bottom: 8px;
        }

        .calendar-day-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            color: var(--calendar-text);
            font-size: 13px;
            font-weight: 700;
        }

        .calendar-day.is-outside .calendar-day-number {
            color: #9ca3af;
        }

        .calendar-day.is-today .calendar-day-number {
            background: var(--calendar-primary);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
        }

        .calendar-day-count {
            color: var(--calendar-muted);
            font-size: 10px;
            font-weight: 600;
        }

        .calendar-lessons {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .calendar-lesson {
            position: relative;
            padding: 10px 11px;
            overflow: hidden;
            border: 1px solid var(--calendar-primary-border);
            border-radius: 9px;
            background: var(--calendar-primary-soft);
            cursor: pointer;
            outline: none;
            transition:
                transform 150ms ease,
                box-shadow 150ms ease,
                border-color 150ms ease,
                background 150ms ease;
        }

        .calendar-lesson:hover {
            transform: translateY(-1px);
            border-color: var(--calendar-primary);
            background: var(--calendar-primary-soft);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
        }

        .calendar-lesson:focus-visible {
            border-color: var(--calendar-primary);
            box-shadow:
                0 0 0 3px var(--calendar-primary-soft),
                0 4px 12px rgba(0, 0, 0, 0.07);
        }

        .calendar-lesson::before {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 3px;
            background: var(--calendar-primary);
            content: "";
        }

        .calendar-lesson-subject {
            margin-bottom: 5px;
            padding-left: 5px;
            overflow: hidden;
            color: var(--calendar-primary);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.4;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .calendar-lesson-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
            padding-left: 5px;
            overflow: hidden;
            color: var(--calendar-muted);
            font-size: 11px;
            line-height: 1.4;
        }

        .calendar-lesson-meta svg {
            width: 12px;
            height: 12px;
            flex: 0 0 12px;
        }

        .calendar-lesson-meta span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .calendar-lesson-topic {
            display: -webkit-box;
            margin-top: 7px;
            padding-left: 5px;
            overflow: hidden;
            color: var(--calendar-muted);
            font-size: 11px;
            line-height: 1.45;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .lesson-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, 0.58);
            backdrop-filter: blur(5px);
        }

        .lesson-modal {
            width: min(760px, 100%);
            max-height: calc(100vh - 48px);
            overflow: hidden;
            border: 1px solid var(--calendar-border);
            border-radius: 20px;
            background: var(--calendar-bg);
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.03);
        }

        .lesson-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            padding: 26px 28px;
            border-bottom: 1px solid var(--calendar-border);
        }

        .lesson-modal-title {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .lesson-modal-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            border-radius: 13px;
            background: var(--calendar-primary-soft);
            color: var(--calendar-primary);
        }

        .lesson-modal-icon svg {
            width: 23px;
            height: 23px;
        }

        .lesson-modal-title h3 {
            margin: 0;
            color: var(--calendar-text);
            font-size: 22px;
            font-weight: 700;
            line-height: 1.3;
        }

        .lesson-modal-title p {
            margin: 6px 0 0;
            color: var(--calendar-muted);
            font-size: 14px;
        }

        .lesson-modal-close {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            border: 0;
            border-radius: 9px;
            background: var(--calendar-soft);
            color: var(--calendar-muted);
            cursor: pointer;
            transition:
                background 150ms ease,
                color 150ms ease;
        }

        .lesson-modal-close:hover {
            background: var(--calendar-hover);
            color: var(--calendar-text);
        }

        .lesson-modal-close svg {
            width: 18px;
            height: 18px;
        }

        .lesson-modal-body {
            max-height: calc(100vh - 180px);
            overflow-y: auto;
            padding: 28px;
        }

        .lesson-modal-section {
            margin-bottom: 24px;
        }

        .lesson-modal-section:last-child {
            margin-bottom: 0;
        }

        .lesson-modal-section-title {
            margin-bottom: 12px;
            color: var(--calendar-muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .lesson-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .lesson-info-item {
            padding: 15px 16px;
            border: 1px solid var(--calendar-border);
            border-radius: 11px;
            background: var(--calendar-soft);
        }

        .lesson-info-label {
            margin-bottom: 5px;
            color: var(--calendar-muted);
            font-size: 12px;
            font-weight: 600;
        }

        .lesson-info-value {
            color: var(--calendar-text);
            font-size: 15px;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .lesson-topic-box {
            padding: 17px;
            border: 1px solid var(--calendar-border);
            border-radius: 12px;
            background: var(--calendar-soft);
            color: var(--calendar-text);
            font-size: 15px;
            line-height: 1.65;
            white-space: pre-wrap;
        }

        .lesson-raw-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 7px;
        }

        .lesson-raw-item {
            display: grid;
            grid-template-columns: 150px minmax(0, 1fr);
            gap: 12px;
            padding: 10px 12px;
            border-radius: 9px;
            background: var(--calendar-soft);
        }

        .lesson-raw-key {
            color: var(--calendar-muted);
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 11px;
            font-weight: 600;
        }

        .lesson-raw-value {
            color: var(--calendar-text);
            font-size: 12px;
            overflow-wrap: anywhere;
        }

        .lesson-modal-footer {
            display: flex;
            justify-content: flex-end;
            padding: 18px 28px;
            border-top: 1px solid var(--calendar-border);
        }

        [x-cloak] {
            display: none !important;
        }

        @media (max-width: 900px) {
            .calendar-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .calendar-toolbar {
                width: 100%;
            }

            .calendar-filter {
                flex: 1;
            }
        }

        @media (max-width: 640px) {
            .calendar-header {
                padding: 16px;
            }

            .calendar-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .calendar-filter {
                width: 100%;
            }

            .calendar-navigation {
                justify-content: center;
            }

            .calendar-navigation > * {
                flex: 1;
            }

            .calendar-subjects {
                padding: 12px;
            }

            .lesson-modal-backdrop {
                padding: 12px;
            }

            .lesson-modal-header,
            .lesson-modal-body {
                padding: 18px;
            }

            .lesson-info-grid {
                grid-template-columns: 1fr;
            }

            .lesson-raw-item {
                grid-template-columns: 1fr;
                gap: 4px;
            }
        }
    </style>

    <div
        class="lesson-calendar"
        x-data="{
            open: false,
            lesson: null,

            showLesson(data) {
                this.lesson = data
                this.open = true
                document.body.style.overflow = 'hidden'
            },

            closeLesson() {
                this.open = false
                this.lesson = null
                document.body.style.overflow = ''
            }
        }"
        x-on:keydown.escape.window="closeLesson()"
    >

        <div class="calendar-header">

            <div class="calendar-title">

                <div class="calendar-title-icon">
                    <x-heroicon-o-calendar-days />
                </div>

                <div class="calendar-title-content">

                    <div class="calendar-title-row">

                        <h2>
                            {{ $monthStart->locale('it')->translatedFormat('F Y') }}
                        </h2>

                        <span class="calendar-total">
                            {{ $totalLessons }} lezioni
                        </span>

                    </div>

                    <p class="calendar-subtitle">
                        Calendario delle lezioni
                    </p>

                </div>

            </div>

            <div class="calendar-toolbar">

                <div class="calendar-filter">

                    <x-filament::input.wrapper>

                        <x-filament::input.select wire:model.live="classRoomId">

                            <option value="">
                                Tutte le classi
                            </option>

                            @foreach ($classRooms as $id => $name)

                                <option value="{{ $id }}">
                                    {{ $name }}
                                </option>

                            @endforeach

                        </x-filament::input.select>

                    </x-filament::input.wrapper>

                </div>

                <div class="calendar-navigation">

                    <x-filament::button
                        color="gray"
                        wire:click="previousMonth"
                        icon="heroicon-m-chevron-left"
                        tooltip="Mese precedente"
                    />

                    <x-filament::button
                        color="gray"
                        wire:click="goToToday"
                    >
                        Oggi
                    </x-filament::button>

                    <x-filament::button
                        color="gray"
                        wire:click="nextMonth"
                        icon="heroicon-m-chevron-right"
                        icon-position="after"
                        tooltip="Mese successivo"
                    />

                </div>

            </div>

        </div>

        @if ($subjectSummary->isNotEmpty())

            <div class="calendar-subjects">

                <span class="calendar-subjects-label">
                    Materie
                </span>

                @foreach ($subjectSummary as $subjectName => $count)

                    <div class="calendar-subject">

                        <span class="calendar-subject-dot"></span>

                        <span>
                            {{ $subjectName }}
                        </span>

                        <span class="calendar-subject-count">
                            {{ $count }}
                        </span>

                    </div>

                @endforeach

            </div>

        @endif

        <div class="calendar-container">

            <div class="calendar-grid">

                <div class="calendar-weekdays">

                    @foreach (['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'] as $label)

                        <div class="calendar-weekday">
                            {{ $label }}
                        </div>

                    @endforeach

                </div>

                <div class="calendar-days">

                    @foreach ($days as $day)

                        @php
                            $dayLessons = $lessonsByDay->get(
                                $day->toDateString(),
                                collect()
                            );

                            $inMonth = $day->month === $monthStart->month;

                            $isToday = $day->isToday();
                        @endphp

                        <div @class([
                            'calendar-day',
                            'is-outside' => ! $inMonth,
                            'is-today' => $isToday,
                        ])>

                            <div class="calendar-day-header">

                                <div class="calendar-day-number">
                                    {{ $day->day }}
                                </div>

                                @if ($dayLessons->isNotEmpty())

                                    <span class="calendar-day-count">
                                        {{ $dayLessons->count() }}
                                    </span>

                                @endif

                            </div>

                            <div class="calendar-lessons">

                                @foreach ($dayLessons as $lesson)

                                    @php
                                        $lessonData = [
                                            'name' => $lesson->subject?->name,
                                            'date' => $day
                                                ->locale('it')
                                                ->translatedFormat('l d F Y'),
                                            'subject' => $lesson->subject?->name,
                                            'class' => $lesson->classRoom?->name,
                                            'teacher' => $lesson->teacher,
                                            'topics' => $lesson->topics,
                                            'attributes' => $lesson->getAttributes(),
                                        ];
                                    @endphp

                                    <div
                                        class="calendar-lesson"
                                        role="button"
                                        tabindex="0"
                                        title="Clicca per visualizzare i dettagli"
                                        x-on:click="showLesson({{ Js::from($lessonData) }})"
                                        x-on:keydown.enter="showLesson({{ Js::from($lessonData) }})"
                                        x-on:keydown.space.prevent="showLesson({{ Js::from($lessonData) }})"
                                    >

                                        <div class="calendar-lesson-subject">
                                            {{ $lesson->subject?->name ?? 'Materia non specificata' }}
                                        </div>

                                        @if ($showClass && $lesson->classRoom)

                                            <div class="calendar-lesson-meta">

                                                <x-heroicon-m-user-group />

                                                <span>
                                                    {{ $lesson->classRoom->name }}
                                                </span>

                                            </div>

                                        @endif

                                        @if ($lesson->teacher)

                                            <div class="calendar-lesson-meta">

                                                <x-heroicon-m-user />

                                                <span>
                                                    {{ $lesson->teacher }}
                                                </span>

                                            </div>

                                        @endif

                                        @if ($lesson->topics)

                                            <div class="calendar-lesson-topic">
                                                {{ $lesson->topics }}
                                            </div>

                                        @endif

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

        <div
            x-cloak
            x-show="open"
            x-transition.opacity
            class="lesson-modal-backdrop"
            x-on:click.self="closeLesson()"
        >

            <div
                class="lesson-modal"
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >

                <div class="lesson-modal-header">

                    <div class="lesson-modal-title">

                        <div class="lesson-modal-icon">
                            <x-heroicon-o-book-open />
                        </div>

                        <div>

                            <h3 x-text="lesson?.name || lesson?.subject || 'Lezione'"></h3>

                            <p x-text="lesson?.date || ''"></p>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="lesson-modal-close"
                        x-on:click="closeLesson()"
                        aria-label="Chiudi"
                    >
                        <x-heroicon-o-x-mark />
                    </button>

                </div>

                <div class="lesson-modal-body">

                    <div class="lesson-modal-section">

                        <div class="lesson-modal-section-title">
                            Informazioni principali
                        </div>

                        <div class="lesson-info-grid">

                            <div class="lesson-info-item">

                                <div class="lesson-info-label">
                                    Materia
                                </div>

                                <div
                                    class="lesson-info-value"
                                    x-text="lesson?.subject || 'Non specificata'"
                                ></div>

                            </div>

                            <div class="lesson-info-item">

                                <div class="lesson-info-label">
                                    Classe
                                </div>

                                <div
                                    class="lesson-info-value"
                                    x-text="lesson?.class || 'Non specificata'"
                                ></div>

                            </div>

                            <div class="lesson-info-item">

                                <div class="lesson-info-label">
                                    Docente
                                </div>

                                <div
                                    class="lesson-info-value"
                                    x-text="lesson?.teacher || 'Non specificato'"
                                ></div>

                            </div>

                            <div class="lesson-info-item">

                                <div class="lesson-info-label">
                                    Data
                                </div>

                                <div
                                    class="lesson-info-value"
                                    x-text="lesson?.date || '—'"
                                ></div>

                            </div>


                        </div>

                    </div>

                    <template x-if="lesson?.topics">

                        <div class="lesson-modal-section">

                            <div class="lesson-modal-section-title">
                                Argomento della lezione
                            </div>

                            <div
                                class="lesson-topic-box"
                                x-text="lesson.topics"
                            ></div>

                        </div>

                    </template>

                    <div class="lesson-modal-section">

                        <div class="lesson-modal-section-title">
                            Dettagli completi
                        </div>

                        <div class="lesson-raw-grid">

                            <template
                                x-for="(value, key) in (lesson?.attributes || {})"
                                :key="key"
                            >

                                <template x-if="key !== 'id'">

                                    <div class="lesson-raw-item">

                                        <div
                                            class="lesson-raw-key"
                                            x-text="key"
                                        ></div>

                                        <div
                                            class="lesson-raw-value"
                                            x-text="
                                                value === null || value === ''
                                                    ? '—'
                                                    : value
                                            "
                                        ></div>

                                    </div>

                                </template>

                            </template>

                        </div>

                    </div>

                </div>

                <div class="lesson-modal-footer">

                    <x-filament::button
                        color="gray"
                        x-on:click="closeLesson()"
                    >
                        Chiudi
                    </x-filament::button>

                </div>

            </div>

        </div>

    </div>

</x-filament-panels::page>