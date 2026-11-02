<fieldset class="mb-3">
    <legend class="form-label mb-2">Metode pembayaran</legend>
    <div class="payment-methods">
        <label class="payment-option" for="metode_tunai">
            <input class="form-check-input" type="radio" name="metode" id="metode_tunai" value="tunai" @checked(old('metode', 'tunai') === 'tunai') required>
            <span class="payment-option-copy">
                <strong>Tunai</strong>
                <small>{{ $isKasir ? 'Pilih status pembayaran di bawah berdasarkan uang yang sudah diterima.' : 'Bayar di kasir. Pesanan masuk dapur setelah kasir mengonfirmasi pembayaran.' }}</small>
            </span>
        </label>
        <label class="payment-option" for="metode_qris">
            <input class="form-check-input" type="radio" name="metode" id="metode_qris" value="qris" @checked(old('metode', 'tunai') === 'qris') required>
            <span class="payment-option-copy">
                <strong>Cashless · QRIS</strong>
                <small>{{ $isKasir ? 'Pilih status lunas hanya setelah pembayaran QRIS diverifikasi.' : 'Scan QR di halaman pembayaran, lalu kasir mengonfirmasi uang masuk.' }}</small>
            </span>
        </label>
    </div>
</fieldset>
