<?php

namespace App\Filament\Resources\ServiceRequests;

use App\Enums\ServiceRequestStatus;
use App\Filament\Resources\ServiceRequests\Pages\EditServiceRequest;
use App\Filament\Resources\ServiceRequests\Pages\ListServiceRequests;
use App\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use App\Models\ServiceRequest;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 1;

    public static function getRecordTitle(?Model $record): string
    {
        return $record ? "Request #{$record->id} · ".($record->user->company_name ?: $record->user->name) : 'Request';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ServiceRequest::where('status', ServiceRequestStatus::New)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->options(ServiceRequestStatus::class)
                    ->required(),
                Textarea::make('internal_notes')
                    ->label('Internal notes (not visible to client)')
                    ->rows(5)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.company_name')->label('Company')->placeholder('-'),
                        TextEntry::make('user.name')->label('Contact'),
                        TextEntry::make('user.job_title')->label('Job title')->placeholder('-'),
                        TextEntry::make('user.email')->label('Email')->copyable(),
                        TextEntry::make('user.phone')->label('Phone')->copyable()->placeholder('-'),
                        TextEntry::make('user.website')->label('Website')->url(fn ($state) => $state, true)->placeholder('-'),
                        TextEntry::make('location')
                            ->label('Address')
                            ->state(fn (ServiceRequest $record) => collect([$record->user->address, $record->user->city, $record->user->country])->filter()->implode(', '))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make('Request')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('services.name')->label('Services')->badge()->columnSpanFull(),
                        TextEntry::make('notes')->label('Client notes')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('created_at')->label('Submitted')->dateTime(),
                        TextEntry::make('internal_notes')->placeholder('-')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('user.company_name')->label('Company')->searchable()->sortable(),
                TextColumn::make('user.name')->label('Contact')->searchable(),
                TextColumn::make('user.email')->label('Email')->searchable()->toggleable(),
                TextColumn::make('user.phone')->label('Phone')->toggleable(),
                TextColumn::make('services.name')->label('Services')->badge()->limitList(3),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('created_at')->label('Submitted')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(ServiceRequestStatus::class),
                SelectFilter::make('services')->relationship('services', 'name')->multiple()->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceRequests::route('/'),
            'view' => ViewServiceRequest::route('/{record}'),
            'edit' => EditServiceRequest::route('/{record}/edit'),
        ];
    }
}
