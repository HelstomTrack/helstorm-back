<?php

namespace App\Service;

use App\Entity\Plan;
use Doctrine\ORM\EntityManagerInterface;

class ProgramSelectorService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getProgram(string $goal, float $weight, float $height): ?Plan
    {
        $weightCategory = $this->categorizeWeight($weight);
        $heightCategory = $this->categorizeHeight($height);

        $morphoType = "{$goal}_{$weightCategory}_{$heightCategory}";

        $programName = $this->getProgramMapping()[$morphoType] ?? null;

        if (!$programName) {
            return null;
        }
        // Trouver le Plan associé à ce Programme
        return $this->entityManager->getRepository(Plan::class)->findOneBy(['name' => $programName]);
    }

    private function categorizeWeight(float $weight): string
    {
        return $weight <= 60 ? "light" : ($weight < 80 ? "medium" : "heavy");
    }


    private function categorizeHeight(float $height): string
    {
        return $height < 165 ? "little" : ($height <= 180 ? "average" : "big");
    }

   private function getProgramMapping(): array
    {
        return [
            // 🔥 Strong (Prise de Masse + Force)
            'Strong_light_little' => 'Full Body',
            'Strong_light_average' => 'Upper-Lower',
            'Strong_light_big' => 'Push-Pull',
            'Strong_medium_little' => 'PPL',
            'Strong_medium_average' => 'PPL',
            'Strong_medium_big' => 'Strength Training',
            'Strong_heavy_little' => 'Powerlifting',
            'Strong_heavy_average' => 'Powerlifting',
            'Strong_heavy_big' => 'Strongman',

            // 🏆 Shred (Définition et Perte de Poids)
            'Shred_light_little' => 'Full Body',
            'Shred_light_average' => 'Cardio & Conditioning',
            'Shred_light_big' => 'HIIT',
            'Shred_medium_little' => 'PPL',
            'Shred_medium_average' => 'Functional Fitness',
            'Shred_medium_big' => 'CrossFit',
            'Shred_heavy_little' => 'Athlétique',
            'Shred_heavy_average' => 'Endurance Training',
            'Shred_heavy_big' => 'Strongman',

            // 💪 Bulk (Hypertrophie musculaire)
            'Bulk_light_little' => 'Full Body',
            'Bulk_light_average' => 'Push-Pull',
            'Bulk_light_big' => 'PPL',
            'Bulk_medium_little' => 'PPL',
            'Bulk_medium_average' => 'Hypertrophy',
            'Bulk_medium_big' => 'Strength Training',
            'Bulk_heavy_little' => 'Powerlifting',
            'Bulk_heavy_average' => 'Powerlifting',
            'Bulk_heavy_big' => 'Strongman',

            // ⚖️ Fit (Équilibré, Fonctionnel)
            'Fit_light_little' => 'Calisthenics',
            'Fit_light_average' => 'Full Body',
            'Fit_light_big' => 'Athlétique',
            'Fit_medium_little' => 'Functional Fitness',
            'Fit_medium_average' => 'CrossFit',
            'Fit_medium_big' => 'PPL',
            'Fit_heavy_little' => 'Power & Endurance',
            'Fit_heavy_average' => 'Cardio & Conditioning',
            'Fit_heavy_big' => 'Strongman',

            // 🏋️‍♂️ Power (Force Maximale)
            'Power_light_little' => 'Strength Training',
            'Power_light_average' => 'Powerlifting',
            'Power_light_big' => 'Strongman',
            'Power_medium_little' => 'Powerlifting',
            'Power_medium_average' => 'Powerlifting',
            'Power_medium_big' => 'Strongman',
            'Power_heavy_little' => 'Strongman',
            'Power_heavy_average' => 'Strongman',
            'Power_heavy_big' => 'Strongman',

            // 🔥 Cut (Perte de poids, tonification)
            'Cut_light_little' => 'Full Body',
            'Cut_light_average' => 'HIIT',
            'Cut_light_big' => 'Cardio & Conditioning',
            'Cut_medium_little' => 'Athlétique',
            'Cut_medium_average' => 'CrossFit',
            'Cut_medium_big' => 'Functional Fitness',
            'Cut_heavy_little' => 'Endurance Training',
            'Cut_heavy_average' => 'Power & Endurance',
            'Cut_heavy_big' => 'Strongman',

            // 🏃‍♂️ Enduro (Endurance & Agilité)
            'Enduro_light_little' => 'Calisthenics',
            'Enduro_light_average' => 'HIIT',
            'Enduro_light_big' => 'Athlétique',
            'Enduro_medium_little' => 'Endurance Training',
            'Enduro_medium_average' => 'Cardio & Conditioning',
            'Enduro_medium_big' => 'Power & Endurance',
            'Enduro_heavy_little' => 'CrossFit',
            'Enduro_heavy_average' => 'Functional Fitness',
            'Enduro_heavy_big' => 'Strongman',
        ];
    }
}