<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

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
                        TextEntry::make('product.title')->label('套餐')->columnSpan(6),
                        TextEntry::make('name')->label('姓名')->columnSpan(6),
                        TextEntry::make('id_card')->label('身份证')->columnSpan(6),
                        TextEntry::make('phone')->label('手机号')->columnSpan(6),
                        TextEntry::make('created_at')->label('下单时间')->columnSpan(12),
                        TextEntry::make('address')->label('收货地址')->columnSpan(12),
                        TextEntry::make('logistics_company')->label('物流公司')->columnSpan(12)->visible(fn (Order $record) => $record->status=="PASSED"),
                        TextEntry::make('logistics_number')->label('物流单号')->columnSpan(12)->visible(fn (Order $record) => $record->status=="PASSED"),
                        TextEntry::make('status')->label('状态')->badge()->color(fn (string $state): string => match ($state) {
                            'PENDING' => 'warning',
                            'PASSED' => 'success',
                            'REJECTED' => 'danger',
                        })->columnSpan(12),
                        TextEntry::make('passed_at')->label('审核时间')->columnSpan(12)->visible(fn (Order $record) => $record->status=="PASSED"),
                        TextEntry::make('rejected_at')->label('拒绝时间')->columnSpan(12)->visible(fn (Order $record) => $record->status=="REJECTED"),
                        TextEntry::make('reject_reason')->label('拒绝原因')->columnSpan(6)->visible(fn (Order $record) => $record->status=="REJECTED"),
                        TextEntry::make('settlement_status')->label('结算状态')->badge()->color(fn (string $state): string => match ($state) {
                            'PENDING' => 'warning',
                            'SUCCESS' => 'success',
                            'FAILED' => 'danger',
                        })->columnSpan(12)->visible(fn (Order $record) => $record->settlement_status != "PENDING" && $record->status=="PASSED"),
                        TextEntry::make('settlement_failed_reason')->visible(fn (Order $record) => $record->settlement_status == "FAILED")->label('拒绝结算原因')->columnSpan(6),
                        TextEntry::make('settlement_amount')->visible(fn (Order $record) => $record->settlement_status == "SUCCESS")->label('结算佣金')->money('CNY', divideBy: 100)->columnSpan(6),
                        TextEntry::make('commission_amount')->visible(fn (Order $record) => $record->settlement_status == "SUCCESS")->label('分成佣金')->money('CNY', divideBy: 100)->columnSpan(6),
                    ])
                    ->columns(12)
                    ->columnSpan(6)
            ])->columns(12);
    }
}
