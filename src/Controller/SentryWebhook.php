<?php

declare(strict_types=1);

namespace App\Controller;

use App\Feature\SentryWebhook\Service\SentryIssueSaveService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SentryWebhook extends AbstractController
{
    public function __construct(private readonly SentryIssueSaveService $service)
    {
    }

    #[Route('/sentry/web-hook', name: 'sentry')]
    public function sentry(Request $request): JsonResponse
    {
        $this->service->save(json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR));

        return new JsonResponse(['status' => 'ok']);
    }
}