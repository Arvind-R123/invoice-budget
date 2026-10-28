<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ExpenseRequest extends FormRequest { public function authorize(): bool{return true;} public function rules(): array{return ['budget_id'=>'required|exists:budgets,id','amount'=>'required|numeric|min:0.01','expense_date'=>'required|date','description'=>'required|string|max:255','category'=>'required|string|max:255'];} }
