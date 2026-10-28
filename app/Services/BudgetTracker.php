<?php
namespace App\Services;
use App\Models\Budget;
class BudgetTracker { public function summarize(Budget $budget): array { $used='0.00'; foreach($budget->expenses as $expense){$used=bcadd($used,(string)$expense->amount,2);} $amount=(string)$budget->amount; $remaining=bcsub($amount,$used,2); $percentage=bccomp($amount,'0.00',2)===0?'0.00':bcmul(bcdiv($used,$amount,6),'100',2); $status=bccomp($percentage,'100.00',2)>=0?'Over budget':(bccomp($percentage,'80.00',2)>=0?'Near limit':'Within budget'); return compact('used','remaining','percentage','status'); } }
