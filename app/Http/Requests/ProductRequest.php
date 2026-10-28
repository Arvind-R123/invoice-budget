<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ProductRequest extends FormRequest { public function authorize(): bool{return true;} public function rules(): array{return ['name'=>'required|string|max:255','type'=>'required|in:product,service','unit_price'=>'required|numeric|min:0','tax_rate'=>'required|numeric|min:0|max:100'];} }
