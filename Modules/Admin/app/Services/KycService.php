<?php

namespace Modules\Admin\Services;

use Modules\Kyc\Repositories\Interfaces\KycInterface;
use Exception;
use Modules\Core\Helpers\Logger;


class KycService
{
    private $repository;
    /**
     * Constrcutor
     * @param ContentInterface $repository
     */
    public function __construct(KycInterface $repository)
    {
        $this->repository = $repository;
    }

    public function pan_verify($data)
    {
        try {
            return $this->repository->pan_verify($data);          
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function bank_verify($data)
    {
        try {
            return $this->repository->bank_verify($data);          
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }


}