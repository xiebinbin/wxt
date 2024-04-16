<?php

namespace App\Filament\Resources\AgentResource\Pages;

use App\Filament\Resources\AgentResource;
use App\Services\AgentService;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAgent extends ViewRecord
{
    protected static string $resource = AgentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')->label('姓名')->columnSpan(6),
                        TextEntry::make('phone')->label('电话')->columnSpan(6),
                        TextEntry::make('balance')->label('余额')->columnSpan(6)
                            ->money('CNY', divideBy: 100),
                        TextEntry::make('total_income')->label('总收入')->columnSpan(6)
                            ->money('CNY', divideBy: 100),
                        TextEntry::make('order_count')->label('订单总数')->columnSpan(6)
                            ->money('CNY', divideBy: 100),
                        TextEntry::make('valid_order_count')->label('有效订单数')->columnSpan(6)
                            ->money('CNY', divideBy: 100),
                        TextEntry::make('id')->label('推广地址')->columnSpan(12)->formatStateUsing(function (int $state): string {
                            $code = AgentService::idToCode($state);
                            return route('home', [
                                'code' => $code
                            ]);
                        }),
                        ImageEntry::make('qrcode')->label('二维码')->columnSpan(12)->width(200),
                    ])
                    ->columns(12)
                    ->columnSpan(6)
            ])->columns(12);
    }
}
