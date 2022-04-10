<?php

namespace App\Controller\Admin;

use App\Entity\Conference;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConferenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Conference::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('city')->setMaxLength(255)->setRequired(true),
            TextField::new('year')->setMaxLength(4)->setRequired(true),
            ChoiceField::new('is_international')->setChoices([
                'Yes' => 1,
                'No' => 0,
            ])->renderExpanded()->setRequired(true),
            TextField::new('slug')->setMaxLength(255)->setRequired(true),
        ];
    }
}
