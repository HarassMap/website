#!/usr/bin/env bash

php artisan ide-helper:models --dir="plugins/harassmap/incidents/models" \
    --dir="plugins/harassmap/news/models" \
    --dir="plugins/harassmap/comments/models" \
    --dir="plugins/harassmap/mail/models" \
    --dir="plugins/harassmap/contact/models"