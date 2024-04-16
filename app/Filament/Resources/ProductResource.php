<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Services\ProductService;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $modelLabel = '产品';
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('base')
                            ->label('基本信息')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('标题')
                                    ->maxLength(255)
                                    ->default(null)
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('subtitle')
                                    ->label('产品名')
                                    ->maxLength(255)
                                    ->default(null)
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')->label('描述')->columnSpanFull(),
                                Forms\Components\FileUpload::make('list_cover')->directory('product/list_cover/' . now()->format('Y/m/d'))->image()->label('列表封面图')->required()->columnSpan(6),
                                Forms\Components\FileUpload::make('cover')->directory('product/cover/' . now()->format('Y/m/d'))->image()->label('封面图')->required()->columnSpan(6),
                                Forms\Components\CheckboxList::make('tags')->label('标签')
                                    ->required()
                                    ->columnSpan(6)
                                    ->options(array_combine(ProductService::$TAGS,ProductService::$TAGS))->columns(3)->gridDirection('row'),
                                Forms\Components\TextInput::make('badge')->label('角标')
                                    ->maxLength(255)
                                    ->default(null)
                                    ->columnSpan(6),
                                Forms\Components\TextInput::make('apply_count')->label('申请人数')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(6),
                                Forms\Components\DateTimePicker::make('expired_at')->label('下架时间')
                                    ->default(null)
                                    ->columnSpan(6),
                                Forms\Components\TextInput::make('commission')->label('佣金')
                                    ->default(0)
                                    ->numeric()->columnSpan(6)
                                    ->formatStateUsing(fn (int $state) => $state / 100)
                            ])->columns(12)->icon('heroicon-m-bell')
                            ->iconPosition(IconPosition::After),
                        Tabs\Tab::make('rent')
                            ->label('套餐信息')
                            ->schema([
                                Forms\Components\TextInput::make('traffic')->label('流量')
                                    ->maxLength(255)
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->columnSpan(6),
                                Forms\Components\TextInput::make('traffic_description')->label('流量描述')
                                    ->maxLength(255)
                                    ->default(null)
                                    ->columnSpan(6),
                                Forms\Components\TextInput::make('monthly_rent')->label('月租')
                                    ->maxLength(255)
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->columnSpan(6),
                                Forms\Components\TextInput::make('monthly_rent_description')->label('月租描述')
                                    ->maxLength(255)
                                    ->default(null)
                                    ->columnSpan(6),
                                Forms\Components\TextInput::make('call_description')->label('通话描述')
                                    ->maxLength(255)
                                    ->default(null)
                                    ->columnSpan(6),
                                Forms\Components\TextInput::make('discount_description')->label('优惠描述')
                                    ->maxLength(255)
                                    ->default(null)
                                    ->columnSpan(6),
                            ])->columns(12)->icon('heroicon-m-bell')
                            ->iconPosition(IconPosition::After),
                        Tabs\Tab::make('rent_introduction')
                            ->label('资费介绍')
                            ->schema([
                                TiptapEditor::make('rent_introduction')->label('')->columnSpanFull()->output(TiptapOutput::Html),
                            ])->columnSpanFull()->icon('heroicon-m-bell')
                            ->iconPosition(IconPosition::After),
                        Tabs\Tab::make('reminder')
                            ->label('温馨提示')
                            ->schema([
                                TiptapEditor::make('reminder')->label('')->columnSpanFull()->output(TiptapOutput::Html),
                            ])->columnSpanFull()->icon('heroicon-m-bell')
                            ->iconPosition(IconPosition::After),
                    ])->columnSpan(8)
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subtitle')->limit(50)->label('产品')->searchable(),
                Tables\Columns\TextColumn::make('commission')->money('CNY', divideBy: 100)->label('佣金')->searchable(),
                Tables\Columns\ToggleColumn::make('status')->label('开启状态'),
                Tables\Columns\TextColumn::make('expired_at')->dateTime()->label('下架时间'),
                Tables\Columns\TextColumn::make('order_count')->label('总订单'),
                Tables\Columns\TextColumn::make('valid_order_count')->label('有效订单'),
                Tables\Columns\TextColumn::make('settlement_order_count')->label('结算订单'),
                Tables\Columns\TextColumn::make('settlement_commission_amount')->money('CNY', divideBy: 100)->label('已结算佣金'),
                Tables\Columns\TextColumn::make('updated_at')->label('更新时间')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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
