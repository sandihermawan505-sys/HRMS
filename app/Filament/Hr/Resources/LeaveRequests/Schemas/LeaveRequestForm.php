<?php

namespace App\Filament\Hr\Resources\LeaveRequests\Schemas;

// use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
// use Ramsey\Collection\Set;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('leave_type_id')
                    ->relationship('leaveType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('start_date')
                    ->live()
                    ->maxDate(fn (Get $get) => $get('end_date')) // Tidak bisa pilih tanggal setelah end_date
                    // ->beforeOrEqual('end_date') // Start date harus sebelum atau sama dengan end date
                    ->afterStateUpdated(fn($state, Set $set, Get $get) =>
                        self::calculateDays($set, $get))
                    ->required(),
                DatePicker::make('end_date')
                    ->live()
                    ->minDate(fn (Get $get) => $get('start_date')) // Tidak bisa pilih tanggal sebelum start_date
                    // ->afterOrEqual('start_date') // End date harus setelah atau sama dengan start date
                    ->afterStateUpdated(fn($state, Set $set, Get $get) =>
                        self::calculateDays($set, $get))
                    ->required(),
                TextInput::make('days')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->numeric(),
                Textarea::make('reason')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                    ->default('pending')
                    ->live()
                    ->required(),
                // TextInput::make('approved_by')
                //     ->numeric()
                //     ->default(null),
                DateTimePicker::make('approved_at'),
                Textarea::make('rejected_reason')
                    ->default(null)
                    ->columnSpanFull()
                    ->visible(fn(Get $get) => $get('status')=== 'rejected'),
            ]);
    }

    protected static function calculateDays(Set $set, Get $get) {
        $start = $get('start_date');
        $end = $get('end_date');

        if ($start && $end) {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);

             // Jika tanggal mulai melewati tanggal selesai
            // if ($startDate->greaterThan($endDate)) {
                
            //     // Bersihkan secara live kedua datepicker
            //     $set('start_date', null);
            //     $set('end_date', null);
            //     $set('days', null);

            //     return;
            // }

            $days = $startDate->diffInDays($endDate) + 1;
            $set('days' , $days);
        }
    }
}
