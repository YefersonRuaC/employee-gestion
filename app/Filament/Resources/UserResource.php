<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\State;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\City;
use Filament\Forms\Set;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Employees'; 
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Employee management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal info')
                    ->columns(3)
                    ->schema([   
                        Forms\Components\TextInput::make('name')
                        ->required(),

                        Forms\Components\TextInput::make('email')
                        ->email()
                        ->required(),

                        Forms\Components\TextInput::make('password')
                        ->password()
                        ->hiddenOn('edit')
                        ->required(),
                    ]),
                Section::make('Address info')
                    ->columns(3)
                    ->schema([   
                    //Using 'country_id' due to is the name of the field in our migration
                    //'2024_07_26_034724_add_address_fields_to_users_table'
                        Forms\Components\Select::make('country_id')
                        ->relationship(name : 'country', titleAttribute : 'name')
                        ->searchable()
                        ->preload()
                        ->live()//With live() we get access of the state of rhe page
                        //When the country is eliminated, quit state and city
                        ->afterStateUpdated(
                            function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            } 
                        )
                        ->required(),

                        Forms\Components\Select::make('state_id')
                        ->options(
                            fn (Get $get): Collection => State::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated( fn (Set $set) => $set('city_id', null) )
                        ->required(),

                        Forms\Components\Select::make('city_id')
                        ->options(fn (Get $get): Collection => City::query()
                            ->where('state_id', $get('state_id'))
                            ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                        Forms\Components\TextInput::make('address')
                        ->required(),

                        Forms\Components\TextInput::make('postal_code')
                        ->required(),

                        Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('postal_code')
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
