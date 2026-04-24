<?php

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title('Gagal menambah barang!')
            ->body('Minimal jumlah barang yang harus ditambahkan 1.')
            ->danger()
            ->send();
    }
}
