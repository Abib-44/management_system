<?php

namespace App\Filament\Concerns;

trait RedirectsToIndexAfterEdit
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
