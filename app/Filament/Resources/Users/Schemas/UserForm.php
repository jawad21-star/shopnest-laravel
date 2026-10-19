<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Account')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->tel(),
                        TextInput::make('city'),
                        TextInput::make('address')
                            ->columnSpanFull(),
                    ]),
                Section::make('Access')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->helperText(fn (string $operation) => $operation === 'edit' ? 'Leave blank to keep the current password.' : null),
                        Toggle::make('is_admin')
                            ->label('Admin access')
                            ->helperText('Can sign in to this admin panel.'),
                    ]),
            ]);
    }
}
