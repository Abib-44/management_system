<?php

namespace Tests\Feature\Financial;

use App\Filament\Resources\FinancialTransactions\Pages\CreateFinancialTransaction;
use App\Filament\Resources\FinancialTransactions\Pages\EditFinancialTransaction;
use App\Filament\Resources\FinancialTransactions\Pages\ListFinancialTransactions;
use App\Models\DocumentArchive;
use App\Models\DocumentCategory;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialTransactionTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $id = DB::table('users')->insertGetId([
            'name' => 'Test User',
            'email' => 'test-'.uniqid().'@example.com',
            'email_verified_at' => now(),
            'phone' => '0123456789',
            'active' => true,
            'password' => bcrypt('password'),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::query()->findOrFail($id);
    }

    private function baseFormData(int $categoryId): array
    {
        return [
            'transaction_date' => '2026-09-28',
            'type' => 'income',
            'category_id' => $categoryId,
            'description' => 'Test movimento finanziario',
            'amount' => '125.50',
            'payment_method' => 'bank_transfer',
            'scope' => 'all',
            'receipt_number' => 'TEST-001',
            'attachments' => [],
        ];
    }

    public function test_financial_transaction_list_page_can_be_opened(): void
    {
        $user = $this->user();

        $this->actingAs($user);

        Livewire::test(ListFinancialTransactions::class)
            ->assertSuccessful();
    }

    public function test_financial_transaction_can_be_created(): void
    {
        $user = $this->user();

        $category = FinancialCategory::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateFinancialTransaction::class)
            ->fillForm($this->baseFormData($category->id))
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('financial_transactions', [
            'transaction_date' => '2026-09-28',
            'type' => 'income',
            'category_id' => $category->id,
            'description' => 'Test movimento finanziario',
            'amount' => '125.50',
            'payment_method' => 'bank_transfer',
            'scope' => 'all',
            'receipt_number' => 'TEST-001',
        ]);
    }

    public function test_financial_transaction_creation_requires_required_fields(): void
    {
        $user = $this->user();

        $this->actingAs($user);

        Livewire::test(CreateFinancialTransaction::class)
            ->fillForm([
                'transaction_date' => null,
                'type' => null,
                'category_id' => null,
                'description' => null,
                'amount' => null,
                'payment_method' => null,
                'scope' => null,
                'receipt_number' => null,
                'attachments' => [],
            ])
            ->call('create')
            ->assertHasFormErrors([
                'transaction_date',
                'type',
                'category_id',
                'amount',
            ]);
    }

    public function test_financial_transaction_amount_must_be_numeric(): void
    {
        $user = $this->user();

        $category = FinancialCategory::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateFinancialTransaction::class)
            ->fillForm([
                'transaction_date' => '2026-09-28',
                'type' => 'income',
                'category_id' => $category->id,
                'description' => 'Test movimento',
                'amount' => 'abc',
                'payment_method' => 'cash',
                'scope' => 'all',
                'attachments' => [],
            ])
            ->call('create')
            ->assertHasFormErrors([
                'amount',
            ]);
    }

    public function test_created_transaction_can_be_loaded_for_editing(): void
    {
        $user = $this->user();

        $transaction = FinancialTransaction::factory()->create();

        $this->actingAs($user);

        Livewire::test(EditFinancialTransaction::class, [
            'record' => $transaction->getRouteKey(),
        ])
            ->assertSuccessful()
            ->assertFormSet([
                'description' => $transaction->description,
                'type' => $transaction->type,
                'category_id' => $transaction->category_id,
                'payment_method' => $transaction->payment_method,
                'scope' => $transaction->scope,
                'receipt_number' => $transaction->receipt_number,
            ]);
    }

    public function test_financial_transaction_can_be_updated(): void
    {
        $user = $this->user();

        $category = FinancialCategory::factory()->create();

        $transaction = FinancialTransaction::factory()->create([
            'category_id' => $category->id,
            'description' => 'Descrizione originale',
            'amount' => 100,
        ]);

        $this->actingAs($user);

        Livewire::test(EditFinancialTransaction::class, [
            'record' => $transaction->getRouteKey(),
        ])
            ->fillForm([
                'transaction_date' => '2026-09-28',
                'type' => 'expense',
                'category_id' => $category->id,
                'description' => 'Descrizione modificata',
                'amount' => '250.75',
                'payment_method' => 'card',
                'scope' => 'school',
                'receipt_number' => 'MOD-001',
                'attachments' => [],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('financial_transactions', [
            'id' => $transaction->id,
            'type' => 'expense',
            'category_id' => $category->id,
            'description' => 'Descrizione modificata',
            'amount' => '250.75',
            'payment_method' => 'card',
            'scope' => 'school',
            'receipt_number' => 'MOD-001',
        ]);
    }

    public function test_financial_transaction_can_be_deleted(): void
    {
        $user = $this->user();

        $transaction = FinancialTransaction::factory()->create();

        $this->actingAs($user);

        Livewire::test(EditFinancialTransaction::class, [
            'record' => $transaction->getRouteKey(),
        ])
            ->callAction('delete')
            ->assertSuccessful();

        $this->assertDatabaseMissing('financial_transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_transaction_created_by_is_saved(): void
    {
        $user = $this->user();

        $category = FinancialCategory::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateFinancialTransaction::class)
            ->fillForm([
                'transaction_date' => '2026-09-28',
                'type' => 'income',
                'category_id' => $category->id,
                'description' => 'Movimento con autore',
                'amount' => '75.00',
                'payment_method' => 'cash',
                'scope' => 'all',
                'attachments' => [],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $transaction = FinancialTransaction::query()
            ->where('description', 'Movimento con autore')
            ->firstOrFail();

        $this->assertSame($user->id, $transaction->created_by);
    }

    public function test_transaction_can_be_created_with_document_link(): void
    {
        $user = $this->user();

        $category = FinancialCategory::factory()->create();
        $documentCategory = DocumentCategory::factory()->create();

        $document = DocumentArchive::factory()->create([
            'document_category_id' => $documentCategory->id,
        ]);

        $this->actingAs($user);

        Livewire::test(CreateFinancialTransaction::class)
            ->fillForm([
                'transaction_date' => '2026-09-28',
                'type' => 'income',
                'category_id' => $category->id,
                'description' => 'Movimento con documento',
                'amount' => '300.00',
                'payment_method' => 'bank_transfer',
                'scope' => 'all',
                'receipt_number' => 'DOC-001',
                'attachments' => [],
                'documentLinks' => [[
                    'entity_type' => 'transaction',
                    'member_id' => null,
                    'student_id' => null,
                    'activity_id' => null,
                    'document_archive_id' => $document->id,
                ]],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $transaction = FinancialTransaction::query()
            ->where('description', 'Movimento con documento')
            ->firstOrFail();

        $this->assertDatabaseHas('document_archive_links', [
            'document_archive_id' => $document->id,
            'transaction_id' => $transaction->id,
            'entity_type' => 'transaction',
        ]);
    }

    public function test_financial_transaction_can_have_multiple_attachments(): void
    {
        Storage::fake('public');

        $user = $this->user();
        $category = FinancialCategory::factory()->create();

        $this->actingAs($user);

        $photo1 = UploadedFile::fake()->image('foto-1.jpg');
        $photo2 = UploadedFile::fake()->image('foto-2.jpg');
        $photo3 = UploadedFile::fake()->image('foto-3.jpg');

        Livewire::test(CreateFinancialTransaction::class)
            ->fillForm([
                'transaction_date' => '2026-09-28',
                'type' => 'income',
                'category_id' => $category->id,
                'description' => 'Movimento con più foto',
                'amount' => '150.00',
                'payment_method' => 'cash',
                'scope' => 'all',
                'attachments' => [
                    [
                        'file_path' => [
                            $photo1,
                            $photo2,
                            $photo3,
                        ],
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $transaction = FinancialTransaction::query()
            ->where('description', 'Movimento con più foto')
            ->firstOrFail();

        $this->assertSame(1, $transaction->attachments()->count());
    }
}
