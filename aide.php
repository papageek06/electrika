<?php

// clone les ligne pour event et details

//  public function configureActions(Actions $actions): Actions
//     {
//         $cloneAction = Action::new('clone', 'Dupliquer', 'fa fa-clone')
//             ->linkToCrudAction('cloneEntity');

//         return $actions
//             ->add(Crud::PAGE_INDEX, $cloneAction)
//             ->add(Crud::PAGE_DETAIL, $cloneAction)
//             ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $action) {
//                 return $action->linkToCrudAction('updateEntity');
//             })
//             ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
//                 return $action->linkToCrudAction('updateEntity');
//             });
//     }

//     public function cloneEntity(AdminContext $context): RedirectResponse
//     {
//         $entity = $context->getEntity()->getInstance();
//         $clone = clone $entity;

//         // Modifiez les propriétés du clone si nécessaire

//         $this->entityManager->persist($clone);
//         $this->entityManager->flush();

//         $this->addFlash('success', 'Mission dupliquée avec succès!');

//         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
//         return $this->redirect($adminUrlGenerator->setController(self::class)
//             ->setAction('index')
//             ->generateUrl());
//     }
    
//      
    
    
    
    
//     -----------------------------------
    
//      filtre  
    
//      public function configureFilters(Filters $filters): Filters
//     {
//         return $filters
//             ->add('client')
//             ->add('user')
//             ->add('course')
//             ->add('student')
//             ->add('beginAt')
//             ->add('endAt')
//         ;
//     } 