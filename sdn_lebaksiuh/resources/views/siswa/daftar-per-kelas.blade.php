<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa per Kelas</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #444;
        }
        .class-group {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
        }
        .class-group h2 {
            margin-top: 0;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f8f8;
        }
        tr:nth-child(even) {
            background-color: #fdfdfd;
        }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            margin: 0 10px;
        }
        .btn-back {
            background-color: #6c757d;
        }
        .btn-print {
            background-color: #007bff;
        }
        @media print {
            body {
                background-color: #fff;
            }
            .container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .no-print {
                display: none;
            }
            .class-group {
                border: none;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Daftar Siswa per Kelas</h1>
        <p style="text-align: center; margin-top: -10px; margin-bottom: 30px;">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>

        <div class="no-print">
            <a href="{{ url('/') }}" class="btn btn-back">Kembali</a>
        </div>

        @forelse($siswaPerKelas as $kelas => $siswas)
            <div class="class-group">
                <h2>Kelas {{ $kelas }}</h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>Nama Lengkap</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswas as $index => $siswa)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $siswa->nama_lengkap }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <p style="text-align: center;">Tidak ada data siswa aktif yang bisa ditampilkan.</p>
        @endforelse
    </div>
</body>
</html>
