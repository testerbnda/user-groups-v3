<?php

namespace Modules\Admin\Repositories\Interfaces;
use App\Models\User;
interface UserInterface
{

    /**
     * Get new instance
     */
    public function ajaxgetlist(); 

    public function save(User $user, array $data);
    public function update(User $user, array $data);
    public function firstornew($id);
 
    public function updatedetail(User $user, array $data); 
}
