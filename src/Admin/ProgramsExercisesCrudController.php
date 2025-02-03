<?php

namespace App\Admin;

use App\Entity\ProgramsExercises;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProgramsExercisesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProgramsExercises::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('program'),
            AssociationField::new('exercise')

        ];
    }

}
