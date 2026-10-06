<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            ViewEntry::make('student_profile')
                ->view('filament.students.student-profile')
                ->columnSpanFull(),
        ]);
    }
}
