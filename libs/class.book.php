<?php
function create_time_range($start, $end, $by='30 mins') {

    $start_time = strtotime($start);
    $end_time   = strtotime($end);

    $current    = time();
    $add_time   = strtotime('+'.$by, $current);
    $diff       = $add_time-$current;

    $times = array();
    while ($start_time < $end_time) {
        $times[] = $start_time;
        $start_time += $diff;
    }
    $times[] = $start_time;
    return $times;
}

?>