<?php

namespace App\Service;

use App\Entity\Plan;
use Doctrine\ORM\EntityManagerInterface;

class ProgramSelectorService
{
    private EntityManagerInterface $entityManager;

    private const PROGRAM_MAPPING = [
        'Strong' => [
            'light' => [
                'little' => 'Full Body',
                'average' => 'Upper-Lower',
                'big' => 'Push-Pull',
            ],
            'medium' => [
                'little' => 'PPL',
                'average' => 'PPL',
                'big' => 'Strength Training',
            ],
            'heavy' => [
                'little' => 'Powerlifting',
                'average' => 'Powerlifting',
                'big' => 'Strongman',
            ],
        ],
        'Shred' => [
            'light' => [
                'little' => 'Full Body',
                'average' => 'Cardio & Conditioning',
                'big' => 'HIIT',
            ],
            'medium' => [
                'little' => 'PPL',
                'average' => 'Functional Fitness',
                'big' => 'CrossFit',
            ],
            'heavy' => [
                'little' => 'Athlétique',
                'average' => 'Endurance Training',
                'big' => 'Strongman',
            ],
        ],
        'Bulk' => [
            'light' => [
                'little' => 'Full Body',
                'average' => 'Push-Pull',
                'big' => 'PPL',
            ],
            'medium' => [
                'little' => 'PPL',
                'average' => 'Hypertrophy',
                'big' => 'Strength Training',
            ],
            'heavy' => [
                'little' => 'Powerlifting',
                'average' => 'Powerlifting',
                'big' => 'Strongman',
            ],
        ],
        'Fit' => [
            'light' => [
                'little' => 'Calisthenics',
                'average' => 'Full Body',
                'big' => 'Athlétique',
            ],
            'medium' => [
                'little' => 'Functional Fitness',
                'average' => 'CrossFit',
                'big' => 'PPL',
            ],
            'heavy' => [
                'little' => 'Power & Endurance',
                'average' => 'Cardio & Conditioning',
                'big' => 'Strongman',
            ],
        ],
        'Power' => [
            'light' => [
                'little' => 'Strength Training',
                'average' => 'Powerlifting',
                'big' => 'Strongman',
            ],
            'medium' => [
                'little' => 'Powerlifting',
                'average' => 'Powerlifting',
                'big' => 'Strongman',
            ],
            'heavy' => [
                'little' => 'Strongman',
                'average' => 'Strongman',
                'big' => 'Strongman',
            ],
        ],
        'Cut' => [
            'light' => [
                'little' => 'Full Body',
                'average' => 'HIIT',
                'big' => 'Cardio & Conditioning',
            ],
            'medium' => [
                'little' => 'Athlétique',
                'average' => 'CrossFit',
                'big' => 'Functional Fitness',
            ],
            'heavy' => [
                'little' => 'Endurance Training',
                'average' => 'Power & Endurance',
                'big' => 'Strongman',
            ],
        ],
        'Enduro' => [
            'light' => [
                'little' => 'Calisthenics',
                'average' => 'HIIT',
                'big' => 'Athlétique',
            ],
            'medium' => [
                'little' => 'Endurance Training',
                'average' => 'Cardio & Conditioning',
                'big' => 'Power & Endurance',
            ],
            'heavy' => [
                'little' => 'CrossFit',
                'average' => 'Functional Fitness',
                'big' => 'Strongman',
            ],
        ],
    ];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getProgram(string $goal, float $weight, float $height): ?Plan
    {
        $weightCategory = $this->categorizeWeight($weight);
        $heightCategory = $this->categorizeHeight($height);

        if (!isset(self::PROGRAM_MAPPING[$goal][$weightCategory][$heightCategory])) {
            return null;
        }

        $programName = self::PROGRAM_MAPPING[$goal][$weightCategory][$heightCategory];

        return $this->entityManager->getRepository(Plan::class)->findOneBy(['name' => $programName]);
    }

    private function categorizeWeight(float $weight): string
    {
        return match (true) {
            $weight <= 60 => 'light',
            $weight < 80 => 'medium',
            default => 'heavy',
        };
    }

    private function categorizeHeight(float $height): string
    {
        return match (true) {
            $height < 165 => 'little',
            $height <= 180 => 'average',
            default => 'big',
        };
    }
}
