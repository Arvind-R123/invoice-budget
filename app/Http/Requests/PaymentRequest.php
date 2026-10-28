<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['amount' => 'required|numeric|min:.01', 'payment_date' => 'required|date', 'method' => 'required|in:Cash,Bank,UPI,Card,cash,bank,upi,card', 'reference' => 'nullable|string|max:255', 'notes' => 'nullable|string'];
    }
}
