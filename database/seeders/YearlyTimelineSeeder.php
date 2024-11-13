<?php

namespace Database\Seeders;

use App\Enums\TimelineCategory;
use App\Enums\TimelinePriority;
use App\Enums\TimelineStatus;
use App\Models\Year;
use App\Models\YearlyTimeline;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class YearlyTimelineSeeder extends Seeder
{
    private function calculateStatus(string $startDate, string $endDate): string
    {
        $now = Carbon::now();
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($now->isAfter($end)) {
            return TimelineStatus::Completed->value;
        } elseif ($now->between($start, $end)) {
            return TimelineStatus::InProgress->value;
        } else {
            return TimelineStatus::Pending->value;
        }
    }

    public function run(): void
    {
        $year = Year::current();

        $events = [
            // September - October: Axe 1 - Platform
            [
                'title' => 'Mise à jour des conventions PFE sur la plateforme',
                'description' => 'Plateforme carrière - Mise à jour des conventions et développement',
                'start_date' => '2024-09-01',
                'end_date' => '2024-10-31',
                'category' => TimelineCategory::Administrative,
                'priority' => TimelinePriority::High,
                'status' => $this->calculateStatus('2024-09-01', '2024-10-31'),
                'is_highlight' => true,
                'assigned_users' => [1, 4, 6], // User IDs to assign
            ],
            [
                'title' => 'Collecte des données INE3',
                'description' => 'Demander la liste définitive des INE3 avec leurs ID Konosys et adresses e-mail INPT et la liste des étudiants en mobilité avec soutenance à l\'INPT',
                'start_date' => '2024-09-01',
                'end_date' => '2024-09-15',
                'category' => TimelineCategory::Administrative,
                'priority' => TimelinePriority::Critical,
                'status' => $this->calculateStatus('2024-09-01', '2024-09-15'),
                'is_highlight' => false,
                'assigned_users' => [5],
            ],

            // September - October: Axe 2 - Database
            [
                'title' => 'Mise à jour Base de données',
                'description' => 'Intégration des contacts PFE 2024 et élaboration liste de contacts pour rencontres',
                'start_date' => '2024-09-01',
                'end_date' => '2024-10-01',
                'category' => TimelineCategory::Administrative,
                'priority' => TimelinePriority::High,
                'status' => $this->calculateStatus('2024-09-01', '2024-10-01'),
                'is_highlight' => false,
                'assigned_users' => [1],
            ],

            // October: Axe 4 - PFE 2025 Campaign
            [
                'title' => 'Campagne stages PFE 2025',
                'description' => 'Lancement de la campagne de stages PFE 2025, envoi du mailing et suivi des offres',
                'start_date' => '2024-10-01',
                'end_date' => '2024-10-31',
                'category' => TimelineCategory::Event,
                'priority' => TimelinePriority::Critical,
                'status' => $this->calculateStatus('2024-10-01', '2024-10-31'),
                'is_highlight' => false,
                'assigned_users' => [1, 4, 6],
            ],

            // October - December: Axe 3 - Student-Company Meetings
            [
                'title' => 'Rencontres étudiants-entreprises',
                'description' => 'Organisation et gestion des rencontres étudiants-entreprises',
                'start_date' => '2024-10-01',
                'end_date' => '2024-12-31',
                'category' => TimelineCategory::Event,
                'priority' => TimelinePriority::High,
                'status' => $this->calculateStatus('2024-10-01', '2024-12-31'),
                'is_highlight' => false,
                'assigned_users' => [4],
            ],

            // November: Axe 5 - INE3 Meeting
            [
                'title' => 'Réunion INE3 - Stages PFE',
                'description' => 'Réunion avec les INE3 pour présenter la démarche et le déroulement du stage PFE',
                'start_date' => '2024-10-01',
                'end_date' => '2024-10-01',
                'category' => TimelineCategory::Event,
                'priority' => TimelinePriority::High,
                'status' => $this->calculateStatus('2024-10-01', '2024-10-01'),
                'is_highlight' => false,
                'assigned_users' => [1, 4, 5, 6],
            ],

            // December - January: Axe 6 - PFE 2025 Agreements
            [
                'title' => 'Gestion conventions PFE 2025',
                'description' => 'Réception, vérification et signature des conventions PFE 2025',
                'start_date' => '2024-10-15',
                'end_date' => '2025-01-31',
                'category' => TimelineCategory::Administrative,
                'priority' => TimelinePriority::Critical,
                'status' => $this->calculateStatus('2024-10-15', '2025-01-31'),
                'is_highlight' => false,
                'assigned_users' => [1, 4],
            ],

            // Platform Development Milestones
            [
                'title' => 'Développement - Gestion des offres',
                'description' => 'Développement du module de gestion optimisée des offres de stage',
                'start_date' => '2024-09-01',
                'end_date' => '2024-10-31',
                'category' => TimelineCategory::Deadline,
                'priority' => TimelinePriority::High,
                'status' => $this->calculateStatus('2024-09-01', '2024-10-31'),
                'is_highlight' => false,
                'assigned_users' => [1],
            ],

            // New events
            [
                'title' => 'Soutenances Phase I',
                'description' => 'Première phase des soutenances PFE',
                'start_date' => '2024-06-24',
                'end_date' => '2024-07-15',
                'category' => TimelineCategory::Academic,
                'priority' => TimelinePriority::Critical,
                'status' => $this->calculateStatus('2024-06-24', '2024-07-15'),
                'is_highlight' => true,
                'assigned_users' => [1, 4, 5, 6, 10, 11, 12, 13, 14, 15, 16],
            ],
            [
                'title' => 'Soutenances Phase II',
                'description' => 'Deuxième phase des soutenances PFE',
                'start_date' => '2024-09-19',
                'end_date' => '2024-10-31',
                'category' => TimelineCategory::Academic,
                'priority' => TimelinePriority::Critical,
                'status' => $this->calculateStatus('2024-09-19', '2024-10-31'),
                'is_highlight' => true,
                'assigned_users' => [1, 4, 5, 6, 10, 11, 12, 13, 14, 15, 16],
            ],
            [
                'title' => 'Campagne Mailing des stages PFE',
                'description' => 'Envoi des mails pour les stages PFE',
                'start_date' => '2024-10-03',
                'end_date' => '2024-10-18',
                'category' => TimelineCategory::Communication,
                'priority' => TimelinePriority::High,
                'status' => $this->calculateStatus('2024-10-03', '2024-10-18'),
                'is_highlight' => false,
                'assigned_users' => [1, 4, 6],
            ],

            // PFE Phase 2 Campaign
            [
                'title' => 'Campagne stages PFE Phase II',
                'description' => 'Lancement de la deuxième phase de la campagne de stages PFE, mailing et suivi des offres pour les rattrapages',
                'start_date' => '2024-11-14',
                'end_date' => '2024-11-19',
                'category' => TimelineCategory::Communication,
                'priority' => TimelinePriority::High,
                'status' => $this->calculateStatus('2024-11-14', '2024-11-19'),
                'is_highlight' => true,
                'assigned_users' => [1, 4, 6],
            ],
        ];

        foreach ($events as $event) {
            $assignedUsers = $event['assigned_users'] ?? [];
            unset($event['assigned_users']);

            $timeline = YearlyTimeline::create(array_merge($event, [
                'year_id' => $year->id,
                'color' => $event['category']->getColor(),
                'icon' => $event['category']->getIcon(),
            ]));

            if (! empty($assignedUsers)) {
                $timeline->assignedUsers()->attach($assignedUsers);
            }
        }
    }
}
