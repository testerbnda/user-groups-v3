<?php

namespace Modules\Admin\Services;
use Modules\Admin\Repositories\Interfaces\RecievableInterface;
use Exception;
use Modules\Core\Helpers\Logger;

class RecievableService {
    private $recievableRepo;
    public function __construct(RecievableInterface $recievableRepo) {   
        try {
            $this -> recievableRepo = $recievableRepo;
        } catch (\Exception $e) {
            Logger::error('Failed to initialize: ' . $e->getMessage());
            throw $e;
        }
    }

    public function ajaxgetrecievables() {
        try {
            return $this->recievableRepo->ajaxgetrecievables();
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function index() {
        try {
            return $this -> recievableRepo -> index();
        } catch(Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }
}