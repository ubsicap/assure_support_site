#!/bin/bash
# Make sure this script is executable: chmod +x supervisor_event_logger.sh

while read line; do
    read headers
    read payload
    echo "$(date): Supervisor caught a process exit event!" >> /var/log/supervisord-alerts.log
    echo "$payload" >> /var/log/supervisord-alerts.log
done
