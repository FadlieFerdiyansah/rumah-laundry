@extends('layouts.backend')
@section('title','Karyawan - Invoice Customer')
@section('header','Invoice Customer')
@section('content')
<div class="col-md-12">
    <div class="card card-body printableArea">
        <h3><b>INVOICE</b> <span class="pull-right">{{$data->invoice}}</span></h3>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="pull-left">
                    <address>
                        <h3> &nbsp;<b class="text-danger">{{$data->user->nama_cabang}}</b></h3>
                        <p class="text-muted m-l-5"> Diterima Oleh <span style="margin-left:20px"> </span>: {{$data->user->name}}
                            <br/> Alamat <span style="margin-left:70px"> </span>: {{$data->user->alamat_cabang}},
                            <br/> No. Telp <span style="margin-left:65px"> </span>: {{$data->user->no_telp}}
                            </p>
                    </address>
                </div>
                <div class="pull-right text-right">
                    <address>
                        <h3>Detail Order Customer :</h3>
                        <p class="text-muted m-l-30">
                            {{$data->customers->name}}
                            <br/> {{$data->customers->alamat}}
                            <br/> {{$data->customers->no_telp}}</p>
                        <p class="m-t-30"><b>Tanggal Masuk :</b> <i class="fa fa-calendar"></i> {{carbon\carbon::parse($data->tgl_transaksi)->format('d-m-Y')}}</p>
                        <p><b>Tanggal Diambil :</b> <i class="fa fa-calendar"></i>
                            @if ($data->tgl_ambil == "")
                                Belum Diambil
                            @else
                            {{\carbon\carbon::parse($data->tgl_ambil)->format('d-m-Y')}}
                            @endif
                        </p>
                    </address>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive m-t-20" style="clear: both;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Jenis Pakaian</th>
                                <th class="text-right">Berat</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>{{$invoice->prices()->pluck('jenis')->implode(',')}}</td>
                                    <td class="text-right">{{$invoice->kg}} / kg</td>
                                    <td class="text-right">{{Rupiah::getRupiah($invoice->harga)}} /kg</td>
                                    <td class="text-right">
                                        <input type="hidden" value="{{$hitung = $invoice->kg * $invoice->harga}}">
                                        <p>{{Rupiah::getRupiah($hitung)}}</p>
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12">
                <div class="pull-left m-t-10">
                    <h6 style="font-weight:bold">Metode Pembayaran :</h6>
                    <ul>
                      @if ($invoice->payment_code == 'tunai')
                      <li style="color: white"> Tunai</li>
                      @elseif(in_array($invoice->payment_code, ['BC', 'M2']))
                      <li style="color: white"> {{ $invoice->payment_method }}</li>
                      @else
                        <li style="color: white"> {{$bank->nama_bank}} <br> {{$bank->no_rekening}} a/n {{$bank->nama_pemilik}}</li>
                      @endif
                    </ul>
                </div>
                <div class="pull-right m-t-10 text-right">
                    <p>Total : {{Rupiah::getRupiah($hitung)}}</p>
                    <p>Disc @if ($invoice->disc == "")
                        (0 %)
                    @else
                        ({{$invoice->disc}} %)
                    @endif :  </p>
                    <hr>
                    <h3><b>Total Bayar :</b> {{Rupiah::getRupiah($invoice->harga_akhir)}}</h3>
                </div>
                <div class="clearfix"></div>
                <hr>
                <div class="text-right">
                    <a href="{{url('pelayanan')}}" class="btn btn-outline btn-danger" style="color:white">Back</a>
                    <a href="{{url('cetak-invoice/'.$invoice->id. '/print')}}" target="_blank" class="btn btn-success"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection