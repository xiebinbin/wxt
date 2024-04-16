<?php

namespace App\Filament\Resources\AgentBillResource\Pages;

use App\Filament\Resources\AgentBillResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAgentBill extends ViewRecord
{
    protected static string $resource = AgentBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('agent.name')->label('代理商')->columnSpan(6),
                        TextEntry::make('amount')->label('金额')->columnSpan(6)->money('CNY', divideBy: 100),
                        TextEntry::make('type')->label('类型')->badge()->color(fn (string $state): string => match ($state) {
                            'INCOME' => 'success',
                            'EXPENSE' => 'danger',
                        })->columnSpan(6),
                        TextEntry::make('remark')->label('备注')->columnSpan(6),
                        TextEntry::make('created_at')->label('时间')->columnSpan(6)
                    ])
                    ->columns(12)
                    ->columnSpan(6)
            ])->columns(12);
    }
}
