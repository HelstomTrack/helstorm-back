<?php

namespace App\Service;

use App\Entity\Programs;
use Doctrine\ORM\EntityManagerInterface;

class ProgramSelectorService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getProgram(string $goal, float $weight, float $height): ?Programs
    {
        $weightCategory = $this->categorizeWeight($weight);
        $heightCategory = $this->categorizeHeight($height);

        $morphoType = "{$goal}_{$weightCategory}_{$heightCategory}";

        $programName = $this->getProgramMapping()[$morphoType] ?? null;

        if (!$programName) {
            return null;
        }

        return $this->entityManager->getRepository(Programs::class)->findOneBy(['name' => $programName]);
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
            // Strong (Prise de Masse + Force)
            'Strong_light_little' => 'Full Body',
            'Strong_light_average' => 'Full Body',
            'Strong_light_big' => 'Split Musculaire',
            'Strong_medium_little' => 'Split Musculaire',
            'Strong_medium_average' => 'Gain de Masse',
            'Strong_medium_big' => 'Gain de Masse',
            'Strong_heavy_little' => 'Powerlifting',
            'Strong_heavy_average' => 'Powerlifting',
            'Strong_heavy_big' => 'Strongman',

            // Shred (Définition et Perte de Poids)
            'Shred_light_little' => 'Sèche & Définition',
            'Shred_light_average' => 'Sèche & Définition',
            'Shred_light_big' => 'Cardio & Force',
            'Shred_medium_little' => 'Cardio & Force',
            'Shred_medium_average' => 'CrossFit',
            'Shred_medium_big' => 'CrossFit',
            'Shred_heavy_little' => 'Athlétique',
            'Shred_heavy_average' => 'Athlétique',
            'Shred_heavy_big' => 'Endurance & Force',

            // Bulk (Hypertrophie musculaire)
            'Bulk_light_little' => 'Full Body',
            'Bulk_light_average' => 'Split Musculaire',
            'Bulk_light_big' => 'Gain de Masse',
            'Bulk_medium_little' => 'Gain de Masse',
            'Bulk_medium_average' => 'Gain de Masse',
            'Bulk_medium_big' => 'Powerlifting',
            'Bulk_heavy_little' => 'Powerlifting',
            'Bulk_heavy_average' => 'Powerlifting',
            'Bulk_heavy_big' => 'Strongman',

            // Fit (Équilibré, Fonctionnel)
            'Fit_light_little' => 'Calisthénie',
            'Fit_light_average' => 'Calisthénie',
            'Fit_light_big' => 'Athlétique',
            'Fit_medium_little' => 'Athlétique',
            'Fit_medium_average' => 'CrossFit',
            'Fit_medium_big' => 'CrossFit',
            'Fit_heavy_little' => 'Endurance & Force',
            'Fit_heavy_average' => 'Endurance & Force',
            'Fit_heavy_big' => 'Strongman',

            // Power (Force Maximale)
            'Power_light_little' => 'Powerlifting',
            'Power_light_average' => 'Powerlifting',
            'Power_light_big' => 'Strongman',
            'Power_medium_little' => 'Powerlifting',
            'Power_medium_average' => 'Powerlifting',
            'Power_medium_big' => 'Strongman',
            'Power_heavy_little' => 'Strongman',
            'Power_heavy_average' => 'Strongman',
            'Power_heavy_big' => 'Strongman',

            // Cut (Perte de poids, tonification)
            'Cut_light_little' => 'Sèche & Définition',
            'Cut_light_average' => 'Sèche & Définition',
            'Cut_light_big' => 'Cardio & Force',
            'Cut_medium_little' => 'Cardio & Force',
            'Cut_medium_average' => 'CrossFit',
            'Cut_medium_big' => 'CrossFit',
            'Cut_heavy_little' => 'Athlétique',
            'Cut_heavy_average' => 'Athlétique',
            'Cut_heavy_big' => 'Endurance & Force',

            // Enduro (Endurance & Agilité)
            'Enduro_light_little' => 'Calisthénie',
            'Enduro_light_average' => 'Calisthénie',
            'Enduro_light_big' => 'Athlétique',
            'Enduro_medium_little' => 'Athlétique',
            'Enduro_medium_average' => 'Endurance & Force',
            'Enduro_medium_big' => 'Endurance & Force',
            'Enduro_heavy_little' => 'CrossFit',
            'Enduro_heavy_average' => 'CrossFit',
            'Enduro_heavy_big' => 'CrossFit',
        ];
    }
}