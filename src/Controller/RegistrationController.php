<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserDetails;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
    
        $user = new User();
        $userDetails = new UserDetails();
        $user->setEmail($requestData['email']); 
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $requestData['password']
            )
        ); 

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['success' => false, 'errors' => $errorMessages]);
        }
    
        $userDetails->setName($requestData['name']);
        $userDetails->setSurname($requestData['surname']);
        
        $user->setIdUserDetails($userDetails);
    
        $entityManager->persist($user);
        $entityManager->flush();
    
        return new JsonResponse(['success' => true]);
    }
    
}
