<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Sync;

use Emz\StagingEnvironment\Services\Sync\SyncServiceInterface;
use Symfony\Component\Filesystem\Filesystem;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;

class SyncService implements SyncServiceInterface
{
    /** @var string */
    private $projectDir;

    /** @var Filesystem */
    private $fileSystem;

    /** @var EntityRepositoryInterface */
    private $profileRepository;

    public function __construct(
        string $projectDir,
        Filesystem $fileSystem,
        EntityRepositoryInterface $profileRepository
    ){
        $this->projectDir = $projectDir;
        $this->fileSystem = $fileSystem;
        $this->profileRepository = $profileRepository;
    }

    public function syncCore($selectedProfileId): bool
    {
        $config = [
            'folderName' => 'emzstaging',
        ];

        $selectedProfile = $this->profileRepository->search(
            new Criteria([$selectedProfileId]), Context::createDefaultContext()
        )->get($selectedProfileId);

        if ($selectedProfile) {
            $config['folderName'] = str_replace('/', '', $selectedProfile->get('folderName'));
        }        

        $foldersToCopy = [
            'bin',
            'custom',
            'src',
            'var',
            'files',
            'config',
            'public',
            'vendor'
        ];

        $foldersToCreate = [
            'var',
            'var/cache',
            'var/log'
        ];

        $filesToCopy = [
            'index.html',
            'install.lock',
            'license.txt',
            'Dockerfile',
            'phpunit.xml.dist',
            'PLATFORM_COMMIT_SHA',
            'composer.json',
            'README.md',
            'composer.lock',
            'var/cache/plugins.json'
        ];

        if (empty($config['folderName'])) {
            return false;
        }

        foreach($foldersToCopy as $folderToCopy) {
            if($this->fileSystem->exists($this->projectDir.'/'.$folderToCopy)) {
                $this->fileSystem->mirror($this->projectDir.'/'.$folderToCopy, $this->projectDir.'/'.$config['folderName'].'/'.$folderToCopy);
            }
        }

        foreach($foldersToCreate as $folderToCreate) {
            if(!$this->fileSystem->exists($this->projectDir.'/'.$config['folderName'].'/'.$folderToCreate)) {
                $this->fileSystem->mkdir($this->projectDir.'/'.$config['folderName'].'/'.$folderToCreate);
            }
        }

        foreach($filesToCopy as $file) {
            if($this->fileSystem->exists($file)) {
                $this->fileSystem->copy($this->projectDir.'/'.$file, $this->projectDir.'/'.$config['folderName'].'/'.$file);
            }
        }

        return true;
    }
}