<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Models\Bill;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BillResource extends Resource
{
    protected static ?string $modelLabel = '系统账单';
    protected static ?string $model = Bill::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')->money('CNY', divideBy: 100)->label('金额'),
                Tables\Columns\TextColumn::make('type')->label('类型')->badge()->color(fn (string $state): string => match ($state) {
                    'INCOME' => 'success',
                    'EXPENSE' => 'danger',
                }),
                Tables\Columns\TextColumn::make('remark')->label('备注'),
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
            'index' => Pages\ListBills::route('/'),
            'view' => Pages\ViewBill::route('/{record}'),
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
