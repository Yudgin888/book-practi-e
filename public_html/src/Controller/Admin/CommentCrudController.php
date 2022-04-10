<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('author')->setMaxLength(255)->setRequired(true),
            TextEditorField::new('text')->setRequired(true),
            TextField::new('email')->setMaxLength(255)->setRequired(true),
            DateTimeField::new('createdAt')->setRequired(true),
            TextField::new('photoFilename')->setMaxLength(255)->setRequired(false),
            AssociationField::new('conference'),
        ];
    }

}
