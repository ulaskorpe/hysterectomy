<?php

use Carbon\Carbon;
use App\Models\WorkingHour;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $days = [
            ['label' => 'Pazartesi', 'day_of_week' => 1, 'is_active' => true],
            ['label' => 'Salı', 'day_of_week' => 2, 'is_active' => true],
            ['label' => 'Çarşamba', 'day_of_week' => 3, 'is_active' => true],
            ['label' => 'Perşembe', 'day_of_week' => 4, 'is_active' => true],
            ['label' => 'Cuma', 'day_of_week' => 5, 'is_active' => true],
            ['label' => 'Cumartesi', 'day_of_week' => 6, 'is_active' => false],
            ['label' => 'Pazar', 'day_of_week' => 7, 'is_active' => false],
        ];

        foreach ($days as $key => $value) {
            WorkingHour::create([
                'day_name' => $value['label'],
                'day_of_week' => $value['day_of_week'],
                'is_active' => $value['is_active'],
                'start_time' => Carbon::createFromFormat('H:i:s', '09:00:00'),
                'end_time' => Carbon::createFromFormat('H:i:s', '16:30:00')
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
