<?php

namespace App\Controller;

use App\Repository\FolderRepository;
use App\Repository\TaskRepository;
use App\Repository\PriorityRepository;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
    FolderRepository $folderRepository, 
    TaskRepository $taskRepository,
    PriorityRepository $priorityRepository): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'folders' => $folderRepository->findAll(),
            'priorities' => $priorityRepository->findAll(),
            'tasks' => $taskRepository->findAll(),
        ]);
    }
}
