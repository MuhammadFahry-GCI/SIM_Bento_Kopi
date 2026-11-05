<div class="table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Entitas</th><th>ID</th>@if($tampilkanRincianPesanan ?? false)<th>Nama pemesan</th><th>Pesanan</th>@endif<th>Keterangan</th></tr></thead>
        <tbody>
        @forelse($logs as $log)
            <tr>
                <td class="text-nowrap">{{ $log->waktu->format('d/m/Y H:i:s') }}</td>
                <td>{{ $log->pengguna->username ?? 'pelanggan' }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($log->aksi)) }}</td>
                <td>{{ $log->entitas }}</td>
                <td>{{ $log->id_entitas ?? '—' }}</td>
                @if($tampilkanRincianPesanan ?? false)
                    @php($pesanan = $pesananAktivitas->get($log->id_entitas))
                    <td>{{ $pesanan?->nama_pemesan_tampilan ?: 'Tidak dicantumkan' }}</td>
                    <td>
                        @if($pesanan)
                            @forelse($pesanan->detail as $detail)
                                {{ $detail->jumlah }}× {{ $detail->menu?->nama_menu ?? 'Menu dihapus' }}@if(!$loop->last), @endif
                            @empty
                                —
                            @endforelse
                        @else
                            —
                        @endif
                    </td>
                @endif
                <td>{{ $log->keterangan ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="{{ ($tampilkanRincianPesanan ?? false) ? 8 : 6 }}" class="text-center text-muted py-4">Belum ada aktivitas.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
