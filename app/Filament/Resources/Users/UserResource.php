<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Clients & users';

    protected static ?string $recordTitleAttribute = 'company_name';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company & contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('company_name')->maxLength(255),
                        TextInput::make('name')->label('Contact name')->required()->maxLength(255),
                        TextInput::make('job_title')->maxLength(255),
                        TextInput::make('email')->email()->required()->unique(ignoreRecord: true)->maxLength(255),
                        TextInput::make('phone')->tel()->maxLength(50),
                        TextInput::make('website')->url()->maxLength(255),
                        TextInput::make('address')->maxLength(255)->columnSpanFull(),
                        TextInput::make('city')->maxLength(100),
                        TextInput::make('country')->maxLength(100),
                    ]),
                Section::make('Access')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->helperText(fn (string $operation) => $operation === 'edit' ? 'Leave blank to keep the current password.' : null),
                        Toggle::make('is_admin')
                            ->label('Admin / sales team access')
                            ->helperText('Can log in to this admin panel.')
                            // Prevent admins from locking themselves out.
                            ->disabled(fn (?Model $record) => $record?->is(auth()->user())),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company & contact')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('company_name')->placeholder('-'),
                        TextEntry::make('name')->label('Contact name'),
                        TextEntry::make('job_title')->placeholder('-'),
                        TextEntry::make('email')->copyable(),
                        TextEntry::make('phone')->copyable()->placeholder('-'),
                        TextEntry::make('website')->url(fn ($state) => $state, true)->placeholder('-'),
                        TextEntry::make('address')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('city')->placeholder('-'),
                        TextEntry::make('country')->placeholder('-'),
                    ]),
                Section::make('Account')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_admin')->label('Admin')->boolean(),
                        TextEntry::make('created_at')->label('Registered')->dateTime(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('company_name')->label('Company')->searchable()->sortable()->placeholder('-'),
                TextColumn::make('name')->label('Contact')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable()->toggleable(),
                TextColumn::make('country')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('service_requests_count')->counts('serviceRequests')->label('Requests')->sortable(),
                IconColumn::make('is_admin')->label('Admin')->boolean(),
                TextColumn::make('created_at')->label('Registered')->dateTime()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_admin')->label('Admin'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
