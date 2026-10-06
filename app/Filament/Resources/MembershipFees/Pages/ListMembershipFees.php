<?php

namespace App\Filament\Resources\MembershipFees\Pages;

use App\Filament\Resources\MembershipFees\MembershipFeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMembershipFees extends ListRecords
{
    protected static string $resource = MembershipFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
