<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Enum\TaskStatus;
use App\Exception\ApiException;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class TaskController extends AbstractController
{
    public function index(TaskRepository $repo): JsonResponse
    {
        $tasks = $repo->findAll();

        $data = array_map(fn(Task $task) => $this->transformTask($task), $tasks);

        return $this->json($data);
    }

    public function show(Task $task): JsonResponse
    {
        if (is_null($task)) {
            return $this->json('123');
        }
        return $this->json($this->transformTask($task));
    }

    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $task = new Task();
        $task->setTitle($data['title'] ?? '');
        $task->setDescription($data['description'] ?? null);
        $task->setStatus(isset($data['status']) ? TaskStatus::from($data['status']) : TaskStatus::Active);

        $errors = $validator->validate($task);

        if (count($errors) > 0) {
            return $this->json($this->formatValidationErrors($errors), 400);
        }

        $em->persist($task);
        $em->flush();

        return $this->json($this->transformTask($task), 201);
    }

     public function update(
        int $id,
        Request $request,
        TaskRepository $repo,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $task = $repo->find($id);

        if (!$task) {
            throw new ApiException(404, "Task with id $id not found");
        }

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (isset($data['title'])) {
            $task->setTitle($data['title']);
        }

        if (array_key_exists('description', $data)) {
            $task->setDescription($data['description']);
        }

        if (isset($data['status'])) {
            $task->setStatus(TaskStatus::from($data['status']));
        }

        $errors = $validator->validate($task);
        if (count($errors) > 0) {
            return $this->json($this->formatValidationErrors($errors), 400);
        }

        $em->flush();

        return $this->json($this->transformTask($task));
    }

    public function delete(
        int $id,
        TaskRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $task = $repo->find($id);

        if (!$task) {
            throw new ApiException(404, "Task with id $id not found");
        }

        $em->remove($task);
        $em->flush();

        return $this->json([
            'message' => "Task with id $id deleted successfully"
        ], 200);
    }

    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $result = [];
        foreach ($errors as $error) {
            $result[] = [
                'field' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
            ];
        }
        return ['errors' => $result];
    }

    private function transformTask(Task $task): array
    {
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription() ?? "No description provided",
            'status' => $task->getStatus(),
        ];
    }
}
