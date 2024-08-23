<?php

namespace Modules\Core\Repositories\Implementations;

use Modules\Core\Repositories\Interfaces\LogViewerInterface;
use File;
use Modules\Core\Entities\ApiLog;


class LogViewerRepository implements LogViewerInterface
{
   
     public function getLogFileDates()
    {
        $dates = [];
        $files = glob(storage_path('logs/laravel-*.log'));
        $files = array_reverse($files);
        foreach ($files as $path) {
            $fileName = basename($path);
            preg_match('/(?<=laravel-)(.*)(?=.log)/', $fileName, $dtMatch);
            $date = $dtMatch[0];
            array_push($dates, $date);
        }

        return $dates;
    }

    public function getlogs(array $data)
    {

        $availableDates = $this->getLogFileDates();

        if (count($availableDates) == 0) {
             return  $data = [
            'available_log_dates' => [],
            'date' => '',
            'filename' => '',
            'logs' => []
        ];
        }

        $configDate = isset($data['date'])?$data['date']:date('Y-m-d');
        $currentdatefileName = 'laravel-' . $configDate . '.log';
        if ($configDate == null || !file_exists(storage_path('logs/' . $currentdatefileName))) {
            $configDate = $availableDates[0];
        }

        if (!in_array($configDate, $availableDates)) {
            return  $data = [
            'available_log_dates' => [],
            'date' => '',
            'filename' => '',
            'logs' => []
        ];
        }


        $pattern = "/^\[(?<date>.*)\]\s(?<env>\w+)\.(?<type>\w+):(?<message>.*)/m";

        $fileName = 'laravel-' . $configDate . '.log';
        $content = file_get_contents(storage_path('logs/' . $fileName));
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER, 0);
       // return $matches;

        $logs = [];
        foreach ($matches as $match) {
            $logs[] = [
                'timestamp' => $match['date'],
                'env' => $match['env'],
                'type' => get_badges($match['type']),
                'message' => trim($match['message'])
            ];
        }

        preg_match('/(?<=laravel-)(.*)(?=.log)/', $fileName, $dtMatch);
        $date = $dtMatch[0];

        $data = [
            'available_log_dates' => $availableDates,
            'date' => $date,
            'filename' => $fileName,
            'logs' => array_reverse($logs)
        ];
        return $data;
    }


    public function logDelete(array $data)
    {
        /* if ($data['filename']) {
            $file = 'logs/' . $data['filename'];
            if (File::exists(storage_path($file))) {
                File::delete(storage_path($file));
                return true;
            } else {
                return false;
            }
        }
       if ($data['clear']) {
            if ($data['clear'] == true) {
                $files = glob(storage_path('logs/*.log'));

                array_map('unlink', array_filter($files));
            }
        }*/
    }


      public function insertlog(array $data)
    {
      ApiLog::create($data);
        \Log::info("Api Logger repo >> ".json_encode($data));
    }


}
