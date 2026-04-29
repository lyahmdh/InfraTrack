<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Update Status Laporan</title>
</head>
<body>

    <h2>Status Laporan Diperbarui</h2>

    <p>Halo {{ $report->user->name }},</p>

    <p>Status laporan anda telah diperbarui.</p>

    <hr>

    <p>
        <strong>Judul Laporan:</strong><br>
        {{ $report->title }}
    </p>

    <p>
        <strong>Status Baru:</strong><br>
        {{ strtoupper($report->status) }}
    </p>

    <p>
        <strong>Lokasi:</strong><br>
        {{ $report->location_name }}
    </p>

    <hr>

    <p>
        Terima kasih telah menggunakan sistem pelaporan fasilitas publik.
    </p>

</body>
</html>