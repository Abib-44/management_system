<?php

namespace App\Filament\Resources\MembershipFees\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\MembershipFees\MembershipFeeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMembershipFee extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = MembershipFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
