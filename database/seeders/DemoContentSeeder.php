<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\AlumniProfile;
use App\Models\AlumniStory;
use App\Models\Campus;
use App\Models\Certification;
use App\Models\Comment;
use App\Models\Company;
use App\Models\CommunityPost;
use App\Models\Connection;
use App\Models\Degree;
use App\Models\Department;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\Education;
use App\Models\Employment;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Interest;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Like;
use App\Models\MentorProfile;
use App\Models\Mentorship;
use App\Models\MentorshipRequest;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Program;
use App\Models\Project;
use App\Models\Publication;
use App\Models\Role;
use App\Models\Skill;
use App\Models\User;
use App\Notifications\AdminAnnouncementPosted;
use App\Notifications\ConnectionAccepted;
use App\Notifications\ConnectionRequestReceived;
use App\Notifications\DonationConfirmation;
use App\Notifications\EventRegistrationConfirmed;
use App\Notifications\JobApproved;
use App\Notifications\MentorshipRequestReceived;
use App\Notifications\ProfileVerified;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Realistic demo/staging content for "Springfield State University" — the fictional
 * institution implied by the campuses (Springfield, Riverside) seeded in
 * ReferenceDataSeeder. NOT wired into DatabaseSeeder. Run explicitly with:
 *
 *   php artisan db:seed --class=DemoContentSeeder
 *
 * Intended for demo/staging environments only — it creates ~50 alumni, events,
 * jobs, news, stories, community activity, donations, mentorship, and connections.
 */
class DemoContentSeeder extends Seeder
{
    private const UNIVERSITY = 'Springfield State University';

    /** @var Collection<int, AlumniProfile> */
    private Collection $alumni;

    private User $staff;

    private User $admin;

    public function run(): void
    {
        DB::transaction(function () {
            $this->admin = User::firstOrCreate(
                ['email' => 'admin@alumni.test'],
                [
                    'role_id' => Role::where('slug', Role::SUPER_ADMIN)->value('id'),
                    'first_name' => 'System',
                    'last_name' => 'Administrator',
                    'password' => 'password',
                    'status' => User::STATUS_VERIFIED,
                ]
            );

            $this->staff = User::firstOrCreate(
                ['email' => 'alumni.relations@alumni.test'],
                [
                    'role_id' => Role::where('slug', Role::ALUMNI_ADMIN)->value('id'),
                    'first_name' => 'Alumni',
                    'last_name' => 'Relations Office',
                    'password' => 'password',
                    'status' => User::STATUS_VERIFIED,
                ]
            );
            $this->staff->forceFill(['email_verified_at' => now()])->save();
            $this->admin->forceFill(['email_verified_at' => now()])->save();

            $skills = $this->seedSkills();
            $interests = Interest::all();
            $companies = $this->seedCompanies();
            $newsCategories = $this->seedNewsCategories();

            $this->alumni = $this->seedAlumni($skills, $interests);

            $this->seedEvents();
            $jobPostings = $this->seedJobs($companies);
            $this->seedNews($newsCategories);
            $announcements = $this->seedAnnouncements();
            $this->seedStories();
            $this->seedCommunity();
            $donations = $this->seedDonations();
            $mentorshipRequests = $this->seedMentorship();
            $connections = $this->seedConnections();

            $this->seedNotifications($jobPostings, $announcements, $donations, $mentorshipRequests, $connections);
        });
    }

    // ------------------------------------------------------------------
    // Reference-ish demo lookups (not touched by ReferenceDataSeeder)
    // ------------------------------------------------------------------

    /** @return Collection<int, Skill> */
    private function seedSkills(): Collection
    {
        $names = [
            'PHP', 'Laravel', 'JavaScript', 'React', 'Python', 'Data Analysis',
            'Project Management', 'Digital Marketing', 'Financial Modeling', 'Public Speaking',
            'Machine Learning', 'SQL', 'Cloud Computing (AWS)', 'UI/UX Design', 'Salesforce',
            'Accounting', 'Clinical Research', 'Curriculum Design', 'Structural Analysis',
            'Supply Chain Management',
        ];

        return collect($names)->map(fn (string $name) => Skill::updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name]
        ));
    }

    /** @return Collection<int, Company> */
    private function seedCompanies(): Collection
    {
        $companies = [
            ['name' => 'Meridian Analytics', 'industry' => 'Technology'],
            ['name' => 'Boro Health Systems', 'industry' => 'Healthcare'],
            ['name' => 'Cascade Financial Group', 'industry' => 'Finance'],
            ['name' => 'Nimbus Cloud Technologies', 'industry' => 'Technology'],
            ['name' => 'Harborview Consulting', 'industry' => 'Consulting'],
            ['name' => 'BrightPath Education', 'industry' => 'Education'],
            ['name' => 'Redwood Legal Partners', 'industry' => 'Legal'],
            ['name' => 'Summit Manufacturing Co.', 'industry' => 'Manufacturing'],
            ['name' => 'Vantage Point Capital', 'industry' => 'Finance'],
            ['name' => 'Clearwater Biotech', 'industry' => 'Healthcare'],
            ['name' => 'Lodestar Software', 'industry' => 'Technology'],
            ['name' => 'Granite Peak Engineering', 'industry' => 'Engineering'],
            ['name' => 'Evergreen Renewable Energy', 'industry' => 'Energy'],
            ['name' => 'Beacon Hill Advisors', 'industry' => 'Finance'],
            ['name' => 'Crestline Logistics', 'industry' => 'Logistics'],
            ['name' => 'Northwind Media Group', 'industry' => 'Media'],
            ['name' => 'Ashford Architecture Studio', 'industry' => 'Architecture'],
            ['name' => 'Sterling Public Relations', 'industry' => 'Marketing'],
            ['name' => 'Pioneer Robotics', 'industry' => 'Technology'],
            ['name' => 'Quantum Leap AI', 'industry' => 'Technology'],
        ];

        return collect($companies)->map(fn (array $c) => Company::updateOrCreate(
            ['slug' => Str::slug($c['name'])],
            [
                'name' => $c['name'],
                'industry' => $c['industry'],
                'website' => 'https://www.' . Str::slug($c['name'], '') . '.example.com',
                'description' => "{$c['name']} is a {$c['industry']}-focused organization headquartered in the United States, employing several ".self::UNIVERSITY." alumni.",
            ]
        ));
    }

    /** @return Collection<int, NewsCategory> */
    private function seedNewsCategories(): Collection
    {
        $names = ['Campus News', 'Alumni Achievements', 'Research & Innovation', 'Events & Reunions', 'University Announcements'];

        return collect($names)->map(fn (string $name) => NewsCategory::updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name]
        ));
    }

    // ------------------------------------------------------------------
    // Alumni (users + profiles + skills/interests + resume-style records)
    // ------------------------------------------------------------------

    /**
     * @param Collection<int, Skill> $skills
     * @param Collection<int, Interest> $interests
     * @return Collection<int, AlumniProfile>
     */
    private function seedAlumni(Collection $skills, Collection $interests): Collection
    {
        $departments = Department::with('programs')->get();
        $campuses = Campus::all();
        $alumniRoleId = Role::where('slug', Role::ALUMNI)->value('id');

        $careerTracks = [
            ['title' => 'Software Engineer', 'industry' => 'Technology'],
            ['title' => 'Senior Data Analyst', 'industry' => 'Technology'],
            ['title' => 'Product Manager', 'industry' => 'Technology'],
            ['title' => 'Financial Analyst', 'industry' => 'Finance'],
            ['title' => 'Investment Banking Associate', 'industry' => 'Finance'],
            ['title' => 'Registered Nurse', 'industry' => 'Healthcare'],
            ['title' => 'Physician Assistant', 'industry' => 'Healthcare'],
            ['title' => 'Mechanical Design Engineer', 'industry' => 'Engineering'],
            ['title' => 'Structural Engineer', 'industry' => 'Engineering'],
            ['title' => 'Corporate Attorney', 'industry' => 'Legal'],
            ['title' => 'Paralegal', 'industry' => 'Legal'],
            ['title' => 'High School Teacher', 'industry' => 'Education'],
            ['title' => 'University Lecturer', 'industry' => 'Education'],
            ['title' => 'UX Designer', 'industry' => 'Technology'],
            ['title' => 'Marketing Manager', 'industry' => 'Marketing'],
            ['title' => 'Business Development Manager', 'industry' => 'Sales'],
            ['title' => 'Supply Chain Analyst', 'industry' => 'Logistics'],
            ['title' => 'Research Scientist', 'industry' => 'Research'],
            ['title' => 'Architect', 'industry' => 'Architecture'],
            ['title' => 'Clinical Psychologist', 'industry' => 'Healthcare'],
        ];

        $bioTemplates = [
            "%s graduated from " . self::UNIVERSITY . " in %d with a focus on %s and now works as a %s at %s.",
            "Since earning their degree in %s from " . self::UNIVERSITY . " (Class of %d), %s has built a career as a %s, currently at %s.",
            "%s (Class of %d, %s) is a %s at %s, and stays closely connected to the " . self::UNIVERSITY . " alumni community.",
        ];

        $bioExtras = [
            'Outside of work, they mentor current students through the alumni mentorship program.',
            'They regularly return to campus for homecoming and guest-lecture opportunities.',
            'They are an active volunteer with the local chapter of the alumni association.',
            'In their free time, they enjoy hiking, travel photography, and coaching youth sports.',
            'They serve on the advisory board for their former department.',
            'They credit their time at ' . self::UNIVERSITY . ' with shaping their approach to problem-solving.',
            'They are passionate about giving back and regularly donates to the scholarship fund.',
            'They occasionally speak on career panels for current students.',
        ];

        $genders = ['male', 'female', 'other', 'prefer_not_to_say'];
        $visibilities = [AlumniProfile::VISIBILITY_PUBLIC, AlumniProfile::VISIBILITY_ALUMNI, AlumniProfile::VISIBILITY_PRIVATE];

        $profiles = collect();

        for ($i = 1; $i <= 50; $i++) {
            $firstName = fake()->firstName();
            $lastName = fake()->lastName();
            $graduationYear = fake()->numberBetween(2005, 2024);

            /** @var Department $department */
            $department = $departments->random();
            $program = $department->programs->isNotEmpty() ? $department->programs->random() : null;
            $level = $program->level ?? 'undergraduate';
            $degreeAbbrev = $level === 'undergraduate'
                ? fake()->randomElement(['BSc', 'BA', 'BBA', 'BArch', 'BFA', 'LLB'])
                : fake()->randomElement(['MSc', 'MA', 'MBA', 'MD', 'PhD']);
            $degree = Degree::where('abbreviation', $degreeAbbrev)->first();
            $admissionYear = $level === 'undergraduate' ? $graduationYear - 4 : $graduationYear - 2;

            $track = fake()->randomElement($careerTracks);
            $company = fake()->randomElement(['Meridian Analytics', 'Boro Health Systems', 'Cascade Financial Group', 'Nimbus Cloud Technologies', 'Harborview Consulting', 'BrightPath Education', 'Redwood Legal Partners', 'Summit Manufacturing Co.', 'Vantage Point Capital', 'Clearwater Biotech', 'Lodestar Software', 'Granite Peak Engineering', 'Evergreen Renewable Energy', 'Beacon Hill Advisors', 'Crestline Logistics', 'Northwind Media Group']);

            $user = User::create([
                'role_id' => $alumniRoleId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => Str::slug($firstName . '.' . $lastName, '.') . $i . '@example.com',
                'password' => Hash::make('password'),
                'phone' => fake()->numerify('##########'),
                'status' => User::STATUS_VERIFIED,
            ]);
            $user->forceFill(['email_verified_at' => now()->subDays(fake()->numberBetween(30, 900))])->save();

            $bioTemplate = fake()->randomElement($bioTemplates);
            $bio = sprintf($bioTemplate, $firstName . ' ' . $lastName, $graduationYear, $department->name, $track['title'], $company)
                . ' ' . fake()->randomElement($bioExtras);

            $profile = AlumniProfile::create([
                'user_id' => $user->id,
                'student_id' => 'SSU-' . $graduationYear . '-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'department_id' => $department->id,
                'program_id' => $program?->id,
                'degree_id' => $degree?->id,
                'campus_id' => $campuses->random()->id,
                'major' => $department->name,
                'admission_year' => $admissionYear,
                'graduation_year' => $graduationYear,
                'batch' => "Class of {$graduationYear}",
                'date_of_birth' => fake()->dateTimeBetween($admissionYear - 24 . '-01-01', $admissionYear - 18 . '-12-31'),
                'gender' => fake()->randomElement($genders),
                'country' => 'United States',
                'city' => fake()->city(),
                'address' => fake()->streetAddress(),
                'job_title' => $track['title'],
                'organization' => $company,
                'industry' => $track['industry'],
                'employment_type' => 'full_time',
                'work_location' => fake()->randomElement(['On-site', 'Hybrid', 'Remote']),
                'linkedin_url' => 'https://www.linkedin.com/in/' . Str::slug($firstName . '-' . $lastName) . '-' . $i,
                'bio' => $bio,
                'profile_visibility' => fake()->randomElement($visibilities),
                'profile_completion' => fake()->numberBetween(55, 100),
                'verified_by' => $this->admin->id,
                'verified_at' => now()->subDays(fake()->numberBetween(1, 800)),
            ]);

            // Skills & interests
            $profile->skills()->attach($skills->random(fake()->numberBetween(3, 6))->pluck('id'));
            $profile->interests()->attach($interests->random(min($interests->count(), fake()->numberBetween(2, 4)))->pluck('id'));

            // Education: primary SSU record for everyone
            Education::create([
                'alumni_profile_id' => $profile->id,
                'institution' => self::UNIVERSITY,
                'degree' => $degree->name ?? $degreeAbbrev,
                'field_of_study' => $department->name,
                'start_year' => $admissionYear,
                'end_year' => $graduationYear,
                'description' => "Completed {$department->name} coursework at " . self::UNIVERSITY . '.',
            ]);

            // Current employment for everyone
            Employment::create([
                'alumni_profile_id' => $profile->id,
                'company_name' => $company,
                'job_title' => $track['title'],
                'industry' => $track['industry'],
                'employment_type' => 'full_time',
                'location' => fake()->city() . ', USA',
                'start_date' => now()->subYears(fake()->numberBetween(1, 5)),
                'end_date' => null,
                'is_current' => true,
                'description' => "Responsible for key initiatives in {$track['industry']} at {$company}, building on a foundation from " . self::UNIVERSITY . '.',
            ]);

            // ~40% get a prior job to show career progression
            if ($i % 5 !== 0) {
                $priorTrack = fake()->randomElement($careerTracks);
                Employment::create([
                    'alumni_profile_id' => $profile->id,
                    'company_name' => fake()->randomElement(['Ashford Architecture Studio', 'Sterling Public Relations', 'Pioneer Robotics', 'Quantum Leap AI', 'Crestline Logistics']),
                    'job_title' => $priorTrack['title'],
                    'industry' => $priorTrack['industry'],
                    'employment_type' => 'full_time',
                    'location' => fake()->city() . ', USA',
                    'start_date' => now()->subYears(fake()->numberBetween(6, 10)),
                    'end_date' => now()->subYears(fake()->numberBetween(2, 5)),
                    'is_current' => false,
                    'description' => "Early-career role focused on {$priorTrack['industry']} fundamentals after graduating from " . self::UNIVERSITY . '.',
                ]);
            }

            // A handful (every 4th alum, ~12-13 people) get extra profile depth
            if ($i % 4 === 0) {
                Achievement::create([
                    'alumni_profile_id' => $profile->id,
                    'title' => fake()->randomElement(['Employee of the Year', 'Rising Star Award', '40 Under 40 Honoree', 'Outstanding Alumni Award', 'Innovation Excellence Award']),
                    'description' => "Recognized for outstanding contributions to {$track['industry']} while at {$company}.",
                    'achieved_on' => now()->subYears(fake()->numberBetween(1, 4)),
                ]);

                Certification::create([
                    'alumni_profile_id' => $profile->id,
                    'name' => fake()->randomElement(['Project Management Professional (PMP)', 'AWS Certified Solutions Architect', 'Certified Public Accountant (CPA)', 'Six Sigma Green Belt', 'Certified ScrumMaster (CSM)']),
                    'issuing_organization' => fake()->randomElement(['PMI', 'Amazon Web Services', 'AICPA', 'ASQ', 'Scrum Alliance']),
                    'issue_date' => now()->subYears(fake()->numberBetween(1, 6)),
                    'expiry_date' => now()->addYears(fake()->numberBetween(1, 3)),
                    'credential_id' => strtoupper(Str::random(8)),
                ]);
            }

            // A smaller handful get publications/projects (research-leaning alumni)
            if ($i % 7 === 0) {
                Publication::create([
                    'alumni_profile_id' => $profile->id,
                    'title' => "Advances in {$department->name}: A Practitioner's Perspective",
                    'publisher' => fake()->randomElement(['Journal of Applied Sciences', 'Springfield Review', 'National Industry Quarterly']),
                    'published_on' => now()->subYears(fake()->numberBetween(1, 5)),
                    'description' => "A peer-reviewed article examining practical applications of {$department->name} in industry, informed by work at {$company}.",
                ]);

                Project::create([
                    'alumni_profile_id' => $profile->id,
                    'title' => fake()->randomElement(['Community Health Data Dashboard', 'Open-Source Scheduling Toolkit', 'Campus Sustainability Tracker', 'Small Business Financial Planner']),
                    'description' => "A side project built to apply {$department->name} skills to a real-world problem, later shared with the " . self::UNIVERSITY . ' alumni community.',
                    'start_date' => now()->subYears(fake()->numberBetween(2, 4)),
                    'end_date' => now()->subYears(fake()->numberBetween(0, 1)),
                ]);
            }

            $profiles->push($profile->fresh());
        }

        return $profiles;
    }

    // ------------------------------------------------------------------
    // Events
    // ------------------------------------------------------------------

    private function seedEvents(): void
    {
        $definitions = [
            ['title' => 'Class of 2015 Reunion Weekend', 'category' => 'reunion', 'mode' => 'offline', 'venue' => 'Alumni Hall, Main Campus', 'days' => -60, 'desc' => 'A weekend of campus tours, a formal dinner, and small-group catch-ups for the Class of 2015, marking their 10-year milestone since graduation.'],
            ['title' => 'Careers in AI: Alumni Panel Webinar', 'category' => 'webinar', 'mode' => 'online', 'venue' => null, 'days' => 20, 'desc' => 'Four ' . self::UNIVERSITY . ' alumni working in machine learning and AI product roles discuss how the field has changed and how to break in, followed by live Q&A.'],
            ['title' => 'Annual Alumni Networking Mixer', 'category' => 'networking', 'mode' => 'offline', 'venue' => 'The Harborview Rooftop, Downtown Springfield', 'days' => 10, 'desc' => 'An evening of casual networking with fellow graduates across industries, hosted at a rooftop venue in downtown Springfield.'],
            ['title' => 'Springfield State Career Fair 2026', 'category' => 'career', 'mode' => 'offline', 'venue' => 'Student Union Arena, Main Campus', 'days' => 45, 'desc' => 'Employers from technology, finance, healthcare, and engineering meet current students and recent graduates for full-time and internship opportunities.'],
            ['title' => "Founders' Gala: Scholarship Fundraiser", 'category' => 'fundraising', 'mode' => 'offline', 'venue' => 'Grand Ballroom, Downtown Campus', 'days' => 75, 'desc' => 'A black-tie evening supporting the Springfield State Scholarship Endowment, featuring a keynote from a distinguished alumni entrepreneur and a live auction.'],
            ['title' => 'Homecoming 2025: Golden Anniversary Celebration', 'category' => 'reunion', 'mode' => 'offline', 'venue' => 'Founders Quad, Main Campus', 'days' => -200, 'desc' => 'Alumni from every decade returned to campus for the annual Homecoming celebration, including a parade, tailgate, and the traditional bonfire.'],
            ['title' => 'Women in STEM Networking Breakfast', 'category' => 'networking', 'mode' => 'offline', 'venue' => 'Innovation Center Atrium, Main Campus', 'days' => -30, 'desc' => 'A morning of conversation and mentorship connecting current STEM students with alumnae working in engineering, technology, and research.'],
            ['title' => 'Entrepreneurship Bootcamp Webinar Series', 'category' => 'webinar', 'mode' => 'online', 'venue' => null, 'days' => -15, 'desc' => 'A three-part virtual series covering fundraising, go-to-market strategy, and legal basics for alumni founders, led by graduates who have built and sold companies.'],
            ['title' => 'Regional Alumni Meetup - Riverside Chapter', 'category' => 'alumni_meetup', 'mode' => 'offline', 'venue' => "O'Malley's Public House, Riverside", 'days' => 5, 'desc' => 'A low-key evening meetup for alumni living in and around Riverside to reconnect and welcome new members to the local chapter.'],
            ['title' => 'Spring Cultural Night & Alumni Showcase', 'category' => 'cultural', 'mode' => 'offline', 'venue' => 'Performing Arts Center, Main Campus', 'days' => 100, 'desc' => 'An evening celebrating the university\'s cultural student organizations, with performances and a showcase of alumni-led community projects.'],
        ];

        foreach ($definitions as $def) {
            $eventDate = now()->addDays($def['days']);
            $isPast = $def['days'] < 0;

            $event = Event::create([
                'organizer_id' => $this->staff->id,
                'title' => $def['title'],
                'slug' => Str::slug($def['title']),
                'description' => $def['desc'],
                'category' => $def['category'],
                'mode' => $def['mode'],
                'venue' => $def['venue'] ?? 'Online',
                'city' => $def['mode'] === 'offline' ? 'Springfield' : null,
                'country' => $def['mode'] === 'offline' ? 'United States' : null,
                'meeting_url' => $def['mode'] === 'online' ? 'https://meet.alumni.example.com/' . Str::slug($def['title']) : null,
                'event_date' => $eventDate,
                'start_time' => '18:00:00',
                'end_time' => '21:00:00',
                'registration_deadline' => $eventDate->copy()->subDays(2),
                'max_participants' => fake()->numberBetween(40, 200),
                'organizer_name' => self::UNIVERSITY . ' Alumni Relations Office',
                'contact_email' => 'alumni.relations@alumni.test',
                'status' => Event::STATUS_PUBLISHED,
                'published_at' => $eventDate->copy()->subDays(30),
            ]);

            $attendees = $this->alumni->random(fake()->numberBetween(8, 15));
            foreach ($attendees as $profile) {
                $status = $isPast ? fake()->randomElement(['attended', 'attended', 'attended', 'cancelled']) : fake()->randomElement(['registered', 'registered', 'registered', 'cancelled']);

                EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id' => $profile->user_id,
                    'status' => $status,
                    'registered_at' => $eventDate->copy()->subDays(fake()->numberBetween(3, 29)),
                    'cancelled_at' => $status === 'cancelled' ? $eventDate->copy()->subDays(1) : null,
                ]);
            }
        }
    }

    // ------------------------------------------------------------------
    // Jobs
    // ------------------------------------------------------------------

    /**
     * @param Collection<int, Company> $companies
     * @return Collection<int, JobPosting>
     */
    private function seedJobs(Collection $companies): Collection
    {
        $definitions = [
            ['title' => 'Software Engineer II', 'type' => 'full_time', 'desc' => 'Join our platform team building customer-facing web applications. Work closely with product and design to ship features used by thousands of users daily.'],
            ['title' => 'Data Analyst', 'type' => 'full_time', 'desc' => 'Analyze operational and customer data to support decision-making across the organization. SQL and dashboarding experience required.'],
            ['title' => 'Product Manager, Growth', 'type' => 'full_time', 'desc' => 'Own the roadmap for our acquisition and onboarding funnels, partnering with engineering, design, and marketing to hit growth targets.'],
            ['title' => 'Financial Analyst', 'type' => 'full_time', 'desc' => 'Support monthly close, budgeting, and forecasting for a growing finance team. Advanced Excel and financial modeling skills preferred.'],
            ['title' => 'Marketing Coordinator', 'type' => 'full_time', 'desc' => 'Coordinate campaign execution across email, social, and paid channels. Great opportunity for a recent graduate looking to grow into a marketing career.'],
            ['title' => 'Registered Nurse - Med/Surg', 'type' => 'full_time', 'desc' => 'Provide direct patient care on a busy medical-surgical unit. Current RN license and BLS certification required.'],
            ['title' => 'Mechanical Design Engineer', 'type' => 'full_time', 'desc' => 'Design and test mechanical components for industrial equipment using SolidWorks. Collaborate with manufacturing on DFM reviews.'],
            ['title' => 'Corporate Associate Attorney', 'type' => 'full_time', 'desc' => 'Support mergers, acquisitions, and general corporate matters for a mid-sized business law practice. JD and bar admission required.'],
            ['title' => 'Adjunct Lecturer, Business', 'type' => 'part_time', 'desc' => "Teach one or two undergraduate business courses per semester. Industry experience and a graduate degree preferred."],
            ['title' => 'UX Designer', 'type' => 'full_time', 'desc' => 'Design intuitive interfaces for our internal tools and customer applications. Portfolio demonstrating end-to-end product design required.'],
            ['title' => 'Supply Chain Analyst', 'type' => 'full_time', 'desc' => 'Optimize inventory planning and logistics workflows across our distribution network. Strong Excel and analytical skills required.'],
            ['title' => 'Business Development Representative', 'type' => 'full_time', 'desc' => 'Generate and qualify new business opportunities for our sales team. Great entry-level role for recent graduates interested in sales.'],
            ['title' => 'Research Assistant, Renewable Energy', 'type' => 'contract', 'desc' => 'Support a 12-month applied research project on battery storage systems. Background in engineering or physical sciences preferred.'],
            ['title' => 'High School Mathematics Teacher', 'type' => 'full_time', 'desc' => 'Teach Algebra II and AP Calculus at a growing charter high school. State teaching certification preferred but not required to apply.'],
            ['title' => 'Summer Investment Banking Intern', 'type' => 'internship', 'desc' => 'A 10-week summer internship rotating through our M&A and capital markets teams. Open to rising seniors and recent graduates.'],
        ];

        $jobPostings = collect();

        foreach ($definitions as $index => $def) {
            $company = $companies->random();
            $posterIsAlumni = $index % 3 !== 0;
            $poster = $posterIsAlumni ? $this->alumni->random()->user_id : $this->staff->id;

            $title = $def['title'];

            $jobPostings->push(JobPosting::create([
                'posted_by' => $poster,
                'company_id' => $company->id,
                'company_name' => $company->name,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . ($index + 1),
                'location' => fake()->randomElement(['Springfield, ST', 'Riverside, ST', 'Remote (USA)', 'Boston, MA', 'Austin, TX', 'Chicago, IL']),
                'employment_type' => $def['type'],
                'industry' => $company->industry,
                'salary_min' => fake()->numberBetween(45, 90) * 1000,
                'salary_max' => fake()->numberBetween(95, 160) * 1000,
                'salary_currency' => 'USD',
                'description' => $def['desc'],
                'requirements' => "Bachelor's degree or equivalent experience. Strong communication skills. " . fake()->numberBetween(0, 5) . '+ years of relevant experience preferred.',
                'application_url' => 'https://careers.' . Str::slug($company->name, '') . '.example.com/jobs/' . Str::slug($title),
                'application_email' => 'careers@' . Str::slug($company->name, '') . '.example.com',
                'deadline' => now()->addDays(fake()->numberBetween(20, 90)),
                'status' => JobPosting::STATUS_APPROVED,
                'approved_by' => $this->admin->id,
                'approved_at' => now()->subDays(fake()->numberBetween(1, 20)),
            ]));
        }

        // Job applications from demo alumni against random postings (unique per pair)
        $pairs = collect();
        while ($pairs->count() < 25) {
            $job = $jobPostings->random();
            $applicant = $this->alumni->random();
            $key = $job->id . '-' . $applicant->user_id;

            if ($pairs->has($key)) {
                continue;
            }

            $pairs->put($key, true);

            JobApplication::create([
                'job_posting_id' => $job->id,
                'user_id' => $applicant->user_id,
                'cover_letter' => "I am writing to express interest in the {$job->title} position. As a " . self::UNIVERSITY . " graduate with a background in {$applicant->industry}, I believe my experience at {$applicant->organization} makes me a strong fit for this role.",
                'status' => fake()->randomElement(['submitted', 'reviewed', 'shortlisted', 'rejected', 'hired']),
                'applied_at' => now()->subDays(fake()->numberBetween(1, 30)),
            ]);
        }

        return $jobPostings;
    }

    // ------------------------------------------------------------------
    // News & Announcements
    // ------------------------------------------------------------------

    /** @param Collection<int, NewsCategory> $categories */
    private function seedNews(Collection $categories): void
    {
        $articles = [
            ['title' => 'Springfield State Breaks Ground on New Innovation Center', 'excerpt' => 'The 80,000-square-foot facility will house startup incubator space, robotics labs, and a maker space for students across disciplines.', 'body' => "University leadership, city officials, and alumni donors gathered on Founders Quad this month to break ground on the new Innovation Center. The 80,000-square-foot facility, funded in part by a lead gift from the Class of 1995, will house a startup incubator, robotics and engineering labs, and a shared maker space open to students across every college.\n\nConstruction is expected to take 18 months, with a planned opening in time for the fall semester. \"This building is a direct result of our alumni community believing in what our students can build,\" said the university president at the ceremony."],
            ['title' => 'University Ranked Among Top 50 Public Universities Nationally', 'excerpt' => "Springfield State climbed twelve spots in this year's national rankings, citing graduate outcomes and research output.", 'body' => "Springfield State University has been ranked among the top 50 public universities in the country for the first time in institutional history, climbing twelve spots from last year's list. The ranking cited strong graduate employment outcomes, growing research expenditures, and improved four-year graduation rates.\n\n\"This recognition reflects the work of our faculty, staff, and especially our alumni, who continue to open doors for current students through mentorship and hiring,\" said the Provost."],
            ['title' => 'Alumni-Founded Startup Acquired by Tech Giant', 'excerpt' => 'A logistics software company founded by two Springfield State computer science graduates has been acquired in an eight-figure deal.', 'body' => "A logistics optimization startup founded by two Springfield State computer science alumni has been acquired by a major technology company in a deal reported to be worth eight figures. The founders met as juniors in a systems design course and built an early prototype of their product for a class project.\n\n\"None of this happens without the professors who pushed us to think bigger,\" one of the founders said in a statement. Both plan to establish a scholarship fund for computer science students in the coming year."],
            ['title' => 'Springfield State Launches New AI Research Lab', 'excerpt' => 'The Department of Computer Science has opened a dedicated lab focused on applied machine learning research with industry partners.', 'body' => "The Department of Computer Science has launched a new applied artificial intelligence research lab, backed by partnerships with several companies founded or led by Springfield State alumni. The lab will support both faculty research and undergraduate research assistantships.\n\nThe first cohort of ten student researchers began work this semester on projects ranging from medical imaging to natural language processing."],
            ['title' => 'Homecoming 2026 Dates Announced', 'excerpt' => 'Mark your calendars: Homecoming Weekend returns to Main Campus this fall with a full schedule of reunions, tailgates, and campus tours.', 'body' => "The Alumni Relations Office has announced dates for Homecoming Weekend 2026, with a full slate of class reunions, the annual tailgate at Founders Quad, and guided tours of the new Innovation Center. Registration opens next month, with early-bird pricing available through the end of the summer.\n\nSpecial milestone reunions are planned for the classes of 1976, 1986, 1996, 2001, and 2016."],
            ['title' => 'Record Number of Graduates Land Jobs Within Six Months', 'excerpt' => "This year's graduating class reported the highest six-month employment rate in the university's history.", 'body' => "According to the annual Career Services outcomes survey, 94% of this year's graduating class reported being employed, enrolled in graduate school, or engaged in a fellowship within six months of graduation — the highest rate on record.\n\nCareer Services credited expanded employer partnerships and a growing alumni mentorship network for the improvement. \"Our alumni show up for our students in ways that make a measurable difference,\" said the Director of Career Services."],
            ['title' => 'New Scholarship Fund Established by Class of 1995', 'excerpt' => 'Members of the Class of 1995 have pooled resources to establish a need-based scholarship ahead of their upcoming reunion.', 'body' => "To mark their upcoming reunion, members of the Class of 1995 have established a new need-based scholarship fund supporting first-generation college students. The fund has already surpassed its initial $150,000 goal, with contributions from more than 60 classmates.\n\nThe first scholarships will be awarded to incoming first-year students for the upcoming academic year."],
            ['title' => 'Springfield State Athletics Wins Regional Championship', 'excerpt' => "The men's basketball team brought home its first regional title in over a decade, with several alumni returning to campus for the celebration.", 'body' => "The Springfield State men's basketball team captured the regional championship this weekend, its first title in over a decade. Hundreds of alumni returned to campus for a celebration on Founders Quad following the win.\n\n\"Seeing alumni from every decade in the stands reminded the team what they're playing for,\" said the head coach after the game."],
            ['title' => 'University Partners with Local Hospitals for Nursing Residency Program', 'excerpt' => 'A new residency program will place nursing graduates directly into paid clinical rotations at three regional hospital systems.', 'body' => "The School of Nursing has launched a new residency program in partnership with three regional hospital systems, several of which are led by Springfield State nursing alumni in senior clinical roles. The program will place new graduates into paid, structured clinical rotations designed to ease the transition from student to practicing nurse.\n\nThe first cohort of 25 residents begins this summer."],
            ['title' => 'Distinguished Alumni Award Recipients Announced for 2026', 'excerpt' => 'Five graduates will be honored at this year\'s Founders\' Gala for outstanding achievement in their fields and service to the university.', 'body' => "The Alumni Association has announced five recipients of this year's Distinguished Alumni Award, recognizing outstanding professional achievement and service to Springfield State. Honorees span careers in medicine, engineering, public service, entrepreneurship, and the arts.\n\nThe awards will be presented at the upcoming Founders' Gala, with proceeds from the event supporting the Springfield State Scholarship Endowment."],
        ];

        foreach ($articles as $index => $article) {
            News::create([
                'author_id' => $index % 2 === 0 ? $this->staff->id : $this->admin->id,
                'news_category_id' => $categories->random()->id,
                'title' => $article['title'],
                'slug' => Str::slug($article['title']),
                'excerpt' => $article['excerpt'],
                'body' => $article['body'],
                'status' => News::STATUS_PUBLISHED,
                'published_at' => now()->subDays(fake()->numberBetween(2, 180)),
                'views' => fake()->numberBetween(50, 3000),
            ]);
        }
    }

    /** @return Collection<int, Announcement> */
    private function seedAnnouncements(): Collection
    {
        $definitions = [
            ['title' => 'Alumni Directory Now Open for Updates', 'body' => 'Please take a moment to review and update your profile in the alumni directory, including your current employer and contact information.', 'audience' => 'alumni', 'pinned' => true],
            ['title' => 'Homecoming Registration Now Open', 'body' => 'Registration for Homecoming Weekend is now open. Early-bird pricing ends soon, so reserve your spot today.', 'audience' => 'all', 'pinned' => true],
            ['title' => 'New Mentorship Program Applications Open', 'body' => 'Applications are now open for both mentors and mentees in this year\'s alumni mentorship program. Sign up from your dashboard.', 'audience' => 'alumni', 'pinned' => false],
            ['title' => 'Annual Giving Campaign Kickoff', 'body' => 'Our Annual Giving Campaign has officially launched. Every gift, no matter the size, helps fund scholarships and student programs.', 'audience' => 'all', 'pinned' => false],
            ['title' => 'Scheduled Website Maintenance', 'body' => 'The alumni portal will be briefly unavailable for scheduled maintenance overnight this weekend.', 'audience' => 'all', 'pinned' => false],
            ['title' => 'Call for Nominations: Distinguished Alumni Award', 'body' => 'Nominate a fellow graduate for next year\'s Distinguished Alumni Award. Nominations close at the end of the month.', 'audience' => 'alumni', 'pinned' => false],
        ];

        return collect($definitions)->map(fn (array $def) => Announcement::create([
            'created_by' => $this->staff->id,
            'title' => $def['title'],
            'body' => $def['body'],
            'audience' => $def['audience'],
            'is_pinned' => $def['pinned'],
            'published_at' => now()->subDays(fake()->numberBetween(1, 30)),
            'expires_at' => now()->addDays(fake()->numberBetween(30, 120)),
        ]));
    }

    // ------------------------------------------------------------------
    // Alumni stories
    // ------------------------------------------------------------------

    private function seedStories(): void
    {
        $storyProfiles = $this->alumni->random(10);

        $narratives = [
            "When I walked across the stage at Founders Quad, I had no idea I'd end up building a company. My time in %s taught me how to break big problems into small, testable pieces — a skill I use every single day as a founder. The first two years were brutal: bootstrapped, sleeping in the office, and more than a few rejected pitches. What kept me going was remembering a professor who told our class that 'the only failed project is the one you stop working on.' Today, our team has grown, and I still call that professor for advice.",
            "I never expected to fall in love with %s, but a required elective changed everything. After graduating, I spent three years learning the fundamentals before landing my current role at %s. The biggest lesson from Springfield State wasn't in any textbook — it was learning to ask for help early and often. I still mentor students from my old department every semester, because someone once did the same for me.",
            "My path after Springfield State wasn't a straight line. I switched careers twice before finding my footing in %s, and I'm grateful every day that I did. Working at %s has given me the chance to apply everything I learned on campus, from late-night study groups to the internship that almost didn't happen. If there's one thing I'd tell current students, it's that the alumni network is real — someone I met at a networking mixer years ago is now one of my closest professional contacts.",
            "Ten years after graduating, I still think about the friendships I built in %s more than the coursework. Those relationships turned into a professional network that helped me land my first three jobs, including my current position at %s. I've made it a point to give back by volunteering with the mentorship program, because the alumni who reached out to me made all the difference when I was starting out.",
        ];

        $titles = [
            'From Dorm Room Project to Series A: My Startup Journey',
            'How Springfield State Prepared Me for a Career I Never Expected',
            'Two Career Changes Later, I Finally Found My Place',
            'The Alumni Network That Changed My Career Trajectory',
            'Building a Nonprofit After Graduation: Lessons From the First Five Years',
            'What Ten Years in Healthcare Taught Me About My Springfield State Education',
            'From Teaching Assistant to Department Head: A Career in Academia',
            'Why I Came Back to Springfield State as a Guest Lecturer',
            'Scaling a Family Business With What I Learned in the Classroom',
            'From Intern to Managing Director: A Finance Career Retrospective',
        ];

        foreach ($storyProfiles as $index => $profile) {
            $narrative = fake()->randomElement($narratives);
            $story = sprintf($narrative, $profile->department?->name ?? 'my program', $profile->organization ?? 'my current company');

            AlumniStory::create([
                'alumni_profile_id' => $profile->id,
                'title' => $titles[$index] ?? ('My Journey Since ' . self::UNIVERSITY),
                'slug' => Str::slug(($titles[$index] ?? 'my-journey') . '-' . $profile->id),
                'story' => $story,
                'achievements' => "Promoted to {$profile->job_title} at {$profile->organization}; active volunteer with the alumni mentorship program; occasional guest speaker for current students.",
                'career_highlight' => $profile->job_title . ' at ' . $profile->organization,
                'status' => AlumniStory::STATUS_PUBLISHED,
                'reviewed_by' => $this->admin->id,
                'published_at' => now()->subDays(fake()->numberBetween(5, 300)),
                'views' => fake()->numberBetween(30, 1500),
            ]);
        }
    }

    // ------------------------------------------------------------------
    // Community: posts, comments, likes, polls
    // ------------------------------------------------------------------

    private function seedCommunity(): void
    {
        $posts = [
            ['title' => 'Best resources for prepping for the PMP certification?', 'category' => 'career', 'body' => "I'm studying for the PMP exam and would love recommendations for study guides or practice exams that actually helped. Bonus points if a fellow alum has taken it recently."],
            ['title' => 'Anyone else attending the Riverside meetup next week?', 'category' => 'regional', 'body' => "I just registered for the Riverside chapter meetup and would love to know who else from the Class of 2018 onward is planning to go. Happy to carpool from Springfield."],
            ['title' => 'Looking for a co-founder with backend experience', 'category' => 'entrepreneurship', 'body' => "I'm building an early-stage product in the logistics space and looking for a technical co-founder, ideally with Laravel or Django experience. DM me if you're interested in hearing more."],
            ['title' => "Favorite professor from your time at Springfield State?", 'category' => 'social', 'body' => "Feeling nostalgic today — who was your favorite professor and why? I'll start: mine completely changed how I think about writing."],
            ['title' => 'How did you negotiate your first job offer?', 'category' => 'career', 'body' => "I have an offer in hand and want to negotiate but I've never done it before. Any advice from people who've been through a few negotiations?"],
            ['title' => 'Research collaboration opportunity: renewable energy storage', 'category' => 'research', 'body' => "I'm working on a battery storage research project and looking for alumni collaborators with a materials science or electrical engineering background."],
            ['title' => 'Recommendations for remote-friendly companies hiring right now', 'category' => 'career', 'body' => "Looking to make a move to a fully remote role in the next few months. Would love to hear which companies have treated alumni well as remote employees."],
            ['title' => 'Throwback: campus during the 2010 snowstorm', 'category' => 'social', 'body' => "Found some old photos from when campus got buried in snow back in 2010 and classes were cancelled for a full week. Anyone else remember that?"],
            ['title' => 'Anyone working with LLMs in production? Would love to compare notes', 'category' => 'technology', 'body' => "Our team just shipped our first LLM-powered feature and I'd love to swap lessons learned with other alumni working on similar problems."],
            ['title' => 'Small business owners - how did you fund your first year?', 'category' => 'entrepreneurship', 'body' => "Thinking about leaving my job to start a small business and trying to understand realistic funding options beyond savings. Curious how other alumni founders did it."],
            ['title' => 'Grad school vs. industry - looking for perspectives', 'category' => 'academic', 'body' => "I have an offer to start a master's program in the fall but also a solid job offer. Would love to hear from people who chose either path."],
            ['title' => "Springfield alumni in the Bay Area - let's meet up", 'category' => 'regional', 'body' => "There are clearly a lot of us out here. Thinking about organizing an informal happy hour next month — reply if you'd be interested."],
            ['title' => "What's changed most on campus since you graduated?", 'category' => 'social', 'body' => "Went back for homecoming and barely recognized the north side of campus. What surprised you most on your last visit?"],
            ['title' => 'Hiring: looking for a junior data analyst on our team', 'category' => 'career', 'body' => "My team is hiring a junior data analyst and I'd love to find a fellow Springfield State grad. Feel free to reach out directly if interested."],
            ['title' => 'Poll: What was your favorite campus dining hall?', 'category' => 'social', 'body' => "Settling a debate with my old roommates. Vote below!", 'poll' => true],
            ['title' => 'Poll: Which alumni event should we prioritize this year?', 'category' => 'career', 'body' => "The alumni board is planning next year's calendar and wants your input on which event to invest the most in.", 'poll' => true],
            ['title' => 'Poll: Best format for the annual mentorship kickoff?', 'category' => 'academic', 'body' => "We're redesigning the mentorship kickoff event and want to hear from past participants on the format.", 'poll' => true],
            ['title' => 'Tips for first-time conference speakers?', 'category' => 'academic', 'body' => "I've been invited to speak at an industry conference for the first time and would love any advice from alumni who've done it before."],
        ];

        $commentBodies = [
            'This is really helpful, thanks for sharing!',
            'I went through something similar - happy to chat more if useful.',
            'Following this thread, would love more recommendations too.',
            "Great question, I've been wondering the same thing.",
            'I can put you in touch with someone from my network on this.',
            'Congrats, this is awesome to see!',
            "I remember dealing with exactly this after I graduated.",
            'Sending you a DM with more details.',
        ];

        $pollDefinitions = [
            'Poll: What was your favorite campus dining hall?' => ['The Grove', 'Riverside Commons', 'Union Food Court', 'Café Verde'],
            'Poll: Which alumni event should we prioritize this year?' => ['Career Fair', 'Regional Meetups', 'Homecoming Reunion', 'Mentorship Kickoff'],
            'Poll: Best format for the annual mentorship kickoff?' => ['In-person mixer', 'Virtual panel', 'Small group dinners', 'Hybrid event'],
        ];

        foreach ($posts as $index => $def) {
            $author = $this->alumni->random();

            $post = CommunityPost::create([
                'user_id' => $author->user_id,
                'category' => $def['category'],
                'title' => $def['title'],
                'body' => $def['body'],
                'post_type' => ! empty($def['poll']) ? 'poll' : 'post',
                'status' => CommunityPost::STATUS_PUBLISHED,
            ]);

            // Comments (skip a few posts to vary engagement levels)
            if ($index % 4 !== 3) {
                $commenters = $this->alumni->random(fake()->numberBetween(2, 4));
                foreach ($commenters as $commenter) {
                    Comment::create([
                        'user_id' => $commenter->user_id,
                        'commentable_type' => CommunityPost::class,
                        'commentable_id' => $post->id,
                        'body' => fake()->randomElement($commentBodies),
                        'status' => 'published',
                    ]);
                }
            }

            // Likes
            $likers = $this->alumni->random(fake()->numberBetween(3, 10))->unique('user_id');
            foreach ($likers as $liker) {
                Like::firstOrCreate([
                    'user_id' => $liker->user_id,
                    'likeable_type' => CommunityPost::class,
                    'likeable_id' => $post->id,
                ]);
            }

            // Polls
            if (! empty($def['poll']) && isset($pollDefinitions[$def['title']])) {
                $poll = Poll::create([
                    'community_post_id' => $post->id,
                    'question' => $def['title'],
                    'expires_at' => now()->addDays(14),
                ]);

                $options = collect($pollDefinitions[$def['title']])->map(fn (string $text) => PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => $text,
                ]));

                $voters = $this->alumni->random(fake()->numberBetween(15, 25))->unique('user_id');
                foreach ($voters as $voter) {
                    $option = $options->random();
                    PollVote::firstOrCreate(
                        ['poll_id' => $poll->id, 'user_id' => $voter->user_id],
                        ['poll_option_id' => $option->id]
                    );
                }
            }
        }
    }

    // ------------------------------------------------------------------
    // Donations
    // ------------------------------------------------------------------

    /** @return Collection<int, Donation> */
    private function seedDonations(): Collection
    {
        $campaigns = collect([
            DonationCampaign::create([
                'created_by' => $this->staff->id,
                'title' => 'Springfield State Scholarship Endowment',
                'slug' => 'scholarship-endowment',
                'description' => 'Supporting need-based and merit scholarships for current and future Springfield State students.',
                'category' => 'scholarship',
                'goal_amount' => 250000,
                'raised_amount' => 0,
                'start_date' => now()->subMonths(6),
                'end_date' => now()->addMonths(6),
                'status' => DonationCampaign::STATUS_ACTIVE,
            ]),
            DonationCampaign::create([
                'created_by' => $this->staff->id,
                'title' => 'New Innovation Center Fund',
                'slug' => 'innovation-center-fund',
                'description' => 'Help fund construction of the new Innovation Center, home to student startups and research labs.',
                'category' => 'infrastructure',
                'goal_amount' => 500000,
                'raised_amount' => 0,
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(12),
                'status' => DonationCampaign::STATUS_ACTIVE,
            ]),
            DonationCampaign::create([
                'created_by' => $this->staff->id,
                'title' => 'Emergency Student Support Fund',
                'slug' => 'emergency-student-support-fund',
                'description' => 'Provides short-term emergency grants to students facing unexpected financial hardship.',
                'category' => 'emergency_fund',
                'goal_amount' => 75000,
                'raised_amount' => 0,
                'start_date' => now()->subMonths(9),
                'end_date' => now()->addMonths(3),
                'status' => DonationCampaign::STATUS_ACTIVE,
            ]),
        ]);

        $amounts = [25, 50, 100, 100, 250, 500, 1000, 5000];
        $donations = collect();

        for ($i = 0; $i < 15; $i++) {
            $donor = $this->alumni->random();
            $campaign = $campaigns->random();
            $isAnonymous = $i % 6 === 0;
            $status = $i % 8 === 0 ? Donation::PAYMENT_PENDING : Donation::PAYMENT_COMPLETED;

            $donations->push(Donation::create([
                'donor_id' => $donor->user_id,
                'donation_campaign_id' => $campaign->id,
                'donor_name' => $isAnonymous ? null : $donor->user->full_name,
                'donor_email' => $isAnonymous ? null : $donor->user->email,
                'amount' => fake()->randomElement($amounts),
                'currency' => 'USD',
                'category' => $campaign->category,
                'is_anonymous' => $isAnonymous,
                'payment_method' => fake()->randomElement(['card', 'bank_transfer', 'paypal']),
                'payment_status' => $status,
                'transaction_reference' => 'TXN-' . strtoupper(Str::random(10)),
                'notes' => $i % 5 === 0 ? 'In honor of my time in the ' . ($donor->department?->name ?? 'university') . ' program.' : null,
            ]));
        }

        foreach ($campaigns as $campaign) {
            $raised = $donations->where('donation_campaign_id', $campaign->id)
                ->where('payment_status', Donation::PAYMENT_COMPLETED)
                ->sum('amount');
            $campaign->update(['raised_amount' => $raised]);
        }

        return $donations;
    }

    // ------------------------------------------------------------------
    // Mentorship
    // ------------------------------------------------------------------

    /** @return Collection<int, MentorshipRequest> */
    private function seedMentorship(): Collection
    {
        $seniorAlumni = $this->alumni->filter(fn (AlumniProfile $p) => $p->graduation_year <= 2016)->values();
        $mentors = $seniorAlumni->isNotEmpty() ? $seniorAlumni->random(min(8, $seniorAlumni->count())) : $this->alumni->random(8);

        $topicsPool = ['Career transitions', 'Resume & interview prep', 'Breaking into tech', 'Graduate school advice', 'Entrepreneurship', 'Leadership development', 'Work-life balance', 'Public speaking'];

        $mentorProfiles = $mentors->map(fn (AlumniProfile $profile, int $idx) => MentorProfile::create([
            'user_id' => $profile->user_id,
            'expertise' => $profile->industry ?? 'General career guidance',
            'industry' => $profile->industry,
            'experience_years' => fake()->numberBetween(6, 20),
            'availability' => fake()->randomElement(['1 hour/month', '2 hours/month', 'Bi-weekly', 'On request']),
            'topics' => implode(', ', Arr::random($topicsPool, 3)),
            'bio' => "I've spent over a decade in {$profile->industry} since graduating from " . self::UNIVERSITY . " and enjoy helping recent graduates navigate similar career paths.",
            'is_active' => $idx !== 0,
        ]));

        $menteeCandidates = $this->alumni->reject(fn (AlumniProfile $p) => $mentors->contains('user_id', $p->user_id))->values();

        $statuses = [
            MentorshipRequest::STATUS_PENDING,
            MentorshipRequest::STATUS_ACCEPTED,
            MentorshipRequest::STATUS_ACCEPTED,
            MentorshipRequest::STATUS_REJECTED,
            MentorshipRequest::STATUS_COMPLETED,
        ];

        $requests = collect();

        for ($i = 0; $i < 12; $i++) {
            $mentee = $menteeCandidates->random();
            $mentorProfile = $mentors->random();
            $status = fake()->randomElement($statuses);

            $request = MentorshipRequest::create([
                'mentee_id' => $mentee->user_id,
                'mentor_id' => $mentorProfile->user_id,
                'message' => "Hi, I'm a fellow " . self::UNIVERSITY . " grad interested in {$mentorProfile->industry}. Would you be open to a mentorship conversation about breaking into the field?",
                'status' => $status,
                'responded_at' => $status === MentorshipRequest::STATUS_PENDING ? null : now()->subDays(fake()->numberBetween(1, 60)),
            ]);

            $requests->push($request);

            if (in_array($status, [MentorshipRequest::STATUS_ACCEPTED, MentorshipRequest::STATUS_COMPLETED], true)) {
                $isCompleted = $status === MentorshipRequest::STATUS_COMPLETED;

                Mentorship::create([
                    'mentorship_request_id' => $request->id,
                    'mentor_id' => $request->mentor_id,
                    'mentee_id' => $request->mentee_id,
                    'started_at' => now()->subDays(fake()->numberBetween(30, 90)),
                    'ended_at' => $isCompleted ? now()->subDays(fake()->numberBetween(1, 20)) : null,
                    'status' => $isCompleted ? Mentorship::STATUS_COMPLETED : Mentorship::STATUS_ACTIVE,
                ]);
            }
        }

        return $requests;
    }

    // ------------------------------------------------------------------
    // Connections
    // ------------------------------------------------------------------

    /** @return Collection<int, Connection> */
    private function seedConnections(): Collection
    {
        $connections = collect();
        $seen = collect();
        $attempts = 0;

        while ($connections->count() < 30 && $attempts < 200) {
            $attempts++;

            $requester = $this->alumni->random();
            $recipient = $this->alumni->random();

            if ($requester->user_id === $recipient->user_id) {
                continue;
            }

            $key = min($requester->user_id, $recipient->user_id) . '-' . max($requester->user_id, $recipient->user_id);
            if ($seen->has($key)) {
                continue;
            }
            $seen->put($key, true);

            $isAccepted = $connections->count() < 20;

            $connections->push(Connection::create([
                'requester_id' => $requester->user_id,
                'recipient_id' => $recipient->user_id,
                'status' => $isAccepted ? Connection::STATUS_ACCEPTED : Connection::STATUS_PENDING,
                'responded_at' => $isAccepted ? now()->subDays(fake()->numberBetween(1, 90)) : null,
            ]));
        }

        return $connections;
    }

    // ------------------------------------------------------------------
    // Notifications (reuses the app's real Notification classes)
    // ------------------------------------------------------------------

    /**
     * @param Collection<int, JobPosting> $jobPostings
     * @param Collection<int, Announcement> $announcements
     * @param Collection<int, Donation> $donations
     * @param Collection<int, MentorshipRequest> $mentorshipRequests
     * @param Collection<int, Connection> $connections
     */
    private function seedNotifications(
        Collection $jobPostings,
        Collection $announcements,
        Collection $donations,
        Collection $mentorshipRequests,
        Collection $connections
    ): void {
        // Job approvals, for alumni-posted jobs
        foreach ($jobPostings->filter(fn (JobPosting $j) => $j->poster?->isAlumni())->take(6) as $job) {
            $job->poster->notify(new JobApproved($job));
        }

        // Connection requests / acceptances
        foreach ($connections->take(10) as $connection) {
            if ($connection->status === Connection::STATUS_ACCEPTED) {
                $connection->requester->notify(new ConnectionAccepted($connection));
            } else {
                $connection->recipient->notify(new ConnectionRequestReceived($connection));
            }
        }

        // Mentorship requests, notify the mentor
        foreach ($mentorshipRequests->take(8) as $request) {
            $request->mentor->notify(new MentorshipRequestReceived($request));
        }

        // Donation confirmations, for identified (non-anonymous) donors
        foreach ($donations->where('is_anonymous', false)->take(8) as $donation) {
            if ($donation->donor) {
                $donation->donor->notify(new DonationConfirmation($donation));
            }
        }

        // Announcement broadcasts to a sample of alumni
        $sampleAlumni = $this->alumni->random(min(15, $this->alumni->count()));
        foreach ($announcements->take(2) as $announcement) {
            foreach ($sampleAlumni->take(8) as $profile) {
                $profile->user->notify(new AdminAnnouncementPosted($announcement));
            }
        }

        // Welcome / verification notifications for a handful of alumni
        foreach ($this->alumni->random(10) as $profile) {
            $profile->user->notify(new ProfileVerified());
        }
    }
}
