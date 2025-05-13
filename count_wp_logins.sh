#!/bin/bash

CONTAINER_ID="ff10f57c81ea"

CURRENT_TIME=$(date +%s)
THRESHOLD=$((CURRENT_TIME - 900))  

docker logs "$CONTAINER_ID" 2>&1 \
  | grep 'POST /wp-login.php' \
  | awk -v threshold="$THRESHOLD" '
    function parse_time(str) {
      gsub("/", " ", str)
      gsub(":", " ", str)
      split(str, parts, " ")
      return parts[1] " " parts[2] " " parts[3] " " parts[4] ":" parts[5] ":" parts[6] " +1200"
    }

    {
      match($0, /\[([0-9]{2}\/[A-Za-z]{3}\/[0-9]{4}:[0-9]{2}:[0-9]{2}:[0-9]{2})/, m)
      if (m[1] != "") {
        cmd = "date -d \"" parse_time(m[1]) "\" +%s"
        cmd | getline log_time
        close(cmd)

        if (log_time >= threshold) {
          ip = $1
          ip_count[ip]++
        }
      }
    }
    END {
      for (ip in ip_count)
        if (ip_count[ip] > 5)
          print ip, ip_count[ip]
    }
  '
