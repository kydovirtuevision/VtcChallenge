<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Note;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/notes")
 */
class NoteController extends AbstractController
{
    private function getUserFromRequest(Request $request, UserRepository $userRepository)
    {
        $token = null;
        $auth = $request->headers->get('Authorization');
        if ($auth && 0 === strpos($auth, 'Bearer ')) {
            $token = substr($auth, 7);
        }
        if (!$token) {
            $token = $request->headers->get('X-AUTH-TOKEN');
        }

        if (!$token) {
            return null;
        }

        return $userRepository->findOneByApiToken($token);
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function list(Request $request, NoteRepository $noteRepository, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $userRepository);
        if (!$user) {
            return $this->json(['error' => 'unauthorized'], 401);
        }

        // fallback to a simple DB query for listing to avoid possible ORM mapping edge-cases
        $conn = $em->getConnection();
        $rows = $conn->fetchAllAssociative('SELECT id, title, content, category, status FROM note WHERE owner_id = ?', [$user->getId()]);

        return $this->json($rows);
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $userRepository);
        if (!$user) {
            return $this->json(['error' => 'unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true) ?: [];
        $title = $data['title'] ?? '';
        $content = $data['content'] ?? '';
        $category = $data['category'] ?? null;
        $status = $data['status'] ?? 'new';

        $note = new Note();
        $note->setTitle($title)
             ->setContent($content)
             ->setCategory($category)
             ->setStatus($status)
             ->setOwner($user);

        $em->persist($note);
        $em->flush();

        return $this->json(['id' => $note->getId()], 201);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function getNote(int $id, Request $request, NoteRepository $noteRepository, UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $userRepository);
        if (!$user) {
            return $this->json(['error' => 'unauthorized'], 401);
        }

        $note = $noteRepository->find($id);
        if (!$note || $note->getOwner()->getId() !== $user->getId()) {
            return $this->json(['error' => 'not found'], 404);
        }

        return $this->json([
            'id' => $note->getId(),
            'title' => $note->getTitle(),
            'content' => $note->getContent(),
            'category' => $note->getCategory(),
            'status' => $note->getStatus(),
        ]);
    }

    /**
     * @Route("/{id}", methods={"PUT","PATCH"})
     */
    public function update(int $id, Request $request, EntityManagerInterface $em, NoteRepository $noteRepository, UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $userRepository);
        if (!$user) {
            return $this->json(['error' => 'unauthorized'], 401);
        }

        $note = $noteRepository->find($id);
        if (!$note || $note->getOwner()->getId() !== $user->getId()) {
            return $this->json(['error' => 'not found'], 404);
        }

        $data = json_decode($request->getContent(), true) ?: [];
        if (isset($data['title'])) $note->setTitle($data['title']);
        if (isset($data['content'])) $note->setContent($data['content']);
        if (array_key_exists('category', $data)) $note->setCategory($data['category']);
        if (isset($data['status'])) $note->setStatus($data['status']);

        $em->flush();

        return $this->json(['message' => 'updated']);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function delete(int $id, Request $request, EntityManagerInterface $em, NoteRepository $noteRepository, UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $userRepository);
        if (!$user) {
            return $this->json(['error' => 'unauthorized'], 401);
        }

        $note = $noteRepository->find($id);
        if (!$note || $note->getOwner()->getId() !== $user->getId()) {
            return $this->json(['error' => 'not found'], 404);
        }

        $em->remove($note);
        $em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
