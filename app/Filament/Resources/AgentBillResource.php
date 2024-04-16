<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentBillResource\Pages;
use App\Filament\Resources\AgentBillResource\RelationManagers;
use App\Models\AgentBill;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgentBillResource extends Resource
{
    protected static ?string $modelLabel = '代理商账单';
    protected static ?string $model = AgentBill::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agent.name')->label('代理商'),
                Tables\Columns\TextColumn::make('amount')->money('CNY', divideBy: 100)->label('金额'),
                Tables\Columns\TextColumn::make('type')->label('类型')->badge()->color(fn (string $state): string => match ($state) {
                    'INCOME' => 'success',
                    'EXPENSE' => 'danger',
                }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('时间'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('类型')
                    ->options([
                        'INCOME' => 'INCOME',
                        'EXPENSE' => 'EXPENSE',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'DESC');
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
            'index' => Pages\ListAgentBills::route('/'),
            'view' => Pages\ViewAgentBill::route('/{record}'),
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
