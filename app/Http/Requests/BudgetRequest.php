<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class BudgetRequest extends FormRequest { public function authorize(): bool{return true;} public function rules(): array{return ['name'=>'required|string|max:255','period_type'=>'required|in:monthly,yearly','category'=>'required|string|max:255','amount'=>'required|numeric|min:0.01','period_start'=>'required|date','period_end'=>'required|date|after_or_equal:period_start'];} }
