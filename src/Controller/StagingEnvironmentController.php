<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Controller;

use Symfony\Component\HttpFoundation\Request;
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
    public function create(Request $request): JsonResponse
    {
        $environmentName = $request->get('name');

        if ($this->syncService->syncCore($environmentName)) {
            return new JsonResponse([
                "status" => true,
                "message" => "Core successfully synced!"
            ]);
        }
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/sync_files", name="api.action.emz_pse.environment.sync_files", methods={"POST"})
     */
    public function syncFiles(Request $request): JsonResponse
    {
        $environmentName = $request->get('name');

        sleep(10);
        
        return new JsonResponse([
            "status" => true,
            "message" => "SYNC FILES complete!"
        ]);
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/clone_database", name="api.action.emz_pse.environment.clone_database", methods={"POST"})
     */
    public function cloneDatabase(Request $request): JsonResponse
    {
        $environmentName = $request->get('name');
        
        sleep(10);

        return new JsonResponse([
            "status" => true,
            "message" => "CLONE DATABASE complete!"
        ]);
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/update_settings", name="api.action.emz_pse.environment.update_settings", methods={"POST"})
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $environmentName = $request->get('name');

        sleep(10);

        return new JsonResponse([
            "status" => true,
            "message" => "UPDATE SETTINGS complete!"
        ]);
    }
}