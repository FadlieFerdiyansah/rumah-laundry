<?php

namespace App\Http\Controllers\Customer;

use Exception;
use App\Models\transaksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PembayaranController extends Controller
{
	//
	public function bayar()
	{
		$duitkuConfig = new \Duitku\Config("730d0a8bbcf882b1409d8b8b8ae92b0f", "DS19222");
		// false for production mode
		// true for sandbox mode
		$duitkuConfig->setSandboxMode(true);
		// set sanitizer (default : true)
		$duitkuConfig->setSanitizedMode(false);
		// set log parameter (default : true)
		$duitkuConfig->setDuitkuLogs(false);

		$transaksi = transaksi::where('invoice', request('invoice'))->first();
		$paymentMethod      = request('payment_method');
		$paymentAmount      = $transaksi->harga_akhir; // Amount
		$email              = $transaksi->email_customer; // your customer email
		$phoneNumber        = $transaksi->customers->no_telp; // your customer phone number (optional)
		$productDetails     = $transaksi->prices()->pluck('jenis')->implode(',');
		$merchantOrderId    = $transaksi->invoice; // from merchant, unique   
		$additionalParam    = ''; // optional
		$merchantUserInfo   = ''; // optional
		$customerVaName     = $transaksi->customers->name; // display name on bank confirmation display
		$callbackUrl        = 'https://harum.myhost.id/callback'; // url for callback
		$returnUrl          = 'https://harum.myhost.id/home'; // url for redirect
		$expiryPeriod       = 1440; // set the expired time in minutes

		// Customer Detail
		$firstName          = $transaksi->customers->name;
		// $lastName           = "Doe";

		// Address
		$alamat             = $transaksi->customers->alamat;
		$city               = "Jakarta";
		$postalCode         = "11530";
		$countryCode        = "ID";

		$address = array(
			'firstName'     => $firstName,
			// 'lastName'      => $lastName,
			'address'       => $alamat,
			'city'          => $city,
			'postalCode'    => $postalCode,
			'phone'         => $phoneNumber,
			'countryCode'   => $countryCode
		);

		$customerDetail = array(
			'firstName'         => $firstName,
			// 'lastName'          => $lastName,
			'email'             => $email,
			'phoneNumber'       => $phoneNumber,
			'billingAddress'    => $address,
			'shippingAddress'   => $address
		);

		// Item Details
		$item1 = array(
			'name'      => $productDetails,
			'price'     => $paymentAmount,
			'quantity'  => 1
		);

		$itemDetails = array(
			$item1
		);

		$params = array(
			'paymentMethod'     => $paymentMethod,
			'paymentAmount'     => $paymentAmount,
			'merchantOrderId'   => $merchantOrderId,
			'productDetails'    => $productDetails,
			'additionalParam'   => $additionalParam,
			'merchantUserInfo'  => $merchantUserInfo,
			'customerVaName'    => $customerVaName,
			'email'             => $email,
			'phoneNumber'       => $phoneNumber,
			'itemDetails'       => $itemDetails,
			'customerDetail'    => $customerDetail,
			'callbackUrl'       => $callbackUrl,
			'returnUrl'         => $returnUrl,
			'expiryPeriod'      => $expiryPeriod
		);

		try {
			// createInvoice Request
			$responseDuitkuPop = \Duitku\Pop::createInvoice($params, $duitkuConfig);
			$decode = json_decode($responseDuitkuPop);
			header('Content-Type: application/json');
			$transaksi->update(['payment_url' => $decode->paymentUrl, 'payment_method' => $paymentMethod]);
			return redirect($transaksi->payment_url);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function callback()
	{
		$apiKey = '730d0a8bbcf882b1409d8b8b8ae92b0f'; // API key anda
		$merchantCode = isset($_POST['merchantCode']) ? $_POST['merchantCode'] : null; 
		$amount = isset($_POST['amount']) ? $_POST['amount'] : null; 
		$merchantOrderId = isset($_POST['merchantOrderId']) ? $_POST['merchantOrderId'] : null; 
		$productDetail = isset($_POST['productDetail']) ? $_POST['productDetail'] : null; 
		$additionalParam = isset($_POST['additionalParam']) ? $_POST['additionalParam'] : null; 
		$paymentMethod = isset($_POST['paymentCode']) ? $_POST['paymentCode'] : null; 
		$resultCode = isset($_POST['resultCode']) ? $_POST['resultCode'] : null; 
		$merchantUserId = isset($_POST['merchantUserId']) ? $_POST['merchantUserId'] : null; 
		$reference = isset($_POST['reference']) ? $_POST['reference'] : null; 
		$signature = isset($_POST['signature']) ? $_POST['signature'] : null; 
		$publisherOrderId = isset($_POST['publisherOrderId']) ? $_POST['publisherOrderId'] : null; 
		$spUserHash = isset($_POST['spUserHash']) ? $_POST['spUserHash'] : null; 
		$settlementDate = isset($_POST['settlementDate']) ? $_POST['settlementDate'] : null; 
		$issuerCode = isset($_POST['issuerCode']) ? $_POST['issuerCode'] : null; 
		
		//log callback untuk debug 
		// file_put_contents('callback.txt', "* Callback *\r\n", FILE_APPEND | LOCK_EX);
		
		if(!empty($merchantCode) && !empty($amount) && !empty($merchantOrderId) && !empty($signature))
		{
			$params = $merchantCode . $amount . $merchantOrderId . $apiKey;
			$calcSignature = md5($params);
		
			if($signature == $calcSignature)
			{
				//Callback tervalidasi
				//Silahkan rubah status transaksi anda disini
				// file_put_contents('callback.txt', "* Success *\r\n\r\n", FILE_APPEND | LOCK_EX);
				$transaksi = transaksi::where('invoice', $merchantOrderId)->first();
                $transaksi->status = 'Success';
				$transaksi->status_order = 'Process';
				if($transaksi->payment_method == 'M2'){
				$transaksi->payment_method = 'Mandiri Virtual Account';
				}else if($transaksi->payment_method == 'BC'){
				$transaksi->payment_method = 'BCA Virtual Account';
				}
                $transaksi->save();
			}
			else
			{
				// file_put_contents('callback.txt', "* Bad Signature *\r\n\r\n", FILE_APPEND | LOCK_EX);
				throw new Exception('Bad Signature');
			}
		}
		else
		{
			// file_put_contents('callback.txt', "* Bad Parameter *\r\n\r\n", FILE_APPEND | LOCK_EX);
			throw new Exception('Bad Parameter');
		}
		
	}
}
