<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporateResource\Pages;
use App\Models\Corporate;
use Exception;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CorporateResource extends Resource
{
    protected static ?string $model = Corporate::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    /**
     * @throws Exception
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->helperText('Name of the corporate')
                    ->required(),
                TextInput::make('api_key')
                    ->label('API Key')
                    ->helperText('Specified API Key to the corporate')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'PENDING' => 'Pending',
                        'ACCEPTED' => 'Accepted',
                        'REJECTED' => 'Rejected',
                    ])
                    ->native(false),
                FileUpload::make('chat_bg')
                    ->label('Chat Background')
                    ->helperText('A background chosen for the chat background')
                    ->image()
                    ->imageEditor()
                    ->imageEditorMode(2)
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                        null
                    ])
                    ->disk('liara'),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->label("Corporate's logo")
                    ->image()
                    ->avatar()
                    ->imageEditor()
                    ->circleCropper()
                    ->disk('liara')
                    ->alignCenter()

            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name'),
                Tables\Columns\ImageColumn::make('chat_bg')
                    ->disk('liara')
                    ->label('Chat Background')
                    ->circular(),
                Tables\Columns\ImageColumn::make('logo')
                    ->disk('liara')
                    ->label('Logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PENDING' => 'gray',
                        'ACCEPTED' => 'success',
                        'REJECTED' => 'danger',
                    })
                    ->formatStateUsing(
                        fn(string $state): string => Str::lower($state)
                    )
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('chat')
                    ->label('Chat')
                    ->url(
                        function (Corporate $corporate) {
                            return "https://chatbots.irapardaz.ir/corporates/$corporate->slug";
                        }, shouldOpenInNewTab: true
                    )
                    ->icon('heroicon-c-chat-bubble-left-ellipsis')
                    ->disabled(fn(Corporate $corporate) => $corporate->status !== 'ACCEPTED')
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
            'index' => Pages\ListCorporates::route('/'),
        ];
    }
}
