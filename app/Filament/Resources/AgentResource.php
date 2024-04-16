<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentResource\Pages;
use App\Filament\Resources\AgentResource\RelationManagers;
use App\Models\Agent;
use App\Models\AgentBill;
use App\Models\Bill;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class AgentResource extends Resource
{
    protected static ?string $modelLabel = '代理商';
    protected static ?string $model = Agent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('姓名')
                            ->maxLength(255)
                            ->default(null)
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('phone')
                            ->label('电话')
                            ->maxLength(255)
                            ->default(null)
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\FileUpload::make('qrcode')
                            ->label('客服二维码')
                            ->directory('agent/qrcodes')
                            ->required()
                            ->image()
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('remark')
                            ->label('备注')
                            ->maxLength(255)
                            ->default(null)
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->columnSpan(6),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('电话')
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('余额')
                    ->money('CNY', divideBy: 100),
                Tables\Columns\TextColumn::make('order_count')
                    ->label('订单数'),
                Tables\Columns\TextColumn::make('valid_order_count')
                    ->label('有效订单数'),
                Tables\Columns\TextColumn::make('total_income')
                    ->label('总收入')
                    ->money('CNY', divideBy: 100),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
                Action::make('withdraw')->form([
                    TextInput::make('name')->label('姓名')->default(fn (Agent $agent) => $agent->name)->disabled(true),
                    TextInput::make('balance')->label('余额')->default(fn (Agent $agent) => Number::currency($agent->balance / 100, 'CNY'))->disabled(true),
                    TextInput::make('amount')->numeric()->label('提现金额')->required(),
                ])->action(function (array $data, Agent $record): void {
                    $money = intval($data['amount']) * 100;
                    if ($money > $record->balance) {
                        Notification::make()
                            ->title('余额不足!')
                            ->danger()
                            ->send();
                        return;
                    }
                    DB::beginTransaction();
                    try {
                        $result = Agent::query()
                            ->where('id', $record->id)
                            ->where('balance', '>=', $money)
                            ->decrement('balance', $money);
                        if (!$result) {
                            throw new \Exception('余额不足');
                        }
                        $bill = new AgentBill();
                        $bill->agent_id = $record->id;
                        $bill->amount = $money;
                        $bill->remark = '代理商提现';
                        $bill->type = 'EXPENSE';
                        $bill->save();
                        $billOne = new Bill();
                        $billOne->amount = $bill->amount;
                        $billOne->remark = '代理商提现';
                        $billOne->type = 'EXPENSE';
                        $billOne->save();
                        DB::commit();
                        Notification::make()
                            ->title('提现成功!')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Notification::make()
                            ->title($e->getMessage())
                            ->danger()
                            ->send();
                        return;
                    }
                })->link()->label('提现')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgents::route('/'),
            'create' => Pages\CreateAgent::route('/create'),
            'view' => Pages\ViewAgent::route('/{record}'),
            'edit' => Pages\EditAgent::route('/{record}/edit'),
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
