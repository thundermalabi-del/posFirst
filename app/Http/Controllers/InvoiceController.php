<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use App\Models\Transaction;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function print($id)
    {
        $transaction = Transaction::with(['items.product'])->findOrFail($id);

        $storeSettings = StoreSetting::getSettings();

        return view('invoice.print', compact('transaction', 'storeSettings'));
    }

    public function download($id)
    {
        $transaction = Transaction::with(['items.product'])->findOrFail($id);

        return view('invoice.download', compact('transaction'));
    }

    public function getTransactionData($id)
    {
        try {
            $transaction = Transaction::with(['items.product'])->findOrFail($id);
            $storeSettings = StoreSetting::getSettings();

            // Format the transaction data for the receipt
            $transactionData = [
                'id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
                'created_at' => $transaction->created_at,
                'customer_name' => $transaction->customer_name,
                'subtotal' => $transaction->subtotal,
                'discount' => $transaction->discount,
                'discount_amount' => $transaction->discount,
                'tax' => $transaction->tax,
                'tax_amount' => $transaction->tax,
                'total_amount' => $transaction->total_amount,
                'paid_amount' => $transaction->paid_amount,
                'change_amount' => $transaction->change_amount,
                'payment_method' => $transaction->payment_method,
                // Add store settings to transaction data
                'store_name' => $storeSettings->name,
                'store_address' => $storeSettings->address,
                'store_phone' => $storeSettings->phone,
                'store_email' => $storeSettings->email,
                'items' => $transaction->items->map(function ($item) {
                    return [
                        'product_name' => $item->product ? $item->product->name : null,
                        'custom_product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'unit' => $item->product ? $item->product->unit : '-',
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                        'price_type' => $item->price_type,
                    ];
                })
            ];

            return response()->json($transactionData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
    }
}
