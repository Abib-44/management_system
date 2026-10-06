<?php

use App\Filament\Resources\FinancialTransactions\Schemas\FinancialTransactionForm;
use App\Models\FinancialTransaction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialTransactionFormTestComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function mount(): void
    {
        $this->form->fill([
            'documentLinks' => [[
                'entity_type' => 'member',
                'member_id' => null,
                'student_id' => null,
                'activity_id' => null,
                'document_archive_id' => null,
            ]],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return FinancialTransactionForm::configure(
            $schema->model(FinancialTransaction::class)
        );
    }

    public function render(): string
    {
        return '<div></div>';
    }
}

class FinancialTransactionFormTest extends TestCase
{
    public function test_form_can_be_configured(): void
    {
        $component = Livewire::test(FinancialTransactionFormTestComponent::class);

        $this->assertNotNull($component->instance()->form);
    }

    public function test_form_contains_financial_transaction_fields(): void
    {
        $component = Livewire::test(FinancialTransactionFormTestComponent::class);

        $fields = $component
            ->instance()
            ->form
            ->getFlatFields(withHidden: true);

        $fieldNames = array_keys($fields);

        $expectedFields = [
            'transaction_date',
            'type',
            'category_id',
            'description',
            'amount',
            'payment_method',
            'scope',
            'receipt_number',
        ];

        foreach ($expectedFields as $field) {
            $this->assertContains($field, $fieldNames);
        }
    }

    public function test_form_contains_document_links_fields(): void
    {
        $component = Livewire::test(FinancialTransactionFormTestComponent::class);

        $fields = $component
            ->instance()
            ->form
            ->getFlatFields(withHidden: true);

        $documentLinkFields = collect($fields)
            ->filter(
                fn ($field, string $name): bool => str_contains($name, 'documentLinks.')
            );

        $this->assertNotEmpty(
            $documentLinkFields,
            'The documentLinks repeater does not contain any fields.'
        );
    }

    public function test_form_contains_document_link_fields(): void
    {
        $component = Livewire::test(FinancialTransactionFormTestComponent::class);

        $fields = $component
            ->instance()
            ->form
            ->getFlatFields(withHidden: true);

        $fieldNames = array_keys($fields);

        foreach ([
            'entity_type',
            'member_id',
            'student_id',
            'activity_id',
            'document_archive_id',
        ] as $field) {
            $this->assertTrue(
                collect($fieldNames)->contains(
                    fn (string $name): bool => str_ends_with($name, '.'.$field)
                ),
                "Field {$field} not found."
            );
        }
    }

    public function test_member_field_is_visible_for_member(): void
    {
        $component = Livewire::test(FinancialTransactionFormTestComponent::class);

        $form = $component->instance()->form;

        $fields = $form->getFlatFields(withHidden: true);

        $memberField = collect($fields)
            ->first(
                fn ($field, string $name): bool => str_ends_with($name, '.member_id')
            );

        $studentField = collect($fields)
            ->first(
                fn ($field, string $name): bool => str_ends_with($name, '.student_id')
            );

        $activityField = collect($fields)
            ->first(
                fn ($field, string $name): bool => str_ends_with($name, '.activity_id')
            );

        $this->assertInstanceOf(Select::class, $memberField);
        $this->assertInstanceOf(Select::class, $studentField);
        $this->assertInstanceOf(Select::class, $activityField);

        $this->assertTrue($memberField->isVisible());
        $this->assertFalse($studentField->isVisible());
        $this->assertFalse($activityField->isVisible());
    }

    public function test_student_field_is_visible_for_student(): void
    {
        $component = Livewire::test(FinancialTransactionFormTestComponent::class);

        $component->instance()->form->fill([
            'documentLinks' => [[
                'entity_type' => 'student',
                'member_id' => null,
                'student_id' => null,
                'activity_id' => null,
                'document_archive_id' => null,
            ]],
        ]);

        $fields = $component
            ->instance()
            ->form
            ->getFlatFields(withHidden: true);

        $memberField = collect($fields)
            ->first(
                fn ($field, string $name): bool => str_ends_with($name, '.member_id')
            );

        $studentField = collect($fields)
            ->first(
                fn ($field, string $name): bool => str_ends_with($name, '.student_id')
            );

        $activityField = collect($fields)
            ->first(
                fn ($field, string $name): bool => str_ends_with($name, '.activity_id')
            );

        $this->assertFalse($memberField->isVisible());
        $this->assertTrue($studentField->isVisible());
        $this->assertFalse($activityField->isVisible());
    }

    public function test_activity_field_is_visible_for_activity(): void
    {
        $component = Livewire::test(FinancialTransactionFormTestComponent::class);

        $component->instance()->form->fill([
            'documentLinks' => [[
                'entity_type' => 'activity',
                'member_id' => null,
                'student_id' => null,
                'activity_id' => null,
                'document_archive_id' => null,
            ]],
        ]);

        $fields = $component
            ->instance()
            ->form
            ->getFlatFields(withHidden: true);

        $memberField = collect($fields)
            ->first(
                fn ($field, string $name): bool => str_ends_with($name, '.member_id')
            );

        $studentField = collect($fields)
            ->first(
                fn ($field, string $name): bool => str_ends_with($name, '.student_id')
            );

        $activityField = collect($fields)
            ->first(
                fn ($field, string $name): bool => str_ends_with($name, '.activity_id')
            );

        $this->assertFalse($memberField->isVisible());
        $this->assertFalse($studentField->isVisible());
        $this->assertTrue($activityField->isVisible());
    }

    public function test_document_archive_field_exists(): void
    {
        $component = Livewire::test(FinancialTransactionFormTestComponent::class);

        $fields = $component
            ->instance()
            ->form
            ->getFlatFields(withHidden: true);

        $documentField = collect($fields)
            ->first(
                fn ($field, string $name): bool => str_ends_with($name, '.document_archive_id')
            );

        $this->assertInstanceOf(Select::class, $documentField);
    }

    public function test_amount_field_exists_and_is_numeric(): void
    {
        $component = Livewire::test(FinancialTransactionFormTestComponent::class);

        $fields = $component
            ->instance()
            ->form
            ->getFlatFields(withHidden: true);

        $amountField = $fields['amount'] ?? null;

        $this->assertInstanceOf(TextInput::class, $amountField);
        $this->assertTrue($amountField->isNumeric());
    }
}
