<?php

namespace Modules\Admin\Services;

use Modules\Admin\Repositories\Interfaces\AdminInterface;
use Exception;
use Modules\Core\Helpers\Logger;
use Modules\Core\Helpers\BasicHelper;
use Modules\Core\Enums\Flag;

class AdminService
{
    private $adminRepo;
    /**
     * Constrcutor
     * @param ContentInterface $repository
     */
    public function __construct(AdminInterface $adminRepo)
    {
        $this->adminRepo = $adminRepo;
    }

    /**
     * Get nykaa army post's only views sum
     * @return Exception
     */
    public function storeTemplate($template_name,$template_code,$template_format,$template_type,$temp_level,$site_group,$site_id)
    {
        try {
         return $this->adminRepo->storeTemplate($template_name,$template_code,$template_format,$template_type,$temp_level,$site_group,$site_id);          
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function listTemplate()
    {
        try {
         return $this->adminRepo->listTemplate();          
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function firstornewtemplate($id)
    {
        try {
         return $this->adminRepo->firstornewtemplate($id);          
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function update_template($template,$data)
    {
        try {
         return $this->adminRepo->update_template($template,$data);          
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function ajaxgettemplist()
    {
        try {
         return $this->adminRepo->ajaxgettemplist();          
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function storeAgreement($buyer_name,$seller_name)
    {
        try {
         return $this->adminRepo->storeAgreement($buyer_name,$seller_name);          
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }

    public function storeEnach($request)
    {
        try {
         return $this->adminRepo->storeEnach($request);          
        } catch (Exception $ex) {
            Logger::error($ex);
            return $ex;
        }
    }
    
    public function getEmailTemplates()
    {
        try{
            return $this->adminRepo->getEmailTemplates();
        }catch(Exception $ex){
            Logger::error($ex);
            return $ex;
        }
    }
  
}
