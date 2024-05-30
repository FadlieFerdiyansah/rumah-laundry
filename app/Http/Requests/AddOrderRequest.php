<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'status_payment'    => 'required',
          'kg'                => 'required|regex:/^[0-9.]+$/|numeric',
          'hari'              => 'required',
          'payment_method'  => 'required',
          'disc'              => 'nullable|numeric',
          'customer_id'       => 'required'
        ];
    }

    public function messages()
    {
      return [
        'status_payment.required'   => 'Status Pembayaran wajib dipilih.',
        'kg.required'               => 'Berat Pakaian tidak boleh kosong.',
        'kg.numeric'                => 'Berat Pakaian hanya mendukung angka.',
        'hari.required'             => 'Hari tidak boleh kosong.',
        'payment_method.required' => 'Jenis Pembayaran wajib dipilih.',
        'disc.numeric'              => 'Diskon hanya mendukung angka.',
        'customer_id.required'      => 'Customer wajib dipilih.'
      ];
    }
}
