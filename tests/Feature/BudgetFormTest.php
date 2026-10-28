<?php
namespace Tests\Feature;
use App\Models\{User,Budget}; use Illuminate\Foundation\Testing\RefreshDatabase; use Tests\TestCase;
class BudgetFormTest extends TestCase { use RefreshDatabase; public function test_saved_yearly_period_is_preselected_on_edit(): void { $user=User::factory()->create(['role'=>'admin']);$budget=Budget::create(['name'=>'Annual','period_type'=>'yearly','category'=>'Ops','amount'=>'1000','period_start'=>'2026-01-01','period_end'=>'2026-12-31']);$this->actingAs($user)->get(route('budgets.edit',$budget))->assertOk()->assertSee('<option value="yearly" selected>Yearly</option>',false); } }
