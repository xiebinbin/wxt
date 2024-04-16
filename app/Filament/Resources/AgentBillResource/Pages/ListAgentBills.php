<?php

namespace App\Filament\Resources\AgentBillResource\Pages;

use App\Filament\Resources\AgentBillResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgentBills extends ListRecords
{
    protected static string $resource = AgentBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
