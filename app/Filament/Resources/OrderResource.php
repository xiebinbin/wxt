<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Agent;
use App\Models\AgentBill;
use App\Models\Bill;
use App\Models\Order;
use App\Models\Product;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class OrderResource extends Resource
{
    protected static ?string $modelLabel = '订单';
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('agent_id')
                            ->label('代理商')
                            ->searchable()
                            ->required()
                            ->options(Agent::all()->pluck('name', 'id'))
                            ->columnSpan(6)
                            ->disabled(fn (?Order $record) => !empty($record)),
                        Forms\Components\Select::make('product_id')
                            ->label('商品')
                            ->searchable()
                            ->required()
                            ->options(Product::all()->pluck('title', 'id'))
                            ->columnSpan(6)
                            ->disabled(fn (?Order $record) => !empty($record)),
                        Forms\Components\TextInput::make('name')->label('姓名')
                            ->required()
                            ->maxLength(255)->columnSpan(4),
                        Forms\Components\TextInput::make('id_card')->label('身份证')
                            ->required()
                            ->maxLength(255)->columnSpan(4),
                        Forms\Components\TextInput::make('phone')->label('收货电话')
                            ->tel()
                            ->required()
                            ->maxLength(255)->columnSpan(4),
                        Forms\Components\TextInput::make('address')->label('收货地址')
                            ->required()
                            ->maxLength(255)->columnSpan(12),
                    ])
                    ->columns(12)
                    ->columnSpan(8)

            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.subtitle')->label('产品'),
                Tables\Columns\TextColumn::make('name')->label('姓名'),
                Tables\Columns\TextColumn::make('phone')->label('电话')->searchable(),
                Tables\Columns\TextColumn::make('status')->label('状态')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'PASSED' => 'success',
                        'REJECTED' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('settlement_status')->label('结算状态')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'SUCCESS' => 'success',
                        'FAILED' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('创建时间'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('passed')->form([
                    TextInput::make('logistics_company')->label('物流公司')->required(),
                    TextInput::make('logistics_number')->label('物流单号')->required(),
                ])
                    ->action(function (array $data, Order $record): void {
                        $record->logistics_company = $data['logistics_company'];
                        $record->logistics_number = $data['logistics_number'];
                        $record->status = 'PASSED';
                        $record->passed_at = now();
                        $record->save();
                        Product::where('id', $record->product_id)->increment('valid_order_count');
                    })->link()->label('审核发货')->visible(fn (Order $record) => $record->status == 'PENDING'),
                Action::make('reject')->form([
                    TextInput::make('reject_reason')->label('拒绝原因')->required(),
                ])->action(function (array $data, Order $record): void {
                    $record->reject_reason = $data['reject_reason'];
                    $record->status = 'REJECTED';
                    $record->save();
                })->link()->label('拒绝申请')->visible(fn (Order $record) => $record->status == 'PENDING'),
                Action::make('settlement_status')->form([
                    TextInput::make('settlement_amount')->label('结算佣金')->required(),
                    TextInput::make('commission_amount')->label('分成佣金')->required(),
                ])->visible(fn (Order $record) => $record->status == 'PASSED' && $record->settlement_status == 'PENDING')
                    ->action(function (array $data, Order $record): void {
                        try {
                            DB::beginTransaction();
                            $record->settlement_amount = $data['settlement_amount'] * 100;
                            $record->commission_amount = $data['commission_amount'] * 100;
                            $record->settlement_status = 'SUCCESS';
                            $record->save();
                            $agentBill = new AgentBill();
                            $agentBill->agent_id = $record->agent_id;
                            $agentBill->amount = $record->commission_amount;
                            $agentBill->remark = '分成佣金';
                            $agentBill->type = 'INCOME';
                            $agentBill->save();
                            $billOne = new Bill();
                            $billOne->amount = $record->settlement_amount;
                            $billOne->remark = '结算佣金';
                            $billOne->type = 'INCOME';
                            $billOne->save();
                            Agent::where('id', $record->agent_id)->update([
                                'balance' => DB::raw('balance + ' . $record->commission_amount),
                                'total_income' => DB::raw('total_income + ' . $record->commission_amount),
                            ]);
                            Product::where('id', $record->product_id)->update([
                                'settlement_commission_amount' => DB::raw('settlement_commission_amount + ' . $record->settlement_amount),
                                'settlement_order_count' => DB::raw('settlement_order_count + 1'),
                            ]);
                            DB::commit();
                        } catch (\Exception $e) {
                            DB::callback();
                            throw $e;
                        }
                    })->link()->label('佣金结算'),
                Action::make('settlement_failed_reason')->form([
                    TextInput::make('settlement_failed_reason')->label('拒绝原因')->required(),
                ])->visible(fn (Order $record) => $record->status == 'PASSED' && $record->settlement_status == 'PENDING')
                    ->action(function (array $data, Order $record): void {
                        $record->settlement_failed_reason = $data['settlement_failed_reason'];
                        $record->settlement_status = 'FAILED';
                        $record->save();
                    })->link()->label('拒绝结算')
            ])->defaultSort('created_at', 'DESC');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
