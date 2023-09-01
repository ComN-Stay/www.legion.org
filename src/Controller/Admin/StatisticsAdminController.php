<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\StatisticsRepository;

class StatisticsAdminController extends AbstractController
{
    #[Route('/admin/statistics', name: 'app_statistics_admin', methods: ['GET', 'POST'])]
    public function index(
        StatisticsRepository $statisticsRepository, 
        Request $request
    )
    {
        if($request->request->get('start') === null) {
            $start = new \DateTime(date('Y-m-d', strtotime('-1 month')));
            $startDate = $start->format('Y-m-d');
            $response = 'response';
        } else {
            $start = new \DateTime($request->request->get('start'));
            $startDate = $start->format('Y-m-d');
            $response = 'jsonResponse';
        }
        if($request->request->get('end') === null) {
            $end = new \DateTime(date('Y-m-d', strtotime('+1 day')));
            $endDate = $end->format('Y-m-d');
            $endAff = new \DateTime(date('Y-m-d'));
        } else {
            $end = new \DateTime($request->request->get('end'));
            $endDate = $end->format('Y-m-d');
            $endAff = new \DateTime($request->request->get('end'));
        }
        $stats = $statisticsRepository->findVisitsBetween($startDate, $endDate);
        $datas = [];
        $labels = [];
        foreach($stats as $stat) {
            array_push($datas, $stat->getVisits());
            array_push($labels, $stat->getDay()->format('d/m/Y'));
        }
        
        if($response == 'response') {
            return $this->render('admin/statistics_admin/index.html.twig', [
                'startDate' => $startDate,
                'endDate' => $endAff,
                'datas' => $datas,
                'labels' => $labels,
            ]);
        } else {
            $res['result'] = 'success';
            $res['startDate'] = $startDate;
            $res['endDate'] = $endAff;
            $res['datas'] = $datas;
            $res['labels'] = $labels;
            
            return new JsonResponse(json_encode($res));
        }
    }

}
