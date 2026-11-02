<div class="table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Entitas</th><th>ID</th><th>Keterangan</th></tr></thead>
        <tbody>
        @forelse($logs as $log)
            <tr>
                <td class="text-nowrap">{{ $log->waktu->format('d/m/Y H:i:s') }}</td>
                <td>{{ $log->pengguna->username ?? 'pelanggan' }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($log->aksi)) }}</td>
                <td>{{ $log->entitas }}</td>
                <td>{{ $log->id_entitas ?? '—' }}</td>
                <td>{{ $log->keterangan ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada aktivitas.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
