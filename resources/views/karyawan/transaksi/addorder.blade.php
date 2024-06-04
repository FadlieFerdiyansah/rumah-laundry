@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@extends('layouts.backend')
@section('title','Tambah Data Order')
@section('content')
    @if (@$cek_harga->user_id == !null || @$cek_harga->user_id == Auth::user()->id)

    @if($message = Session::get('error'))
      <div class="alert alert-danger alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
      </div>
    @endif

    <div class="card card-outline-info">
      <div class="card-header">
          <h4 class="card-title">Form Tambah Data Order
              {{-- <a href="{{url('customers-create')}}" class="btn btn-danger">+ Customer Baru</a> --}}
          </h4>
      </div>
      <div class="card-body">
        {{-- Cek Apakah Customer ada --}}
        @if ($cek_customer != 0)
          <form id="orderForm" action="{{route('pelayanan.store')}}" method="POST">
            @csrf
            <div class="form-body">
              <div class="row p-t-20">
                  <div class="col-md-3">
                      <div class="form-group has-success">
                          <label class="control-label">Nama Customer</label>
                          <select name="customer_id" id="customer_id" class="form-control select2 @error('customer_id') is-invalid @enderror" >
                              <option value="">-- Pilih Customer --</option>
                              @foreach ($customer as $customers)
                                  <option value="{{$customers->id}}" {{old('customer_id') == $customers->id ? 'selected' : ''}} >{{$customers->name}}</option>
                              @endforeach
                          </select>
                          @error('customer_id')
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                      </div>
                  </div>

                  <div class="col-md-3">
                      <div class="form-group has-success">
                          <label class="control-label">No Transaksi</label>
                          <input type="text" name="invoice" value="{{$newID}}" class="form-control @error('invoice') is-invalid @enderror" readonly>
                          @error('invoice')
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                      </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group has-success">
                        <label class="control-label">Berat Pakaian</label>
                        <input type="text" class="form-control form-control-danger @error('kg') is-invalid @enderror" value=" {{old('kg')}} " name="kg" placeholder="Berat Pakaian" autocomplete="off" >
                        @error('kg')
                          <span class="invalid-feedback text-danger" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group has-success">
                        <label class="control-label">Status Pembayaran</label>
                        <select class="form-control custom-select @error('status_payment') is-invalid @enderror" name="status_payment" >
                            <option value="">-- Pilih Status Payment --</option>
                            <option value="Pending" {{old('status_payment') == 'Pending' ? 'selected' : ''}} >Belum Dibayar</option>
                            <option value="Success" {{old('status_payment') == 'Success' ? 'selected' : ''}}>Sudah Dibayar</option>
                        </select>
                        @error('status_payment')
                          <span class="invalid-feedback text-danger" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                    </div>
                  </div>
              </div>

              <div class="row">

                <div class="col-md-3">
                  <div class="form-group has-success">
                      <label class="control-label">Jenis Pembayaran</label>
                      <select class="form-control custom-select @error('payment_method') is-invalid @enderror" name="payment_method" >
                        <option value="">-- Pilih Jenis Pembayaran --</option>
                        <option value="tunai" {{old('payment_method' == 'tunai' ? 'selected' : '')}} >Tunai</option>
                        <option value="bank_bca" {{old('payment_method' == 'bank_bca' ? 'selected' : '')}}>Transfer Bank BCA</option>
                        <option value="bank_mandiri" {{old('payment_method' == 'bank_mandiri' ? 'selected' : '')}}>Transfer Bank Mandiri</option>
                        <option value="bank_bri" {{old('payment_method' == 'bank_bri' ? 'selected' : '')}}>Transfer Bank BRI</option>
                        <option value="BC" {{old('payment_method' == 'BC' ? 'selected' : '')}}>BCA Virtual Account</option>
                        <option value="M2" {{old('payment_method' == 'M2' ? 'selected' : '')}}>Mandiri Virtual Account</option>
                      </select>
                      @error('payment_method')
                        <span class="invalid-feedback text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                  </div>
                </div>

                <div id="inputContainer">
                  <div class="col-md-12 mb-3">
                      <div class="form-group has-success">
                          <label class="control-label">Pilih Pakaian</label>
                          <div class="input-group">
                              <select name="harga_ids[]" class="form-control harga-select">
                                  <option value="">-- Jenis Pakaian --</option>
                                  @foreach($jenisPakaian as $jenis)
                                      <option value="{{$jenis->id}}" {{old('pakaian') == $jenis->id ? 'selected' : '' }}>{{$jenis->jenis}}</option>
                                  @endforeach
                              </select>
                              &nbsp;<button type="button" class="btn btn-primary btn-sm addButton">+</button>
                              &nbsp;<button type="button" class="btn btn-danger btn-sm removeButton">x</button>
                          </div>
                          {{-- @error('pakaian')
                              <span class="invalid-feedback text-danger" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror --}}
                      </div>
                  </div>
              </div>
              <div class="col-md-2">
                  <div class="form-group has-success">
                      <label class="control-label">Harga</label>
                      <input type="text" id="total-harga" class="form-control" name="total_harga" autocomplete="off">
                      {{-- @error('total_harga')
                          <span class="invalid-feedback text-danger" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror --}}
                  </div>
              </div>
              
                <div class="col-md-2">
                    <div class="form-group has-success">
                      <label for="id" class="control-label">Estimasi Hari</label>
                      <input type="number" id="hari" class="form-control" name="hari"/>
                    </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group has-success">
                      <label class="control-label">Disc</label>
                      <input type="number" name="disc" placeholder="Tulis Disc" class="form-control @error('disc') is-invalid @enderror">
                      @error('disc')
                        <span class="invalid-feedback text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                  </div>
                </div>
              </div>

                <input type="hidden" name="tgl">
                <!--/row-->
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary mr-1 mb-1">Tambah</button>
              <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset</button>
            </div>
          </form>
        @else
          <div class="col text-center">
            <h2 class="text-danger">
              Data Customer Masih Kosong !
            </h2>
          </div>
        @endif
      </div>
    </div>
    @else
      <div class="card">
        <div class="col text-center">
          <img src="{{asset('backend/images/pages/empty.svg')}}" style="height:500px; width:100%; margin-top:10px">
          <h2 class="mt-1">Data Harga Kosong / Tidak Aktif !</h2>
          <h4>Mohon hubungi Administrator :)</h4>
        </div>
      </div>
    @endif
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    var selectedIds = [];

    // Function to handle change event for all select elements
    $(document).on('change', '.harga-select', function() {
        updateSelectedIds();
        fetchAndCalculateTotalHarga();
    });

    // Function to add a new select input with options cloned from the first one
    function addNewSelect() {
        let inputContainer = document.getElementById('inputContainer');
        let originalElement = inputContainer.querySelector('.col-md-12');
        let newElement = originalElement.cloneNode(true);

        // Clear the selection in the new select element
        let selectElement = newElement.querySelector('select');
        selectElement.selectedIndex = -1;

        // Remove existing event listener to avoid multiple bindings
        let newAddButton = newElement.querySelector('button.addButton');
        newAddButton.removeEventListener('click', addNewSelect);

        // Append the new element to the container
        inputContainer.appendChild(newElement);

        // Update the add button event listener for the new element
        newAddButton.addEventListener('click', addNewSelect);

        // Add event listener for remove button
        let newRemoveButton = newElement.querySelector('button.removeButton');
        newRemoveButton.addEventListener('click', function() {
            newElement.remove();
            updateSelectedIds();
            fetchAndCalculateTotalHarga();
        });
    }

    // Function to update the selected IDs array
    function updateSelectedIds() {
        selectedIds = [];
        $('.harga-select').each(function() {
            let id = $(this).val();
            if (id) {
                selectedIds.push(id);
            }
        });
    }

    // Function to fetch harga and calculate total
    function fetchAndCalculateTotalHarga() {
        if (selectedIds.length > 0) {
            $.ajax({
                url: '{{ url("listharga") }}',
                method: 'GET',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'ids': selectedIds
                },
                success: function(resp) {
                    let totalHarga = resp.total_harga;
                    $('#total-harga').val(totalHarga);
                }
            });
        } else {
            $('#total-harga').val(0);
        }
    }

    // Initial add button event listener
    document.querySelector('button.addButton').addEventListener('click', addNewSelect);

    // Initial remove button event listener
    document.querySelector('button.removeButton').addEventListener('click', function() {
        let element = this.closest('.col-md-12');
        element.remove();
        updateSelectedIds();
        fetchAndCalculateTotalHarga();
    });
});





</script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $('.js-example-basic-multiple').select2({
      // theme: "classic"
    });
});
</script>
@endpush