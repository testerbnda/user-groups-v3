<?php

namespace Modules\Core\Services;

use Modules\Core\Repositories\Interfaces\LogViewerInterface;
use Exception;
use Modules\Core\Helpers\Logger;

class LogViewerService
{

    private $repository;

    /**
     * Constructor
     * @param ObjectMetaTagInterface $repository
     */
    public function __construct(LogViewerInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Truncate Meta Table
     */
    public function getlogs($data)
    {
        try {
            return $this->repository->getlogs($data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function logDelete($data)
    {
        try {
            return $this->repository->logDelete($data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

        public function insertlog($data)
    {
        try {
            return $this->repository->insertlog($data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    

}
