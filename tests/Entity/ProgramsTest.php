<?php

namespace App\Tests\Entity;

use App\Entity\Programs;
use App\Entity\ProgramsExercises;
use App\Entity\Plan;
use App\Entity\PlanProgramDay;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ProgramsTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $program = new Programs();

        $date = new \DateTimeImmutable();
        $program->setCreatedAt($date)
            ->setContent(['day1' => 'squat'])
            ->setThreadId('thread123')
            ->setRunId('run456');

        $this->assertSame($date, $program->getCreatedAt());
        $this->assertSame(['day1' => 'squat'], $program->getContent());
        $this->assertSame('thread123', $program->getThreadId());
        $this->assertSame('run456', $program->getRunId());

        $user = new User();
        $program->setUser($user);
        $this->assertSame($user, $program->getUser());
    }

    public function testProgramsExercisesRelation(): void
    {
        $program = new Programs();
        $exercise = new ProgramsExercises();

        $program->addProgramsExercise($exercise);
        $this->assertTrue($program->getProgramsExercises()->contains($exercise));
        $this->assertSame($program, $exercise->getProgram());

        $program->removeProgramsExercise($exercise);
        $this->assertFalse($program->getProgramsExercises()->contains($exercise));
        $this->assertNull($exercise->getProgram());
    }

    public function testPlansRelation(): void
    {
        $program = new Programs();
        $plan = new Plan();

        $program->addPlan($plan);
        $this->assertTrue($program->getPlans()->contains($plan));
        $this->assertTrue($plan->getProgram()->contains($program));

        $program->removePlan($plan);
        $this->assertFalse($program->getPlans()->contains($plan));
    }

    public function testPlanProgramDaysRelation(): void
    {
        $program = new Programs();
        $planDay = new PlanProgramDay();

        $program->addPlanProgramDay($planDay);
        $this->assertTrue($program->getPlanProgramDays()->contains($planDay));
        $this->assertSame($program, $planDay->getProgram());

        $program->removePlanProgramDay($planDay);
        $this->assertFalse($program->getPlanProgramDays()->contains($planDay));
        $this->assertNull($planDay->getProgram());
    }

    public function testContentFallbackToRawString(): void
    {
        $program = new Programs();

        // hack pour simuler un mauvais type
        $reflection = new \ReflectionClass($program);
        $property = $reflection->getProperty('content');
        $property->setValue($program, 'raw-data');

        $this->assertSame(['raw' => 'raw-data'], $program->getContent());
    }
}
