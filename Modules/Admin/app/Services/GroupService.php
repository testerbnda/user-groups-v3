<?php

namespace Modules\Admin\Services;
use Modules\Admin\Repositories\Interfaces\GroupInterface;
use Exception;
use Modules\Core\Helpers\Logger;

class GroupService {
    private $groupRepo;

    public function __construct(GroupInterface $groupRepo) {
        try {
            $this -> groupRepo = $groupRepo;
        } catch (\Exception $e) {
            Logger::error('Failed to initialize: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createGroup($data) {
        try {
            return $this -> groupRepo -> createGroup($data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function searchUser($query) {
        try {
            return $this -> groupRepo -> search($query);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function ajaxgetlist() {
        try {
            return $this->groupRepo->ajaxgetlist();
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function firstornew($id = null) {
        try {
            return $this->groupRepo->firstornew($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function findFirst($id = null) {
        try {
            return $this->groupRepo->findFirst($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function update($groups, $data) {
        try {
            return $this->groupRepo->update($groups, $data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function deleteGroup($id) {
        try {
            return $this -> groupRepo -> deleteGroup($id);
        } catch(Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function ajaxgetusers($id) {
        try {
            return $this->groupRepo->ajaxgetusers($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }
}

