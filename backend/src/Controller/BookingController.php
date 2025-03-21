<?php

namespace App\Controller;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookingController extends AbstractController
{
    #[Route("/booking", name: "app_booking")]
    public function index(): Response
    {
        return $this->render("booking/index.html.twig", [
            "controller_name" => "BookingController",
        ]);
    }
    #[Route("/booking/{companyId}/{startDate}", name: "get_booking_company")]
    public function getBookingByCompany(
        int $companyId,
        string $startDate,
        EntityManagerInterface $em
    ): Response {
        $params = [
            "companyId" => $companyId,
            "startDate" => $startDate,
        ];

        $bookings = $em->getRepository(Booking::class)->findBy($params);

        return $this->json($bookings);
    }
}
