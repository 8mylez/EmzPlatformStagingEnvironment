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
 *                 /____/              
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
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Uuid\Uuid;
use Emz\StagingEnvironment\Services\Sync\ExcludeFoldersIterator;

class SyncService implements SyncServiceInterface
{
    /** @var string */
    private $projectDir;

    /** @var Filesystem */
    private $fileSystem;

    /** @var EntityRepositoryInterface */
    private $environmentRepository;

    /** @var EntityRepositoryInterface */
    private $environmentLogRepository;

    public function __construct(
        string $projectDir,
        Filesystem $fileSystem,
        EntityRepositoryInterface $environmentRepository,
        EntityRepositoryInterface $environmentLogRepository
    ){
        $this->projectDir = $projectDir;
        $this->fileSystem = $fileSystem;
        $this->environmentRepository = $environmentRepository;
        $this->environmentLogRepository = $environmentLogRepository;
    }

    /**
     * Copies all folders related to shopware 6 in the provided subfolder
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool
     */
    public function syncCore(string $environmentId, Context $context): bool
    {
        /** @var StagingEnvironmentEntity */
        $environment = $this->environmentRepository
            ->search(new Criteria([$environmentId]), $context)
            ->get($environmentId);

        if (!$environment instanceof StagingEnvironmentEntity) {
            throw new \InvalidArgumentException(sprintf('Staging Environment with id %s not found environmentId missing', $environmentId));
        }
        
        if (!$environment->getFolderName()) {
            throw new \InvalidArgumentException(sprintf('Staging Environment has no folder saved.'));
        }

        $config['folderName'] = str_replace('/', '', $environment->getFolderName());
        
        $foldersToCopy = [
            'bin',
            'custom',
            'src',
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

        foreach($foldersToCopy as $folderToCopy) {
            if($this->fileSystem->exists($this->projectDir.'/'.$folderToCopy)) {

                $directoryIterator = new \RecursiveDirectoryIterator(
                    $this->projectDir.'/'.$folderToCopy, 
                    \FilesystemIterator::SKIP_DOTS
                );

                $excludedFolders = [];
                if (!empty($environment->getExcludedFolders())) {
                    $excludedFolders = explode(',', $environment->getExcludedFolders());
                }
                
                $excludedFoldersExtended = array_map(function($excludeFolder) {
                    return $this->projectDir . '/' . trim($excludeFolder);
                }, $excludedFolders);

                $excludedFoldersIterator = new ExcludeFoldersIterator($directoryIterator, $excludedFoldersExtended);

                $iterator = new \RecursiveIteratorIterator(
                    $excludedFoldersIterator,
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                $this->fileSystem->mirror(
                    $this->projectDir.'/'.$folderToCopy,
                    $this->projectDir.'/'.$config['folderName'].'/'.$folderToCopy,
                    $iterator    
                );
            }
        }

        foreach($foldersToCreate as $folderToCreate) {
            if(!$this->fileSystem->exists($this->projectDir.'/'.$config['folderName'].'/'.$folderToCreate)) {
                $this->fileSystem->mkdir($this->projectDir.'/'.$config['folderName'].'/'.$folderToCreate);
            }
        }

        foreach($filesToCopy as $file) {
            if($this->fileSystem->exists($this->projectDir.'/'.$file)) {
                $this->fileSystem->copy($this->projectDir.'/'.$file, $this->projectDir.'/'.$config['folderName'].'/'.$file);
            }
        }

        $this->environmentLogRepository->create(
            [
                [
                    'id' => Uuid::randomHex(),
                    'environmentId' => $environmentId,
                    'state' => 'sync_success'
                ],
            ],
            $context
        );

        return true;
    }

    /**
     * Removes all files from staging environment
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool
     */
    public function clearFiles(string $environmentId, Context $context): bool
    {
        /** @var StagingEnvironmentEntity */
        $environment = $this->environmentRepository
            ->search(new Criteria([$environmentId]), $context)
            ->get($environmentId);

        if (!$environment instanceof StagingEnvironmentEntity) {
            throw new \InvalidArgumentException(sprintf('Staging Environment with id %s not found environmentId missing', $environmentId));
        }
        
        if (!$environment->getFolderName()) {
            throw new \InvalidArgumentException(sprintf('Staging Environment has no folder saved.'));
        }

        $config['folderName'] = str_replace('/', '', $environment->getFolderName());

        $this->fileSystem->remove($this->projectDir.'/'.$config['folderName']);

        $this->environmentLogRepository->create(
            [
                [
                    'id' => Uuid::randomHex(),
                    'environmentId' => $environmentId,
                    'state' => 'files_cleared'
                ],
            ],
            $context
        );

        return true;
    }
}