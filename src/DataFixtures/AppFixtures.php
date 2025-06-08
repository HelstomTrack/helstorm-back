<?php

namespace App\DataFixtures;

use App\Entity\Plan;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $planNames = [
            'Prise de masse',
            'Sèche',
            'Endurance',
            'Hypertrophie',
            'Force',
            'Perte de poids',
            'Cardio intense',
            'CrossFit débutant',
            'CrossFit avancé',
            'HIIT brûle-graisse',
            'Fitness général',
            'Préparation marathon',
            'Posture et mobilité',
            'Renfo musculaire maison',
            'Yoga dynamisant',
            'Gainage extrême',
            'Programme full-body',
            'Split 5 jours',
            'Upper/Lower',
            'Push/Pull/Legs'
        ];
        foreach ($planNames as $planName) {
            $plan = new Plan();
            $plan->setName($planName);
            $manager->persist($plan);
        }
        $manager->flush();
    }
}
