<?php

namespace App\Filament\Resources\MembershipFees\Pages;

use App\Filament\Resources\MembershipFees\MembershipFeeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMembershipFee extends ViewRecord
{
    protected static string $resource = MembershipFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
