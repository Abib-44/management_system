<?php

namespace Tests\Unit\Financial;

use App\Models\DocumentArchiveLink;
use Tests\TestCase;

class DocumentArchiveLinkTest extends TestCase
{
    private function triggerSavingEvent(
        DocumentArchiveLink $link
    ): void {
        $dispatcher = DocumentArchiveLink::getEventDispatcher();

        $this->assertNotNull(
            $dispatcher,
            'Il Model Event Dispatcher di Laravel non è disponibile.'
        );

        $dispatcher->dispatch(
            'eloquent.saving: '.DocumentArchiveLink::class,
            $link
        );
    }

    public function test_member_id_sets_entity_type_to_member(): void
    {
        $link = new DocumentArchiveLink([
            'entity_type' => 'student',
            'member_id' => 10,
        ]);

        $this->triggerSavingEvent($link);

        $this->assertSame(
            'member',
            $link->entity_type
        );
    }

    public function test_student_id_sets_entity_type_to_student(): void
    {
        $link = new DocumentArchiveLink([
            'entity_type' => 'member',
            'student_id' => 20,
        ]);

        $this->triggerSavingEvent($link);

        $this->assertSame(
            'student',
            $link->entity_type
        );
    }

    public function test_activity_id_sets_entity_type_to_activity(): void
    {
        $link = new DocumentArchiveLink([
            'entity_type' => 'student',
            'activity_id' => 30,
        ]);

        $this->triggerSavingEvent($link);

        $this->assertSame(
            'activity',
            $link->entity_type
        );
    }

    public function test_transaction_id_sets_entity_type_to_transaction(): void
    {
        $link = new DocumentArchiveLink([
            'entity_type' => 'member',
            'transaction_id' => 40,
        ]);

        $this->triggerSavingEvent($link);

        $this->assertSame(
            'transaction',
            $link->entity_type
        );
    }

    public function test_none_keeps_entity_type_none_when_no_entity_id_exists(): void
    {
        $link = new DocumentArchiveLink([
            'entity_type' => 'none',
        ]);

        $this->triggerSavingEvent($link);

        $this->assertSame(
            'none',
            $link->entity_type
        );
    }
}
