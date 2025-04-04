<?php

namespace App\Controller;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/booking')]
final class BookingController extends AbstractController
{
    #[Route("/new", name: "app_booking_new", methods: ["POST"])]
    public function getNewBooking(Request $request, EntityManagerInterface $em): void
    {

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
