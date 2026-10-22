<form method="POST" action="{{ route('pesanan.batal', $p->id_pesanan) }}" class="d-flex gap-1">@csrf
    <input name="alasan" class="form-control form-control-sm" placeholder="Alasan batal (wajib)" required maxlength="255">
    <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Batalkan pesanan ini?')">Batalkan</button>
</form>
