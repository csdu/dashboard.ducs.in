#!/usr/bin/env bash

# helper script to deploy a file on ec2 instance
# Usage: ./scripts/ec2.sh [PATH_OF_FILE_OR_DIR_RELATIVE_TO_PROJECT_DIR]


source scripts/credentials.sh
deploy() {
    f=$1
    rsync -ruv --progress -e "ssh -i $ec2key" $f $ec2user@$ec2host:/var/www/html/$f
}

deploy $1;
