<div class="modal fade text-left" id="bayar" tabindex="-1" role="dialog" aria-labelledby="bayar" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="bayar">Pembayaran </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('bayar')}}" method="POST">
              @csrf
              <div class="modal-body">
                <label class="mb-1">Invoice </label>
                <div class="form-group">
                    <input type="text" name="invoice" id="invoice" class="form-control" value="" readonly>
                </div>
                <label class="mb-1">Total Harga </label>
                <div class="form-group">
                    <input type="text" name="harga" id="harga" class="form-control" value="" readonly>
                </div>

                  <label for="Nama Bank" class="mb-1">Nama Bank/E-Wallet</label>
                  <div class="form-group">
                    <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                      <option disabled selected>==== Pilih Pembayaran ====</option>  
                      <option value="BC">BCA Virtual Account</option>
                      <option value="M2">Mandiri Virtual Account</option>
                      {{-- <option value="OL">OVO</option>
                      <option value="SL">ShopeePay</option>
                      <option value="DA">DANA</option> --}}
                      {{-- <option value="shopee">shopee</option> --}}
                      {{-- @foreach ($bank as $item)
                        <option value="{{$item->nama_bank}}"> {{$item->nama_bank}} </option>
                      @endforeach --}}
                    </select>
                    @error('payment_method')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
  
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
              </div>
            </form>
        </div>
    </div>
  </div>

