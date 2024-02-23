<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\Type\EmployeeType;
use App\Service\EmloyeeRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    public function __construct(private EmloyeeRepository $emloyeeRepository)
    {
    }

    #[Route('/')]
    public function index(): Response
    {
        return $this->render('home.html.twig', [
            'employees' => $this->emloyeeRepository->findAll(),
        ]);
    }
    #[Route('/delete/{id}', methods: ['POST'])]
    public function delete(string $id): Response
    {
        $result = $this->emloyeeRepository->delete($id);
        if ($result) {
            $this->addFlash(
                'success',
                'Employee deleted!'
            );
        }
        else {
            $this->addFlash(
                'danger',
                'Employee not found!'
            );
        }
        return $this->redirectToRoute('app_employee_index');
    }
    #[Route('/edit/{id}')]
    public function edit(Request $request, string $id): Response
    {
        $employee = $this->emloyeeRepository->find($id);
        if ($employee === null) {
            $this->addFlash(
                'danger',
                'Employee not found!'
            );
            return $this->redirectToRoute('app_employee_index');
        }
        $form = $this->createForm(EmployeeType::class, $employee);
        try {
            $form->handleRequest($request);
        } catch (Exception $e) {
            $this->addFlash(
                'danger',
                $e->getMessage()
            );
            return $this->render('edit.html.twig', ["form" => $form]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->emloyeeRepository->update($employee);
            $this->addFlash(
                'success',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('app_employee_index');
        }
        return $this->render('edit.html.twig', ["form" => $form]);
    }


    #[Route('/new')]
    public function new(Request $request): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        try {
            $form->handleRequest($request);
        } catch (Exception $e) {
            $this->addFlash(
                'danger',
                $e->getMessage()
            );
            return $this->render('create.html.twig', ["form" => $form]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->emloyeeRepository->create($employee);
            $this->addFlash(
                'success',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('app_employee_index');
        }
        return $this->render('create.html.twig', ["form" => $form]);
    }
}
