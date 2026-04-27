<?php

namespace App\Controller;

use App\Repository\FolderRepository;
use App\Repository\PriorityRepository;
use App\Repository\TaskRepository;
use App\Entity\Task;
use App\Enum\Status;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    /**
     * @param Request $request
     * @param FolderRepository $folderRepository
     * @param TaskRepository $taskRepository
     * @param PriorityRepository $priorityRepository
     * @return Response
     */
    public function index(
        Request $request,
        FolderRepository $folderRepository,
        TaskRepository $taskRepository,
        PriorityRepository $priorityRepository
    ): Response {

        $folders = $folderRepository->findAll();
        $folder = $this->getCurrentFolder($request, $folders, $folderRepository);

        $tasks = $folder
            ? $taskRepository->findByFolderOrdered($folder)
            : [];

        $tasks = $this->filterTasks($tasks, $request);

        return $this->render('dashboard/index.html.twig', [
            'tasks' => $tasks,
            'folders' => $folders,
            'priorities' => $priorityRepository->findAll(),
            'currentFolder' => $folder,
            ...$this->groupTasks($tasks),
        ]);
    }


    #[Route('/dashboard/{id}/pin', name: 'app_dashboard_pin')]
    /**
     * Summary of togglePin
     * @param Task $task
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function togglePin(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('pin' . $task->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('app_login');
        }

        $task->setIsPinned(!$task->isPinned());

        $em->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/task/{id}/status', name: 'app_task_status', methods: ['POST'])]
    /**
     * @param Task $task
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeStatus(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        // sécurité CSRF
        if (!$this->isCsrfTokenValid('status' . $task->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('app_login');
        }

        // récupérer valeur envoyée
        $statusValue = $request->request->get('status');

        // convertir string → enum
        $status = Status::tryFrom($statusValue);

        if ($status) {
            $task->setStatus($status);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard');
    }

    ///////////////////utils

    /**
     * Summary of getCurrentFolder
     * @param Request $request
     * @param array $folders
     * @param FolderRepository $repo
     */
    private function getCurrentFolder(Request $request, array $folders, FolderRepository $repo)
    {
        $folderId = $request->query->get('folder');
        //si le dossier est trouvé renvoi le dossier, sinon renvoi le dossier 0 ou null
        return $folderId
            ? $repo->find($folderId)
            : ($folders[0] ?? null);
    }


    /**
     * Summary of filterTasks
     * @param array $tasks
     * @param Request $request
     * @return array
     */
    private function filterTasks(array $tasks, Request $request): array
    {
        $status = $request->query->get('status');
        $priority = $request->query->get('priority');

        return array_filter($tasks, function ($task) use ($status, $priority) {
            // Filtre par statut
            if ($status && $task->getStatus()->value !== $status) {
                return false;
            }
            // Filtre par priorité
            if ($priority && $task->getPriorities()?->getId() != $priority) {
                return false;
            }

            return true;
        });
    }

    /**
     * Summary of groupTasks
     * @param array $tasks
     * @return array{archived: array, notPinned: array, pinned: array}
     */
    private function groupTasks(array $tasks): array
    {
        return [
            'pinned' => array_filter($tasks, function ($task) {
                return $task->isPinned();
            }),

            'notPinned' => array_filter($tasks, function ($task) {
                return !$task->isPinned() && $task->getStatus()->value !== 'archived';
            }),

            'archived' => array_filter($tasks, function ($task) {
                return $task->getStatus()->value === 'archived';
            }),
        ];
    }
}
