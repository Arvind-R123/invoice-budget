<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class InvoiceRequest extends FormRequest { public function authorize(): bool{return true;} public function rules(): array{return ['party_id'=>'required|integer','issue_date'=>'required|date','due_date'=>'required|date','discount_value'=>'nullable|numeric|min:0','discount_type'=>'required|in:flat,percent','tax_rate'=>'nullable|numeric|min:0','items'=>'required|array|min:1','items.*.description'=>'required|string|max:255','items.*.quantity'=>'required|numeric|min:.01','items.*.unit_price'=>'required|numeric|min:0','items.*.tax_rate'=>'nullable|numeric|min:0','items.*.discount_value'=>'nullable|numeric|min:0','items.*.discount_type'=>'nullable|in:flat,percent'];} }
