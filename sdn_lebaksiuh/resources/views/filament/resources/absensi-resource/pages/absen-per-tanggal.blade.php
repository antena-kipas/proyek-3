<x-filament-panels::page>
    <div>
        {{-- Form Tanggal --}}
        {{ $this->form }}
        
        {{-- 
           CATATAN TANGAN KANAN:
           Saya menghapus tombol "Tampilkan Absensi" di sini.
           Karena kita sudah pakai 'reactive()' di PHP, data akan 
           muncul otomatis saat tanggal dipilih. Lebih canggih!
        --}}
    </div>

    @if($siswas && $siswas->isNotEmpty())
        <form wire:submit.prevent="save" class="mt-6">
            
            {{-- Header Kelas & Tanggal --}}
            <div class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white mt-6">
                <h2>Kelas: {{ $kelas }}</h2>
                {{-- PERBAIKAN: Menggunakan $this->tanggalLogika --}}
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $this->tanggalLogika?->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>

            {{-- List Siswa --}}
            <div class="divide-y divide-gray-200 dark:divide-white/10 mt-4">
                @foreach($siswas as $index => $siswa)
                    <div class="flex items-center justify-between py-4">
                        {{-- Nama Siswa --}}
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-950 dark:text-white">
                                {{ $index + 1 }}. {{ $siswa->nama_lengkap }}
                            </span>
                        </div>
                        
                        {{-- 4 Tombol Status (H, I, S, A) --}}
                        <div class="flex gap-3">
                            @php
                                $currentStatus = $statuses[$siswa->id] ?? 'Hadir';
                            @endphp

                            {{-- Tombol HADIR (H) - Biru --}}
                            <button 
                                type="button"
                                wire:click="$set('statuses.{{ $siswa->id }}', 'Hadir')"
                                class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-transform hover:scale-110 shadow-sm"
                                style="{{ $currentStatus === 'Hadir' 
                                    ? 'background-color: #2563eb; color: white; border: 2px solid #2563eb;' 
                                    : 'background-color: #f3f4f6; color: #9ca3af; border: 1px solid #e5e7eb;' 
                                }}">
                                H
                            </button>

                            {{-- Tombol IZIN (I) - Kuning --}}
                            <button 
                                type="button"
                                wire:click="$set('statuses.{{ $siswa->id }}', 'Izin')"
                                class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-transform hover:scale-110 shadow-sm"
                                style="{{ $currentStatus === 'Izin' 
                                    ? 'background-color: #facc15; color: black; border: 2px solid #facc15;' 
                                    : 'background-color: #f3f4f6; color: #9ca3af; border: 1px solid #e5e7eb;' 
                                }}">
                                I
                            </button>

                            {{-- Tombol SAKIT (S) - Orange --}}
                            <button 
                                type="button"
                                wire:click="$set('statuses.{{ $siswa->id }}', 'Sakit')"
                                class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-transform hover:scale-110 shadow-sm"
                                style="{{ $currentStatus === 'Sakit' 
                                    ? 'background-color: #f97316; color: white; border: 2px solid #f97316;' 
                                    : 'background-color: #f3f4f6; color: #9ca3af; border: 1px solid #e5e7eb;' 
                                }}">
                                S
                            </button>

                            {{-- Tombol ALFA (A) - Merah --}}
                            <button 
                                type="button"
                                wire:click="$set('statuses.{{ $siswa->id }}', 'Alfa')"
                                class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-transform hover:scale-110 shadow-sm"
                                style="{{ $currentStatus === 'Alfa' 
                                    ? 'background-color: #dc2626; color: white; border: 2px solid #dc2626;' 
                                    : 'background-color: #f3f4f6; color: #9ca3af; border: 1px solid #e5e7eb;' 
                                }}">
                                A
                            </button>
                        </div>
                        
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" size="lg">
                    Simpan Absensi
                </x-filament::button>
            </div>
        </form>
    @endif
</x-filament-panels::page>