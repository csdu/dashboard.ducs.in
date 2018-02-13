#!/usr/bin/env bash

# build
npm run build:clean
npm run build

# gzip content
gzip -rf assets/css
gzip -rf assets/js

# remove .gz from file names
for file in $(find assets/css -name "*.gz")
do
 mv "$file" "${file%.gz}"
done;
for file in $(find assets/js -name "*.gz")
do
 mv "$file" "${file%.gz}"
done;

# assets
aws s3 sync --delete assets/css/dash s3://cdn.ducs.in/css/dash --metadata-directive REPLACE --cache-control max-age=2592000 --content-encoding gzip --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers

aws s3 sync --delete assets/js/dash s3://cdn.ducs.in/js/dash --metadata-directive REPLACE --cache-control max-age=2592000 --content-encoding gzip --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers

# aws s3 sync --delete assets/images s3://cdn.ducs.in/images --metadata-directive REPLACE --cache-control max-age=2592000 --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers
