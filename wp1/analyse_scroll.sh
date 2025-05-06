#!/bin/bash

LOGFILE="user-activity.log"

NOW=$(date -u +"%Y-%m-%dT%H:%M:%S")
ONE_HOUR_AGO=$(date -u -d "-1 hour" +"%Y-%m-%dT%H:%M:%S")

awk -v start="$ONE_HOUR_AGO" -F, '
function to_epoch(ts) {
    gsub(/timestamp=|"/, "", ts)          
    sub(/\..*$/, "", ts)                 
    gsub(/T/, " ", ts)                   
    gsub(/\+.*$/, "", ts)                 
    return mktime(substr(ts, 1, 4)" "substr(ts, 6, 2)" "substr(ts, 9, 2)" "substr(ts, 12, 2)" "substr(ts, 15, 2)" "substr(ts, 18, 2))
}

BEGIN {
    start_epoch = to_epoch(start)
}

{
    timestamp = ""
    request_id = ""
    msg = ""
    duration = 0

    for (i = 1; i <= NF; i++) {
        if ($i ~ /timestamp=/) {
            timestamp = $i
        }
        if ($i ~ /request_id=/) {
            split($i, a, "="); request_id = a[2]
        }
        if ($i ~ /msg=/) {
            msg = substr($i, 5)
        }
        if ($i ~ /duration=/) {
            split($i, c, "="); duration = c[2]
        }
    }

    log_epoch = to_epoch(timestamp)

    if (log_epoch >= start_epoch) {
        click[request_id] += (msg ~ /click/) ? 1 : 0
        scroll[request_id] += (msg ~ /User scroll down to the 50% position/) ? 1 : 0
        stay[request_id] += (msg ~ /stayed on the page/) ? 1 : 0
        total_duration[request_id] += duration
    }
}

END {
    printf "%-36s %-10s %-10s %-10s\n", "Request ID", "Clicks", "Scrolls", "Duration"
    for (id in click) {
        printf "%-36s %-10d %-10d %-10d\n", id, click[id], scroll[id], total_duration[id]
    }
}
' "$LOGFILE"
