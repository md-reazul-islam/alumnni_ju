<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Degree;
use App\Models\Department;
use App\Models\Interest;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Computer Science' => [
                ['name' => 'BSc Computer Science', 'level' => 'undergraduate'],
                ['name' => 'MSc Computer Science', 'level' => 'graduate'],
            ],
            'Business Administration' => [
                ['name' => 'BBA', 'level' => 'undergraduate'],
                ['name' => 'MBA', 'level' => 'graduate'],
            ],
            'Electrical Engineering' => [
                ['name' => 'BSc Electrical Engineering', 'level' => 'undergraduate'],
            ],
            'Mechanical Engineering' => [
                ['name' => 'BSc Mechanical Engineering', 'level' => 'undergraduate'],
            ],
            'Economics' => [
                ['name' => 'BA Economics', 'level' => 'undergraduate'],
                ['name' => 'MA Economics', 'level' => 'graduate'],
            ],
            'Law' => [
                ['name' => 'LLB', 'level' => 'undergraduate'],
                ['name' => 'JD', 'level' => 'graduate'],
            ],
            'Medicine' => [
                ['name' => 'MBBS', 'level' => 'graduate'],
            ],
            'Architecture' => [
                ['name' => 'BArch', 'level' => 'undergraduate'],
            ],
            'Psychology' => [
                ['name' => 'BSc Psychology', 'level' => 'undergraduate'],
            ],
            'Fine Arts' => [
                ['name' => 'BFA', 'level' => 'undergraduate'],
            ],
        ];

        foreach ($departments as $deptName => $programs) {
            $department = Department::updateOrCreate(
                ['slug' => Str::slug($deptName)],
                ['name' => $deptName]
            );

            foreach ($programs as $program) {
                Program::updateOrCreate(
                    ['slug' => Str::slug($deptName . '-' . $program['name'])],
                    ['department_id' => $department->id, 'name' => $program['name'], 'level' => $program['level']]
                );
            }
        }

        $degrees = [
            ['name' => 'Bachelor of Science', 'abbreviation' => 'BSc'],
            ['name' => 'Bachelor of Arts', 'abbreviation' => 'BA'],
            ['name' => 'Bachelor of Business Administration', 'abbreviation' => 'BBA'],
            ['name' => 'Bachelor of Architecture', 'abbreviation' => 'BArch'],
            ['name' => 'Bachelor of Fine Arts', 'abbreviation' => 'BFA'],
            ['name' => 'Bachelor of Laws', 'abbreviation' => 'LLB'],
            ['name' => 'Master of Science', 'abbreviation' => 'MSc'],
            ['name' => 'Master of Arts', 'abbreviation' => 'MA'],
            ['name' => 'Master of Business Administration', 'abbreviation' => 'MBA'],
            ['name' => 'Doctor of Medicine', 'abbreviation' => 'MD'],
            ['name' => 'Doctor of Philosophy', 'abbreviation' => 'PhD'],
        ];

        foreach ($degrees as $degree) {
            Degree::updateOrCreate(['abbreviation' => $degree['abbreviation']], $degree);
        }

        $campuses = [
            ['name' => 'Main Campus', 'city' => 'Springfield', 'country' => 'United States'],
            ['name' => 'Downtown Campus', 'city' => 'Springfield', 'country' => 'United States'],
            ['name' => 'North Campus', 'city' => 'Riverside', 'country' => 'United States'],
        ];

        foreach ($campuses as $campus) {
            Campus::updateOrCreate(['name' => $campus['name']], $campus);
        }

        $interests = [
            'Technology', 'Business', 'Finance', 'Healthcare', 'Research', 'Education',
            'Entrepreneurship', 'Government', 'Social Work', 'Arts', 'Sports',
        ];

        foreach ($interests as $interest) {
            Interest::updateOrCreate(['slug' => Str::slug($interest)], ['name' => $interest]);
        }
    }
}
