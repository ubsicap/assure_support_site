#!/bin/bash

(crontab -l 2>/dev/null; echo "*/5 * * * * /etc/heartbeat.sh") | crontab -
