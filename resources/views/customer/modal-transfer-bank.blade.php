<div class="modal fade" id="infobank" tabindex="-1" role="dialog" aria-labelledby="infobank" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pembayaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="text-center mb-2"><strong>Informasi Bank Transfer</strong></h5>
                <p>Untuk melakukan pembayaran melalui transfer bank, silakan gunakan informasi berikut:</p>
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td><strong>Nama Bank:</strong></td>
                            <td id="bankName"></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Rekening:</strong></td>
                            <td id="accountName"></td>
                        </tr>
                        <tr>
                            <td><strong>Nomor Rekening:</strong></td>
                            <td id="accountNumber"></td>
                        </tr>
                        <tr>
                            <td><strong>Berita Acara:</strong></td>
                            <td>[{{ $transaksis->customers->name }}] - [<span id="invoice"></span>]</td>
                        </tr>
                    </tbody>
                </table>
                <p>Pastikan berita acara sesuai agar pembayaran cepat diproses. Untuk pertanyaan, hubungi tim dukungan kami.</p>
                <div class="d-flex flex-row align-items-center justify-content-center">
                    <a href="https://api.whatsapp.com/send?phone=6281234567890" target="_blank" class="btn btn-outline-success mr-2">
                        <i class="fa fa-whatsapp"></i>
                    </a>
                    <a href="tel:{{ $setting->no_telp }}" class="btn btn-outline-primary mr-2">
                        <i class="fa fa-phone"></i>
                    </a>
                    <a href="{{ $setting->facebook }}" target="_blank" class="btn btn-outline-info mr-2">
                        <i class="fa fa-facebook"></i>
                    </a>
                    <a href="mailto:{{ $setting->email }}" class="btn btn-outline-secondary">
                        <i class="fa fa-envelope"></i>
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</div>