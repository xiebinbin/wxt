<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\ColorColumn;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('base')
                            ->label('基本信息')
                            ->schema([
                                TextEntry::make('title')->label('标题')->columnSpan(12),
                                TextEntry::make('subtitle')->label('产品')->columnSpan(12),
                                TextEntry::make('description')->label('描述')->columnSpan(12),
                                ImageEntry::make('list_cover')->label('列表封面图')->columnSpan(6),
                                ImageEntry::make('cover')->label('封面图')->columnSpan(6),
                                TextEntry::make('tags')->label('标签')->columnSpan(12),
                                TextEntry::make('badge')->label('角标')->columnSpan(6),
                                TextEntry::make('apply_count')->label('申请人数')->columnSpan(6),
                                TextEntry::make('expired_at')->label('下架时间')->columnSpan(6),
                                TextEntry::make('status')->label('开启状态')->columnSpan(6)->formatStateUsing(fn (int $state) =>$state == 1 ? 'ON': 'OFF'),
                                TextEntry::make('commission')->label('佣金')->money('CNY',divideBy:100)->columnSpan(6),
                            ])->columns(12)->icon('heroicon-m-bell')
                            ->iconPosition(IconPosition::After),
                        Tabs\Tab::make('order')
                            ->label('订单信息')
                            ->schema([
                                TextEntry::make('settlement_commission_amount')->label('结算佣金')->money('CNY',divideBy:100)->columnSpan(6),
                                TextEntry::make('order_count')->label('总订单')->money('CNY',divideBy:100)->columnSpan(6),
                                TextEntry::make('valid_order_count')->label('有效订单')->columnSpan(6),
                                TextEntry::make('settlement_order_count')->label('结算订单')->columnSpan(6),
                            ])->columns(12)->icon('heroicon-m-bell')
                            ->iconPosition(IconPosition::After),
                        Tabs\Tab::make('rent')
                            ->label('套餐信息')
                            ->schema([
                                TextEntry::make('traffic')->label('流量')->columnSpan(6),
                                TextEntry::make('traffic_description')->label('流量描述')->columnSpan(6),
                                TextEntry::make('monthly_rent')->label('月租')->columnSpan(6),
                                TextEntry::make('monthly_rent_description')->label('月租描述')->columnSpan(6),
                                TextEntry::make('call_description')->label('通话描述')->columnSpan(6),
                                TextEntry::make('discount_description')->label('优惠描述')->columnSpan(6),
                            ])->columns(12)->icon('heroicon-m-bell')
                            ->iconPosition(IconPosition::After),
                        Tabs\Tab::make('rent_introduction')
                            ->label('资费介绍')
                            ->schema([
                                TextEntry::make('rent_introduction')->label('')->columnSpanFull()->html(),
                            ])->columnSpanFull()->icon('heroicon-m-bell')
                            ->iconPosition(IconPosition::After),
                        Tabs\Tab::make('reminder')
                            ->label('温馨提示')
                            ->schema([
                                TextEntry::make('reminder')->label('')->columnSpanFull()->html(),
                            ])->columnSpanFull()->icon('heroicon-m-bell')
                            ->iconPosition(IconPosition::After),
                    ])->columnSpan(8)
            ])->columns(12);
    }
}
