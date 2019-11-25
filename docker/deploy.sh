#!/bin/bash

# ensure running bash
if ! [ -n "$BASH_VERSION" ];then
    echo "this is not bash, calling self with bash....";
    SCRIPT=$(readlink -f "$0")
    /bin/bash $SCRIPT
    exit;
fi

SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT") 
cd $SCRIPTPATH

# load the variables
source $SCRIPTPATH/../app/.env

# Need to do this for docker-compose to work.
export REGISTRY=$REGISTRY
export PROJECT_NAME=$PROJECT_NAME

CONTAINER_IMAGE="`echo $REGISTRY`/`echo $PROJECT_NAME`"


docker stop `docker ps -aq`
docker rm `docker ps -aq`


#docker run -d \
#  --restart=always \
#  -p 80:80 \
#  -p 443:443 \
#  --restart=always \
#  --name="$PROJECT_NAME" \
#  $CONTAINER_IMAGE

docker-compose --file docker-compose.yml up

