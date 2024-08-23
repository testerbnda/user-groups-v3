<?php

namespace Modules\Admin\Services;

use Modules\Admin\Repositories\Interfaces\UserInterface;
use Exception;
use App\Models\User;
use Modules\Core\Helpers\Logger;
use Modules\Core\Enums\Flag;

class UserService
{

    private $repository;

    public function __construct(UserInterface $repository)
    {
        
        $this->repository = $repository;
    }

     public function ajaxgetlist()
    {
        try {
            return $this->repository->ajaxgetlist();
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }
   
      public function firstornew($id = null)
    {
        try {
         return $this->repository->firstornew($id);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

      public function save($user,$data)
    {
        try {
         return $this->repository->save($user,$data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function update($user,$data)
    {
        try {
         return $this->repository->update($user,$data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function updatedetail($user,$data)
    {
        try {
         return $this->repository->updatedetail($user,$data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

       

    public function user_docs($id,$data)
    {
        try {
         return $this->repository->user_docs($id,$data);
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }
 
} //end
