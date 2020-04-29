<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Sync;

use Emz\StagingEnvironment\Services\Sync\SyncServiceInterface;
use Symfony\Component\Filesystem\Filesystem;

class SyncService implements SyncServiceInterface
{
    /** @var string */
    private $projectDir;

    /** @var Filesystem */
    private $fileSystem;

    public function __construct(
        string $projectDir,
        Filesystem $fileSystem
    ){
        $this->projectDir = $projectDir;
        $this->fileSystem = $fileSystem;
    }

    public function syncCore($folderName = 'emzstaging'): bool
    {
        //should be coming from the profile configuration
        $config = [
            'folderName' => $folderName,
        ];

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
            'index.htm',
            'license.txt',
            'Dockerfile',
            'phpunit.xml.dist',
            'PLATFORM_COMMIT_SHA',
            'composer.json',
            'README.md',
            'composer.lock',
            'var/cache/plugins.json'
        ];

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