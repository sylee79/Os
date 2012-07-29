<?php
    $vw_bm_events = array();
    function mark($message) {
        global $vw_bm_events;
        $microtime = microtime();
        $comps = explode(' ', $microtime);
        $ts = sprintf('%d%03d', $comps[1], $comps[0] * 1000);
        $vw_bm_events[] = array($ts, $message);
    }

    function lst() {
        global $vw_bm_events;
        echo "<br/><br/><h1>Benchmark Results</h1>";
        $lastTs = 0;
        $totalTs = 0;
        foreach ($vw_bm_events as $info) {
            $ts = $info[0];
            $msg = $info[1];
            $t = 0;
            if ($lastTs != 0) {
                $t = $ts - $lastTs;
            }
            $lastTs = $ts;
            $totalTs += $t;
            echo "<b>" . $msg . "</b> took ${t}ms" . "<br/>";
        }
        echo "<br/>Total: ${totalTs}ms<br/>";
        echo "<br/><b>END<br/><br/>";
    }