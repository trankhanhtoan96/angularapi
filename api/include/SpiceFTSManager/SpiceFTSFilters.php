<?php

namespace SpiceCRM\includes\SpiceFTSManager;

class SpiceFTSFilters
{
    static function buildFiltersFromAggregate($aggregatesFilter, $aggregateData)
    {

        // set to boolean if a key '#not#set#' is returned
        $filterNotExists = false;

        $aggregateFilterKeys = [];

        $queryType = 'terms';

        foreach ($aggregateData as $aggregatesFilterValue) {
            $filterData = json_decode(html_entity_decode(base64_decode($aggregatesFilterValue)), true);

            if ($filterData['key'] == '#not#set#') {
                $filterNotExists = true;
                continue;
            };

            $aggregateFilterKeys[] = $filterData['key'];
            if (isset($filterData['from']) || isset($filterData['to'])) {
                $queryType = 'range';
                $agg = [];
                if($filterData['to']) $agg['lt'] = $filterData['to'];
                if($filterData['from']) $agg['gte'] = $filterData['from'];
                $ranges[] = [
                    $aggregatesFilter => $agg
                ];
            }
        }

        // build the filter
        $filter = [];
        switch ($queryType) {
            case 'terms';
                $filter = [
                    'terms' => [
                        $aggregatesFilter . '.agg' => $aggregateFilterKeys
                    ]
                ];
                break;
            case 'range':
                if (count($ranges) > 1) {
                    $rangesArray = [];
                    foreach ($ranges as $range)
                        $rangesArray[] = ['range' => $range];
                    $filter = ['bool' => [
                        'should' => $rangesArray,
                        'minimum_should_match' => 1
                    ]];
                } else
                    $filter = ["range" => reset($ranges)];
                break;
        }

        // if we shoudl add a filter not exists clause .. add that
        if ($filterNotExists) {
            if (count($filter) > 0) {
                $filter = ['bool' => [
                    'should' => [
                        $filter,
                        ['bool' => [
                            'must_not' => [
                                'exists' => ['field' => $aggregatesFilter . '.agg']
                            ]
                        ]]
                    ]
                ]];
            } else {
                $filter = ['bool' => [
                    'must_not' => [
                        'exists' => ['field' => $aggregatesFilter . '.agg']
                    ]
                ]];
            }
        }

        return $filter;
    }
}
