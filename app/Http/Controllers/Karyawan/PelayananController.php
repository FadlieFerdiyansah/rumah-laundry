<?php

namespace App\Http\Controllers\Karyawan;

use carbon\carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddOrderRequest;
use Illuminate\Support\Facades\Session;
use App\Models\{transaksi,User,harga,DataBank, Notification};
use App\Jobs\DoneCustomerJob;
use App\Jobs\OrderCustomerJob;
use App\Notifications\{OrderMasuk,OrderSelesai};

class PelayananController extends Controller

{

    // Halaman list order masuk
    public function index()
    {
      $order = transaksi::with('price')->where('user_id',Auth::user()->id)
      ->orderBy('id','DESC')->get();
      return view('karyawan.transaksi.order', compact('order'));
    }

    // Proses simpan order
    public function store(AddOrderRequest $request)
    {
      // info('store');
      // return request()->all();
      try {
        DB::beginTransaction();
        $order = new transaksi();
        $order->invoice         = $request->invoice;
        $order->tgl_transaksi   = Carbon::now()->parse($order->tgl_transaksi)->format('d-m-Y');
        $order->status_payment  = $request->status_payment;
        // $order->harga_id        = $request->harga_id;
        $order->customer_id     = $request->customer_id;
        $order->user_id         = Auth::user()->id;
        $order->customer        = namaCustomer($order->customer_id);
        $order->email_customer  = email_customer($order->customer_id);
        $order->hari            = $request->hari;
        $order->kg              = $request->kg;
        $order->harga           = (int) str_replace('.', '', $request->total_harga);
        $order->disc            = $request->disc;
        $hitung                 = $order->kg * $order->harga;
        if ($request->disc != NULL) {
            $disc                = ($hitung * $order->disc) / 100;
            $total               = $hitung - $disc;
            $order->harga_akhir  = $total;
        } else {
          $order->harga_akhir    = $hitung;
        }
        $order->payment_method  = $request->payment_method;
        $order->tgl               = Carbon::now()->day;
        $order->bulan             = Carbon::now()->month;
        $order->tahun             = Carbon::now()->year;
        $order->save();
        $order->prices()->sync(request('harga_ids'));

        if ($order) {
          // Notification Telegram
          if (setNotificationTelegramIn(1) == 1) {
            $order->notify(new OrderMasuk());
          }

          // Notification email
          if (setNotificationEmail(1) == 1) {
            // Menyiapkan data Email
            $bank = DataBank::get();
            $jenisPakaian = harga::where('id', $order->harga_id)->first();
            $data = array(
                'email'         => $order->email_customer,
                'invoice'       => $order->invoice,
                'customer'      => $order->customer,
                'tgl_transaksi' => $order->tgl_transaksi,
                'pakaian'       => $jenisPakaian->jenis,
                'berat'         => $order->kg,
                'harga'         => $order->harga,
                'harga_disc'    => ($hitung * $order->disc) / 100,
                'disc'          => $order->disc,
                'total'         => $order->kg * $order->harga,
                'harga_akhir'   => $order->harga_akhir,
                'laundry_name'  => Auth::user()->nama_cabang,
                'bank'          => $bank
            );

            // Kirim Email
            dispatch(new OrderCustomerJob($data));

          }
          DB::commit();
          Session::flash('success','Order Berhasil Ditambah !');
          return redirect('pelayanan');
        }
      } catch (ErrorException $e) {
        DB::rollback();
        throw new ErrorException($e->getMessage());
      }
    }

    // Tambah Order
    public function addorders()
    {
      $customer = User::where('karyawan_id',Auth::user()->id)->get();
      $jenisPakaian = harga::where('user_id',Auth::id())->where('status','1')->orderBy('jenis', 'asc')->get();
      $y = date('Y');
      $number = mt_rand(1000, 9999);
      // Nomor Form otomatis
      $newID = $number. Auth::user()->id .''.$y;
      $tgl = date('d-m-Y');

      $cek_harga = harga::where('user_id',Auth::user()->id)->where('status',1)->first();
      $cek_customer = User::select('id','karyawan_id')->where('karyawan_id',Auth::id())->count();
      return view('karyawan.transaksi.addorder', compact('customer','newID','cek_harga','cek_customer','jenisPakaian'));
    }

    public function listharga(Request $request)
    {
      info('list harga');

        // Validate the request to ensure 'id' is an array
        $request->validate([
            'ids' => 'required|array'
        ]);
    
        // Fetch harga records based on the user_id and the array of IDs
        $list_harga = harga::select('id', 'harga')
            ->where('user_id', Auth::user()->id)
            ->whereIn('id', $request->ids) // Use whereIn to handle multiple IDs
            ->get();

            info(json_encode($list_harga));
    
        // Initialize the total harga
        $total_harga = 0;
    
        // Loop through the fetched harga records to calculate the total harga
        foreach ($list_harga as $studi) {
            $total_harga += $studi->harga;
        }
    
        // Format the total harga
        $formatted_total_harga = number_format($total_harga, 0, ",", ".");
    
        // Return the formatted total harga
        return response()->json(['total_harga' => $formatted_total_harga]);
    }

    // Filter List Jumlah Hari
    public function listhari(Request $request)
    {
      $list_jenis = harga::select('id','hari')
        ->where('user_id',Auth::user()->id)
        ->where('id',$request->id)
        ->get();
        $select = '';
        $select .= '
                    <div class="form-group has-success">
                    <label for="id" class="control-label">Pilih Hari</label>
                    <select id="hari" class="form-control" name="hari" value="hari">
                    ';
                    foreach ($list_jenis as $hari) {
        $select .= '<option value="'.$hari->hari.'">'.$hari->hari.'</option>';
                    }'
                    </select>
                    </div>
                    </div>';
        return $select;
    }


    // Update Status Laundry
    public function updateStatusLaundry(Request $request)
    {
      $transaksi = transaksi::find($request->id);
      if ($transaksi->status_payment == 'Pending') {
        $transaksi->update([
          'status_payment' => 'Success'
        ]);
      } elseif ($transaksi->status_payment == 'Success') {
        if ($transaksi->status_order == 'Process') {
          $transaksi->update([
            'status_order' => 'Done'
          ]);

            // Tambah point +1
            $points = User::where('id',$transaksi->customer_id)->firstOrFail();
            $points->point =  $points->point + 1;
            $points->update();

            // Create Notifikasi
            $id         = $transaksi->id;
            $user_id    = $transaksi->customer_id;
            $title      = 'Pakaian Selesai';
            $body       = 'Pakaian Sudah Selesai dan Sudah Bisa Diambil :)';
            $kategori   = 'info';
            sendNotification($id,$user_id,$kategori,$title,$body);

            // Cek email notif
            if (setNotificationEmail(1) == 1) {

              // Menyiapkan data
              $data = array(
                  'email'           => $transaksi->email_customer,
                  'invoice'         => $transaksi->invoice,
                  'customer'        => $transaksi->customer,
                  'nama_laundry'    => Auth::user()->nama_cabang,
                  'alamat_laundry'  => Auth::user()->alamat_cabang,
              );

            // Kirim Email
            dispatch(new DoneCustomerJob($data));
            }

            // Cek status notif untuk telegram
            if (setNotificationTelegramFinish(1) == 1) {
              $transaksi->notify(new OrderSelesai());
            }

            // Notifikasi WhatsApp
            if (setNotificationWhatsappOrderSelesai(1) == 1 && getTokenWhatsapp() != null) {
              $waCustomer = $transaksi->customers->no_telp; // get nomor whatsapp customer
              $nameCustomer = $transaksi->customers->name; // get name customer
              notificationWhatsapp(
                getTokenWhatsapp(), // Token
                $waCustomer, // nomor whatsapp
                'Halo Kak '.$nameCustomer.' Laundry kamu sudah selesai dan sudah bisa diambil nih :) ' // pesan
              );
            }

        } elseif ($transaksi->status_order == 'Done') {
          $transaksi->update([
            'status_order' => 'Delivered'
          ]);
        }
      }

      if ($transaksi->status_payment == 'Success') {
          Session::flash('success', "Status Pembayaran Berhasil Diubah !");
      }
      if($transaksi->status_order == 'Done' || $transaksi->status_order == 'Delivered') {
          Session::flash('success', "Status Laundry Berhasil Diubah !");
      }
    }
}
