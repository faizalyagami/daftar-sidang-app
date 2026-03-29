<div class="text-start">
    <table class="table table-borderless">
        <tr>
            <td width="35%"><strong>NPM</strong></td>
            <td>: {{ $mahasiswa->npm }}</td>
        </tr>
        <tr>
            <td><strong>Nama Lengkap</strong></td>
            <td>: {{ $mahasiswa->user->name }}</td>
        </tr>
        <tr>
            <td><strong>Email</strong></td>
            <td>: {{ $mahasiswa->user->email }}</td>
        </tr>
        <tr>
            <td><strong>Username</strong></td>
            <td>: {{ $mahasiswa->user->username }}</td>
        </tr>
        <tr>
            <td><strong>Tempat, Tanggal Lahir</strong></td>
            <td>: {{ $mahasiswa->tempat_lahir }}, {{ $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr>
            <td><strong>No Handphone</strong></td>
            <td>: {{ $mahasiswa->no_hp ?: '-' }}</td>
        </tr>
        <tr>
            <td><strong>IPK</strong></td>
            <td>: {{ $mahasiswa->ipk ? number_format($mahasiswa->ipk, 3) : '-' }}</td>
        </tr>
        <tr>
            <td><strong>Dosen Wali</strong></td>
            <td>: {{ $mahasiswa->dosen_wali ?: '-' }}</td>
        </tr>
    </table>
</div>