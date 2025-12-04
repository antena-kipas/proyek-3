<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Filament\Resources\AbsensiResource\RelationManagers;
use App\Models\Absensi;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Absensi';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Absensi';
    }

    public static function getModelLabel(): string
    {
        return 'Absensi';
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if ($user->role === 'super-user') {
            return true;
        }

        // Hanya tampilkan jika user adalah guru (wali kelas) yang punya kelas.
        // Guru mata pelajaran tidak punya `kelas`.
        if ($user->role === 'guru' && !is_null($user->kelas)) {
            return true;
        }

        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        if ($user->role === 'super-user') {
            return $query;
        }

        // Wali kelas hanya bisa melihat absensi kelasnya sendiri
        if ($user->role === 'guru' && !is_null($user->kelas)) {
            return $query->where('kelas_saat_ini', $user->kelas);
        }

        // Default, kembalikan query kosong jika tidak ada kondisi yang cocok
        // Ini untuk mencegah guru mapel (yang tidak seharusnya melihat resource ini)
        // secara tidak sengaja melihat data apapun.
        return $query->whereRaw('1 = 0');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->relationship('siswa', 'nama_lengkap')
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => 
                        $set('kelas_saat_ini', Siswa::find($state)?->kelas_sekarang)
                    ),
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\TextInput::make('kelas_saat_ini')
                    ->required()
                    ->numeric()
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Alfa' => 'Alfa',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas_saat_ini')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Alfa' => 'Alfa',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAbsensi::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
            'absen-hari-ini' => Pages\AbsenHariIni::route('/absen-hari-ini'),
            'absen-per-tanggal' => Pages\AbsenPerTanggal::route('/absen-per-tanggal'),
            'rekap-bulanan' => Pages\RekapAbsensiBulanan::route('/rekap-bulanan'),
        ];
    }
}
