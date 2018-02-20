#!/usr/bin/env bash

# directories
d_css=assets/css
d_js=assets/js

# flags for aws s3
f_permissions="--grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers"
f_encoding="--content-encoding gzip"
f_cache="--metadata-directive REPLACE --cache-control max-age=2592000"

# s3 buckets
b_cdn=s3://cdn.ducs.in

# build
npm run build:clean
echo "{}" > src/templates/assets.json # reset assets json
npm run build

# get updated files list in scripts/s3/tmp.txt
tmpFile=scripts/s3/tmp.txt
uploadedList=scripts/s3/uploaded.txt

./scripts/s3/_get_files_changed.sh

num=$(wc -l < $tmpFile)
echo "files changed: $num"
printf "" > $uploadedList

if [ $num -gt 0 ]; then
  ######### prepare files for upload
  echo "gzipping files..."

  # gzip all, except images
  find $d_css $d_js -type f ! -name "*.gz" | xargs gzip -kf

  # move all .gz files to upload dir
  #   while removing .gz suffix from file name
  for f in $(find $d_css $d_js -type f -name "*.gz"); do
    d="$(dirname $f)"
    b=${f#}
    mkdir -p assets/upload/"${d#}"
    c="${b%.gz}"
    mv $f assets/upload/$c
  done

  ######### upload only changed files
  echo "Connecting to AWS..."
  while read f; do
    if [[ $f == css/* ]]; then
        aws s3 cp upload/assets/$f $b_cdn/$f $f_cache $f_encoding $f_permissions
    elif [[ $f == js/* ]]; then
        aws s3 cp upload/assets/$f $b_cdn/$f $f_cache $f_encoding $f_permissions
    fi
    echo $f >> $uploadedList
  done < $tmpFile
else
  echo "No files changed."
fi
