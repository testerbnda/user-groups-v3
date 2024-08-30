<?php
namespace Modules\Admin\Repositories\Interfaces;
use Modules\Admin\Entities\Bucket;

interface RecievableInterface {
    //
    public function index();
    public function ajaxgetrecievables();
    public function createBucket(Bucket $bucket);
    public function edit(string $id);
    public function getBalance(string $id);
    public function transferFunds($data, $id);
}
