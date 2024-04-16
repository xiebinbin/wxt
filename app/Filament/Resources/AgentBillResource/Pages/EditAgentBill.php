<?php

namespace App\Filament\Resources\AgentBillResource\Pages;

use App\Filament\Resources\AgentBillResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgentBill extends EditRecord
{
    protected static string $resource = AgentBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
