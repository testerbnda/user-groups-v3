<?php

namespace Modules\Core\Helpers;

use Modules\Core\Enums\ContentType;
use Modules\Core\Helpers\Logger;
use Exception;

class EsHelper
{

    /**
     * Extract the given field from Elasticsearch results
     * @param string $field
     * @param array $response
     * @return array
     */
    public static function extractFieldFromHits(string $field = "all", array $results)
    {
        $data = array();
        if (!empty($results["hits"]["hits"])) {
            foreach ($results["hits"]["hits"] as $result) {
                if ($field != "all" && isset($result["_source"][$field])) {
                    array_push($data, $result["_source"][$field]);
                } else {
                    array_push($data, $result["_source"]);
                }
            }
        }
        return $data;
    }

    /**
     * Extract total hits from results
     * @param array $results
     * @return type
     */
    public static function extractTotalHits(array $results)
    {
        return !empty($results["hits"]["total"]) ? $results["hits"]["total"] : 0;
    }

}
