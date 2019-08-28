<?php

use App\Expense;
use Illuminate\Database\Seeder;

class expensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('goods')) {
            $expense_name = [
                'Հոսանք',
                'Գազ',
                'Ջուր',
                'Աշխատավաձ',

            ];
            $expense_unit = [
                'կվ',
                'մ³',
                'մ³',
                'անձ',
            ];

            for($i = 0; $i < count($expense_name); $i++){
                $expense = new Expense;
                $expense->name = $expense_name[$i];
                $expense->unit = $expense_unit[$i];
                $expense->save();
            }
        }
    }
}
