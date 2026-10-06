<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public const NOTES = [
        'Buon lavoro.',
        'Buona comprensione dell’argomento.',
        'Sta migliorando.',
        'Risultato positivo.',
        'Ottima preparazione.',
        'Da approfondire alcuni argomenti.',
        'Necessita di maggiore esercizio.',
    ];

    public function definition(): array
    {
        // Non ci affidiamo a un eventuale record già presente nel DB:
        // se la tabella students è vuota ne creiamo uno, così
        // student_id/class_room_id non sono mai null.
        $student = Student::query()->inRandomOrder()->first()
            ?? Student::factory()->create();

        return $this->attributesFor($student);
    }

    /**
     * Stato per generare un voto legato a uno studente preciso
     * (usato dal GradeSeeder per evitare duplicazione di logica).
     */
    public function forStudent(Student $student): static
    {
        return $this->state(fn () => $this->attributesFor($student));
    }

    protected function attributesFor(Student $student): array
    {
        $lesson = Lesson::query()
            ->where('class_room_id', $student->class_room_id)
            ->inRandomOrder()
            ->first();

        // La materia del voto segue quella della lezione collegata, se
        // presente; altrimenti ne peschiamo una a caso tra quelle esistenti.
        $subjectId = $lesson?->subject_id
            ?? Subject::inRandomOrder()->value('id')
            ?? Subject::factory()->create()->id;

        return [
            'student_id' => $student->id,
            'class_room_id' => $student->class_room_id,
            'lesson_id' => $lesson?->id,
            'subject_id' => $subjectId,
            'grade_date' => $lesson?->lesson_date
                ?? fake()->dateTimeBetween('-3 months', 'now'),
            'grade' => fake()->randomFloat(1, 5, 10),
            'notes' => fake()->optional(0.35)->randomElement(self::NOTES),
        ];
    }
}
