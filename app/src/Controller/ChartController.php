<?php

namespace App\Controller;

use App\Service\EmloyeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartController extends AbstractController
{

    public function __construct(private EmloyeeRepository $emloyeeRepository)
    {
    }
    #[Route('/chart/age')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        $labels = [];
        $data = [];
        foreach ($this->emloyeeRepository->findAll() as $employee) {
            $labels[] = $employee->getName();
            $data[] = $employee->getBirthDate()->diff(new \DateTimeImmutable())->y;
        }
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Age',
                    'backgroundColor' => '#cfe2ff',
                    'borderColor' => '#cfe2ff',
                    'data' => $data,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);
        return $this->render('chart/index.html.twig', [
            'chart' => $chart,
        ]);
    }
}
