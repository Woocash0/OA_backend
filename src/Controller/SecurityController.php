<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class SecurityController extends AbstractController
{
    private UserRepository $userRepository;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(UserRepository $userRepository, JWTTokenManagerInterface $jwtManager)
    {
        $this->userRepository = $userRepository;
        $this->jwtManager = $jwtManager;
    }

    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON format'], 400); // 400 Bad Request
        }

        $email = $requestData['email'] ?? null;
        $password = $requestData['password'] ?? null;
        
        //Test
        ob_start();
        var_dump($email);
        var_dump($password);
        $dump = ob_get_clean();
        $filePath = 'C:\Users\lukas\Desktop/var_dump_output.txt';
        file_put_contents($filePath, $dump);

        

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Missing email or password'], 400); // 400 Bad Request
        }

        // Znajdź użytkownika na podstawie adresu e-mail
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404); // 404 Not Found
        }

        // Sprawdź hasło (używając UserPasswordHasherInterface)
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid password'], 401);
    }

        // Wygeneruj token JWT
        try {
            $jwtToken = $this->jwtManager->create($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error generating token'], 500);
        }

        $userDetails = $user->getIdUserDetails();
        $name = $userDetails ? $userDetails->getName() : '';
        $surname = $userDetails ? $userDetails->getSurname() : '';

        

        return new JsonResponse([
            'message' => 'Login successful',
            'token' => $jwtToken,
           'user' => [
                'id' => $user->getId(),
                'email' => $user->getUserIdentifier(),
                'name' => $name,
                'surname' => $surname,
            ],
        ], 200);
        
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return new JsonResponse(['message' => 'Logout successful']);
    }
}
