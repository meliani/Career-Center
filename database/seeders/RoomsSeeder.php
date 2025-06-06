<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Enums\RoomStatus;
use Illuminate\Database\Seeder;

class RoomsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::create([
            'name' => 'Amphi 1',
            'description' => 'Amphi 1',
            'status' => RoomStatus::Available,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        Room::create([
            'name' => 'Amphi 2',
            'description' => 'Amphi 2',
            'status' => RoomStatus::Available,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        Room::create([
            'name' => 'Amphi 3',
            'description' => 'Amphi 3',
            'status' => RoomStatus::Available,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        Room::create([
            'name' => 'Amphi 4',
            'description' => 'Amphi 4',
            'status' => RoomStatus::Available,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

    }
}
