<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Lawyer;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        User::updateOrCreate(
            ['email' => 'admin@lawsphere.com'],
            [
                'name' => 'System Administrator',
                'password' => $password,
                'role' => UserRole::Admin,
                'phone' => '+1-555-0100',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $lawyers = [
            [
                'name' => 'John Mitchell',
                'email' => 'lawyer@lawsphere.com',
                'phone' => '+1-555-1001',
                'address' => '1200 Legal Plaza, New York, NY 10001',
                'qualifications' => 'JD, Harvard Law School; LLM, Corporate Law',
                'specialization' => 'Corporate Law',
                'experience_years' => 12,
                'biography' => 'Experienced corporate attorney specializing in mergers, acquisitions, and business contracts.',
                'bar_number' => 'BAR-2024-001',
                'is_approved' => true,
            ],
            [
                'name' => 'Sarah Chen',
                'email' => 'sarah.chen@lawsphere.com',
                'phone' => '+1-555-1002',
                'address' => '450 Family Court Drive, Los Angeles, CA 90012',
                'qualifications' => 'JD, Stanford Law School; Certified Family Law Specialist',
                'specialization' => 'Family Law',
                'experience_years' => 9,
                'biography' => 'Compassionate family law advocate focused on divorce, custody, and mediation.',
                'bar_number' => 'BAR-2024-002',
                'is_approved' => true,
            ],
            [
                'name' => 'Michael Torres',
                'email' => 'michael.torres@lawsphere.com',
                'phone' => '+1-555-1003',
                'address' => '88 Justice Avenue, Chicago, IL 60601',
                'qualifications' => 'JD, University of Chicago Law School',
                'specialization' => 'Criminal Law',
                'experience_years' => 15,
                'biography' => 'Former public defender with extensive trial experience in felony and misdemeanor cases.',
                'bar_number' => 'BAR-2024-003',
                'is_approved' => true,
            ],
            [
                'name' => 'Emily Richardson',
                'email' => 'emily.richardson@lawsphere.com',
                'phone' => '+1-555-1004',
                'address' => '210 Immigration Center, Houston, TX 77002',
                'qualifications' => 'JD, Georgetown Law; Immigration Law Certificate',
                'specialization' => 'Immigration Law',
                'experience_years' => 8,
                'biography' => 'Helps clients with visas, green cards, asylum, and citizenship applications.',
                'bar_number' => 'BAR-2024-004',
                'is_approved' => true,
            ],
            [
                'name' => 'David Okafor',
                'email' => 'david.okafor@lawsphere.com',
                'phone' => '+1-555-1005',
                'address' => '75 Property Lane, Miami, FL 33101',
                'qualifications' => 'JD, Columbia Law School; Real Estate Law Board Certification',
                'specialization' => 'Real Estate Law',
                'experience_years' => 11,
                'biography' => 'Specializes in property transactions, landlord-tenant disputes, and zoning matters.',
                'bar_number' => 'BAR-2024-005',
                'is_approved' => true,
            ],
            [
                'name' => 'Priya Sharma',
                'email' => 'priya.sharma@lawsphere.com',
                'phone' => '+1-555-1006',
                'address' => '500 Innovation Way, San Francisco, CA 94105',
                'qualifications' => 'JD, Berkeley Law; MS, Computer Science',
                'specialization' => 'Intellectual Property',
                'experience_years' => 10,
                'biography' => 'Protects patents, trademarks, and copyrights for startups and technology companies.',
                'bar_number' => 'BAR-2024-006',
                'is_approved' => true,
            ],
            [
                'name' => 'Robert Klein',
                'email' => 'robert.klein@lawsphere.com',
                'phone' => '+1-555-1007',
                'address' => '300 Workplace Blvd, Seattle, WA 98101',
                'qualifications' => 'JD, Northwestern Pritzker School of Law',
                'specialization' => 'Employment Law',
                'experience_years' => 14,
                'biography' => 'Represents employees and employers in discrimination, wrongful termination, and HR compliance cases.',
                'bar_number' => 'BAR-2024-007',
                'is_approved' => true,
            ],
            [
                'name' => 'Amanda Foster',
                'email' => 'amanda.foster@lawsphere.com',
                'phone' => '+1-555-1008',
                'address' => '920 Tax Tower, Boston, MA 02108',
                'qualifications' => 'JD, Boston University; LLM, Taxation',
                'specialization' => 'Tax Law',
                'experience_years' => 7,
                'biography' => 'Advises individuals and businesses on tax planning, audits, and IRS disputes.',
                'bar_number' => 'BAR-2024-008',
                'is_approved' => true,
            ],
            [
                'name' => 'James Wilson',
                'email' => 'james.wilson@lawsphere.com',
                'phone' => '+1-555-1009',
                'address' => '15 Harbor Street, Philadelphia, PA 19102',
                'qualifications' => 'JD, Temple University Beasley School of Law',
                'specialization' => 'Personal Injury',
                'experience_years' => 6,
                'biography' => 'Fights for fair compensation in accident and injury claims. Pending admin approval.',
                'bar_number' => 'BAR-2024-009',
                'is_approved' => false,
            ],
        ];

        foreach ($lawyers as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'role' => UserRole::Lawyer,
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            Lawyer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'qualifications' => $data['qualifications'],
                    'specialization' => $data['specialization'],
                    'experience_years' => $data['experience_years'],
                    'biography' => $data['biography'],
                    'bar_number' => $data['bar_number'],
                    'is_approved' => $data['is_approved'],
                    'approved_at' => $data['is_approved'] ? now() : null,
                    'consultation_fee' => 50 + ($data['experience_years'] * 5),
                    'legal_advice_fee' => 25 + ($data['experience_years'] * 2),
                ]
            );
        }

        $clients = [
            ['name' => 'Jane Smith', 'email' => 'client@lawsphere.com', 'phone' => '+1-555-2001', 'address' => '42 Oak Street, Austin, TX 78701'],
            ['name' => 'Marcus Johnson', 'email' => 'marcus.johnson@lawsphere.com', 'phone' => '+1-555-2002', 'address' => '18 River Road, Denver, CO 80202'],
            ['name' => 'Lisa Park', 'email' => 'lisa.park@lawsphere.com', 'phone' => '+1-555-2003', 'address' => '7 Maple Court, Portland, OR 97201'],
            ['name' => 'Daniel Brooks', 'email' => 'daniel.brooks@lawsphere.com', 'phone' => '+1-555-2004', 'address' => '903 Pine Avenue, Atlanta, GA 30303'],
            ['name' => 'Rachel Green', 'email' => 'rachel.green@lawsphere.com', 'phone' => '+1-555-2005', 'address' => '221 Cedar Lane, Nashville, TN 37201'],
            ['name' => 'Kevin Adams', 'email' => 'kevin.adams@lawsphere.com', 'phone' => '+1-555-2006', 'address' => '56 Birch Boulevard, Phoenix, AZ 85001'],
            ['name' => 'Sophia Martinez', 'email' => 'sophia.martinez@lawsphere.com', 'phone' => '+1-555-2007', 'address' => '144 Willow Way, San Diego, CA 92101'],
            ['name' => 'Thomas Wright', 'email' => 'thomas.wright@lawsphere.com', 'phone' => '+1-555-2008', 'address' => '88 Elm Drive, Charlotte, NC 28202'],
        ];

        $clientModels = [];

        foreach ($clients as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'role' => UserRole::Client,
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            $clientModels[] = Client::updateOrCreate(['user_id' => $user->id]);
        }

        $this->seedSampleReviews($clientModels);

        $this->command->newLine();
        $this->command->info('LawSphere demo data seeded successfully.');
        $this->command->info('All accounts use password: password');
        $this->command->newLine();
        $this->command->info('Admin:  admin@lawsphere.com');
        $this->command->info('Lawyers: 9 accounts (8 approved, 1 pending)');
        $this->command->info('Clients: 8 accounts');
    }

    /**
     * @param  array<int, Client>  $clients
     */
    private function seedSampleReviews(array $clients): void
    {
        $approvedLawyers = Lawyer::where('is_approved', true)->get();

        if ($approvedLawyers->isEmpty() || empty($clients)) {
            return;
        }

        $reviews = [
            ['lawyer_email' => 'lawyer@lawsphere.com', 'client_index' => 1, 'rating' => 5, 'text' => 'Excellent guidance on our merger contract. Very professional.'],
            ['lawyer_email' => 'lawyer@lawsphere.com', 'client_index' => 2, 'rating' => 4, 'text' => 'Clear advice and responsive communication throughout.'],
            ['lawyer_email' => 'sarah.chen@lawsphere.com', 'client_index' => 0, 'rating' => 5, 'text' => 'Handled my custody case with care and expertise.'],
            ['lawyer_email' => 'sarah.chen@lawsphere.com', 'client_index' => 3, 'rating' => 5, 'text' => 'Made a difficult divorce process much easier.'],
            ['lawyer_email' => 'michael.torres@lawsphere.com', 'client_index' => 4, 'rating' => 4, 'text' => 'Strong courtroom representation. Highly recommended.'],
            ['lawyer_email' => 'emily.richardson@lawsphere.com', 'client_index' => 5, 'rating' => 5, 'text' => 'Helped my family obtain visas quickly and smoothly.'],
            ['lawyer_email' => 'david.okafor@lawsphere.com', 'client_index' => 6, 'rating' => 4, 'text' => 'Great support with our commercial lease agreement.'],
            ['lawyer_email' => 'priya.sharma@lawsphere.com', 'client_index' => 7, 'rating' => 5, 'text' => 'Protected our startup IP with thorough trademark filing.'],
            ['lawyer_email' => 'robert.klein@lawsphere.com', 'client_index' => 1, 'rating' => 4, 'text' => 'Resolved my workplace dispute fairly and efficiently.'],
            ['lawyer_email' => 'amanda.foster@lawsphere.com', 'client_index' => 2, 'rating' => 5, 'text' => 'Saved us significant money on tax planning strategies.'],
        ];

        foreach ($reviews as $review) {
            $lawyer = $approvedLawyers->first(fn ($l) => $l->user->email === $review['lawyer_email']);
            $client = $clients[$review['client_index']] ?? null;

            if (! $lawyer || ! $client) {
                continue;
            }

            Review::updateOrCreate(
                [
                    'client_id' => $client->id,
                    'lawyer_id' => $lawyer->id,
                    'appointment_id' => null,
                ],
                [
                    'rating' => $review['rating'],
                    'review_text' => $review['text'],
                ]
            );
        }
    }
}
