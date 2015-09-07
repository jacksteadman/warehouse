<?php

function filter_password($msg, $pw) {
    return str_replace($pw, '', $msg);
}

function get_graphite_metric($metric_name) {
    $from_time = date('H:i_Ymd', strtotime('midnight'));

    $url = sprintf(GRAPHITE_URL, urlencode($from_time), urlencode($metric_name));    
    
    try {
        $data = file_get_contents($url);
        if (empty($data)) {
            throw new Exception('No data returned.');
        }

        list($header, $point_list) = explode('|', $data, 2);
        $points = array_filter(explode(',', $point_list), 'is_numeric');

        if (empty($points)) {
            return array('total' => 0, 'latest' => 0);
        }

        $total = array_sum($points);

        $avg_last5 = (count($points) >= 5) 
            ? array_sum(array_slice($points, -5)) / 5 
            : $total / count($points);

        $rv = array('total' => $total, 'latest' => $avg_last5);

        return $rv;
    } catch (Exception $e) {
        mlog('Exception fetching data from [' . $url . ']: ' . $e->getMessage());
        throw e;
    }
}