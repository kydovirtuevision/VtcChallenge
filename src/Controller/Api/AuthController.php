<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/api/auth")
 */
class AuthController extends AbstractController
{
    /**
     * @Route("/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request, EntityManagerInterface $em, UserRepository $userRepository, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: [];
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['error' => 'email and password required'], 400);
        }

        if ($userRepository->findOneByEmail($email)) {
            return $this->json(['error' => 'user already exists'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $hashed = $hasher->hashPassword($user, $password);
        $user->setPassword($hashed);
        $token = bin2hex(random_bytes(16));
        $user->setConfirmationToken($token);
        $em->persist($user);
        $em->flush();

        // persist email to var/emails as a simple simulation
        $dir = __DIR__ . '/../../../../var/emails';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $content = sprintf("To: %s\nSubject: Confirm your account\n\nVisit /api/auth/confirm/%s to confirm.\n", $email, $token);
        @file_put_contents($dir . '/' . $token . '.txt', $content);

        return $this->json(['message' => 'registered', 'confirmation_token' => $token]);
    }

    /**
     * @Route("/confirm/{token}", name="api_confirm", methods={"GET"})
     */
    public function confirm(string $token, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->findOneByConfirmationToken($token);
        if (!$user) {
            return $this->json(['error' => 'invalid token'], 404);
        }

        $user->setIsVerified(true);
        $user->setConfirmationToken(null);
        $em->flush();

        return $this->json(['message' => 'account confirmed']);
    }

    /**
     * @Route("/login", name="api_login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: [];
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['error' => 'email and password required'], 400);
        }

        $user = $userRepository->findOneByEmail($email);
        if (!$user) {
            return $this->json(['error' => 'invalid credentials'], 401);
        }

        if (!$user->isVerified()) {
            return $this->json(['error' => 'account not confirmed'], 403);
        }

        if (!$hasher->isPasswordValid($user, $password)) {
            return $this->json(['error' => 'invalid credentials'], 401);
        }

        // generate api token and persist
        $apiToken = bin2hex(random_bytes(32));
        $user->setApiToken($apiToken);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->json(['message' => 'login successful', 'api_token' => $apiToken]);
    }
}
