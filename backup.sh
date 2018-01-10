#!/usr/bin/env bash

name=$(date +%Y%m%d-%H%M%S)

cd /var/www/harassmap/site/themes && sudo tar czf ~/backup/$name.tar.gz ./

aws s3 cp ~/backup/$name.tar.gz s3://harassmap-backup

rm -Rf ~/backup/$name.tar.gz
