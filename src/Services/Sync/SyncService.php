<?php 

/**
 * Copyright (c) 8mylez GmbH. All rights reserved.
 * This file is part of software that is released under a proprietary license.
 * You must not copy, modify, distribute, make publicly available, or execute
 * its contents or parts thereof without express permission by the copyright
 * holder, unless otherwise permitted by law.
 * 
 *    ( __ )____ ___  __  __/ /__  ____
 *   / __  / __ `__ \/ / / / / _ \/_  /
 *  / /_/ / / / / / / /_/ / /  __/ / /_
 *  \____/_/ /_/ /_/\__, /_/\___/ /___/
 *              /____/              
 * 
 * Quote: 
 * "Any fool can write code that a computer can understand. 
 * Good programmers write code that humans can understand." 
 * – Martin Fowler
 */

declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Sync;

use Emz\StagingEnvironment\Services\Sync\SyncServiceInterface;
use Symfony\Component\Filesystem\Filesystem;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentProfileEntity;

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

    /**
     * Copies all folders related to shopware 6 in the provided subfolder
     * 
     * @param string $folderName
     * 
     * @return bool
     */
    public function syncCore(string $folderName): bool
    {
        $config = [
            'folderName' => 'emzstaging',
        ];
        
        $config['folderName'] = str_replace('/', '', $folderName);
        
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