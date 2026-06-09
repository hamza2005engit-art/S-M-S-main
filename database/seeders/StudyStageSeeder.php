<?php

namespace Database\Seeders;

use App\Models\StudyStage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudyStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matirials = [
            'Mathematics',
            'Science',
            'Arabic',
            'History',
            'Geography',
            'Art',
            'Physical Education',
            'Music',
            'Computer Science',
            'English',
        ];
        for ($i = 7; $i <= 12; $i++) {
            StudyStage::create([
                'stage_number' => $i,
            ]);
            for ($j = 1; $j <= 3; $j++) {
                $studyStage = StudyStage::find($i-6);
                $studyStage->sections()->create([
                    'section_number' => $j,
                    'study_stage_id' => $studyStage->id,
                ]);
                $studyStage->save();
            }
            $studyStage = StudyStage::find($i-6);
            foreach ($matirials as $material) {
                $studyStage->materials()->create([
                    'name' => $material,
                    'study_stage_id' => $studyStage->id,
                ]);
                $studyStage->save();
            }
        }
    }
}
