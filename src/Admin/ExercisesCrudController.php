<?php

namespace App\Admin;

use App\Entity\Exercises;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ExercisesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Exercises::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextEditorField::new('description'),
            IntegerField::new('rest_time'),
            TextField::new('difficulty'),
            TextField::new('category'),
            IntegerField::new('series'),
            IntegerField::new('calories'),
        ];
    }

}
