@if(!in_array($p->status_pesanan, ['siap', 'disajikan', 'dibatalkan'], true))
    <form method="POST" action="{{ route('pesanan.batal', $p->id_pesanan) }}" class="d-flex gap-1"
        data-confirm="Pesanan yang dibatalkan tidak dapat diproses kembali."
        data-confirm-title="Batalkan pesanan?"
        data-confirm-button="Ya, batalkan">@csrf
        <input name="alasan" class="form-control form-control-sm" placeholder="Alasan batal (wajib)" required maxlength="255">
        <button class="btn btn-outline-danger btn-sm">Batalkan</button>
    </form>
@endif
