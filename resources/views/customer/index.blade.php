@extends('layouts.backend')
@section('title','Dashboard Customer')
@section('content')
<div class="row match-height">
	<div class="col-xl-4 col-md-6 col-12">
		<div class="card card-congratulation-medal">
			<div class="card-body">
				<h5>Welcome 🎉 {{Auth::user()->name}}!</h5>
				<p class="card-text font-small-2">Semoga harimu menyenangkan.</p> <br>
				{{date('l, d F Y')}}, {{date('H:i:s')}}
			</div>
		</div>
	</div>
	<!--/ Medal Card -->

	<div class="col-xl-8 col-md-6 col-12">
		<div class="card card-statistics">
			<div class="card-header">
				<h4 class="card-title">Statistics</h4>
				<div class="d-flex align-items-center">
					<p class="card-text font-small-2 mr-25 mb-0">Updated 1 month ago</p>
				</div>
			</div>
			<div class="card-body statistics-body">
				<div class="row">
					<div class="col-xl-4 col-sm-6 col-12 mb-2 mb-xl-0">
						<div class="media">
							<div class="avatar bg-light-primary mr-2">
								<div class="avatar-content">
									<i class="feather icon-check text-primary font-medium-5"></i>
								</div>
							</div>
							<div class="media-body my-auto">
								<h4 class="font-weight-bolder mb-0">{{$totalLaundry}} Total</h4>
								<p class="card-text font-small-1 mb-0">Jumlah Laundry</p>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-sm-6 col-12 mb-2 mb-xl-0">
						<div class="media">
							<div class="avatar bg-light-info mr-2">
								<div class="avatar-content">
									<i class="feather icon-box text-success font-medium-5"></i>
								</div>
							</div>
							<div class="media-body my-auto">
								<h4 class="font-weight-bolder mb-0">{{$totalLaundryKg}} Kg</h4>
								<p class="card-text font-small-1 mb-0">Jumlah Laundry</p>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-sm-6 col-12 mb-2 mb-sm-0">
						<div class="media">
							<div class="avatar bg-light-danger mr-2">
								<div class="avatar-content">
									<i class="feather icon-star text-danger font-medium-5"></i>
								</div>
							</div>
							<div class="media-body my-auto">
								<h4 class="font-weight-bolder mb-0"> {{Auth::user()->point}} </h4>
								<p class="card-text font-small-1 mb-0">Point</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-12 col-md-12 col-12">
		{{-- Table --}}
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">
					Data Transaksi Kamu
				</h4>
				<div class="table-responsive m-t-0">
					<table id="myTable" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>No Resi</th>
								<th>TGL Transaksi</th>
								<th>Metode Pembayaran</th>
								<th>Status Pembayaran</th>
								<th>Status Pesanan</th>
								<th>Jenis</th>
								<th>Berat</th>
								<th>Total</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($transaksi as $key => $transaksis)
							<tr>
								<td> {{$key+1}} </td>
								<td> {{$transaksis->invoice}} </td>
								<td> {{$transaksis->tgl_transaksi}} </td>
								<td>
									{{ $transaksis->payment_method }}
								</td>
								<td> <span
										class="font-weight-bold text-{{$transaksis->status_payment == 'Pending'? 'warning' : 'success'}}">{{$transaksis->status_payment}}</span>
								</td>
								<td>
									@if ($transaksis->status_order == 'Done')
									<span class="label text-primary">Selesai</span>
									@elseif($transaksis->status_order == 'Delivered')
									<span class="label text-success">Diambil</span>
									@elseif($transaksis->status_order == 'Process')
									<span class="label text-info">Diproses</span>
									@elseif($transaksis->status_order == 'Pending')
									<span class="label text-warning">Pending</span>
									@endif
								</td>
								<td> {{$transaksis->prices()->pluck('jenis')->implode(',')}} </td>
								<td> {{$transaksis->kg}} kg </td>
								<td> {{Rupiah::getRupiah($transaksis->harga_akhir)}} </td>
								<td>
									@if (in_array($transaksis->payment_code, ['BC','M2']) && $transaksis->payment_url)
									<a href="{{ $transaksis->payment_url }}" target="_blank"
										class="btn btn-sm btn-info">Bayar</a>
									@elseif(in_array($transaksis->payment_code, ['bank_bca','bank_mandiri',
									'bank_bri']))
									<a href="" data-toggle="modal" data-target="#infobank_{{ $transaksis->invoice }}"
										class="btn btn-sm btn-info">Bayar</a>
									<div class="modal fade text-left" id="infobank_{{ $transaksis->invoice }}"
										tabindex="-1" role="dialog" aria-labelledby="infobank" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
													<button type="button" class="close" data-dismiss="modal"
														aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<h5 class="text-center mb-2"><strong>Informasi Bank
															Transfer</strong></h5>
													<p>Untuk melakukan pembayaran melalui transfer bank, silakan gunakan
														informasi berikut:</p>
													<table class="table table-borderless">
														<tbody>
															<tr>
																<td><strong>Nama Bank:</strong></td>
																<td>{{
																	$transaksis->bank($transaksis->payment_method)?->nama_bank
																	}}</td>
															</tr>
															<tr>
																<td><strong>Nama Rekening:</strong></td>
																<td>{{
																	$transaksis->bank($transaksis->payment_method)?->nama_pemilik
																	}}</td>
															</tr>
															<tr>
																<td><strong>Nomor Rekening:</strong></td>
																<td>{{
																	$transaksis->bank($transaksis->payment_method)?->no_rekening
																	}}</td>
															</tr>
															<tr>
																<td><strong>Berita Acara:</strong></td>
																<td>[{{ $transaksis->customers->name }}] - [{{
																	$transaksis->invoice }}]</td>
															</tr>
														</tbody>
													</table>
													<p>Pastikan berita acara sesuai agar pembayaran cepat diproses.
														Untuk pertanyaan, hubungi tim dukungan kami.</p>
													<div class="d-flex flex-row align-items-center">
														<a href="https://api.whatsapp.com/send?phone=6281234567890"
															target="_blank" class="btn btn-outline-success mr-2">
															<i class="fa fa-whatsapp"></i>
														</a>
														<a href="tel:{{ $setting->no_telp }}"
															class="btn btn-outline-primary mr-2">
															<i class="fa fa-phone"></i>
														</a>
														<a href="{{ $setting->facebook }}" target="_blank"
															class="btn btn-outline-info mr-2">
															<i class="fa fa-facebook"></i>
														</a>
														<a href="mailto:{{ $setting->email }}"
															class="btn btn-outline-secondary">
															<i class="fa fa-envelope"></i>
														</a>
													</div>

												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary"
														data-dismiss="modal">Close</button>
												</div>
											</div>
										</div>
									</div>
									{{-- @include('customer.modal-transfer-bank') --}}
									@elseif(in_array($transaksis->payment_code, ['BC','M2']))
									<a href="" data-toggle="modal" data-invoice="{{ $transaksis->invoice }}"
										data-harga="{{ Rupiah::getRupiah($transaksis->harga_akhir) }}"
										data-target="#bayar" class="btn btn-sm btn-info">Bayar</a>
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@include('customer.modal')

@endsection
@section('scripts')
<script type="text/javascript">
	// DATATABLE
$(document).ready(function() {
    $('#myTable').DataTable();
    $(document).ready(function() {
        var table = $('#example').DataTable({
            "columnDefs": [{
                "visible": false,
                "targets": 2
            }],
            "order": [
                [2, 'asc']
            ],
            "displayLength": 25,
            "drawCallback": function(settings) {
                var api = this.api();
                var rows = api.rows({
                    page: 'current'
                }).nodes();
                var last = null;
                api.column(2, {
                    page: 'current'
                }).data().each(function(group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before('<tr class="group"><td colspan="5">' + group + '</td></tr>');
                        last = group;
                    }
                });
            }
        });
    });

	$('#bayar').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var invoice = button.data('invoice'); // Extract info from data-* attributes
        var harga = button.data('harga'); // Extract info from data-* attributes

        // Update the modal's input field
        var modal = $(this);
        modal.find('.modal-body #invoice').val(invoice);
        modal.find('.modal-body #harga').val(harga);
    });
});
</script>
@endsection