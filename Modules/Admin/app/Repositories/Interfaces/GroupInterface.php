<?php
namespace Modules\Admin\Repositories\Interfaces;
use Modules\Admin\Entities\Group;
interface GroupInterface {
    public function createGroup(Group $data);
    public function search(string $query);
    public function ajaxgetlist();
    public function firstornew($id);
    public function update(Group $group, array $data);
    public function findFirst(string $id);
    public function deleteGroup(string $id);
}