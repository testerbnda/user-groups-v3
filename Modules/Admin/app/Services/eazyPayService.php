<?php

namespace Modules\Admin\Services;

use Modules\Paymentgatyway\Repositories\Interfaces\EazyPayInterface;
use Exception;
use Modules\Core\Helpers\Logger;
use Modules\Core\Helpers\BasicHelper;
use Modules\Core\Enums\Flag;

class eazyPayService
{
    private $repository;
    /**
     * Constrcutor
     * @param ContentInterface $repository
     */
    public function __construct(EazyPayInterface $repository)
    {
        $this->repository = $repository;
    }

    public function eazyPayPayment($userid)
    {
        try {
            return $this->repository->eazyPay($userid);          
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }


}