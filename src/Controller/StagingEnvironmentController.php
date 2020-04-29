<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Emz\StagingEnvironment\Services\Sync\SyncServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @RouteScope(scopes={"api"}) 
 */
class StagingEnvironmentController extends AbstractController
{
    /**
     * @var SyncServiceInterface
     */
    private $syncService;

    public function __construct(
        SyncServiceInterface $syncService
    )
    {
        $this->syncService = $syncService;
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/create", name="api.action.emz_pse.environment.create", methods={"POST"})
     */
    public function create(): JsonResponse
    {
        if ($this->syncService->syncCore('stagingfromcontroller')) {
            return new JsonResponse([
                "status" => true,
                "message" => "Core successfully synced!"
            ]);
        }
    }
}