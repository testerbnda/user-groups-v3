<?php

namespace Modules\Admin\Repositories\Interfaces;

interface AdminInterface
{

    /**
     * Get new instance
     */
    public function storeTemplate($template_name,$template_code,$template_format,$template_type,$temp_level,$site_group,$site_id);
    public function storeAgreement($buyer_name,$seller_name);
    public function storeEnach($request);
    public function listTemplate();
    public function ajaxgettemplist();
    public function firstornewtemplate($id);
    public function update_template($template, $data);
    public function getEmailTemplates();
}
