<?php

namespace Modules\Core\Repositories\Interfaces;

interface LogViewerInterface
{
    public function getlogs(array $data);
    public function logDelete(array $data);
    public function insertlog(array $data);
}
