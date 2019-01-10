If you wish to develop against the container, deploy as follows:

```
REGISTRY="docker-registry.your-org.org:5000"
PROJECT_NAME="logger-frontend-dev"
CONTAINER_IMAGE="`echo $REGISTRY`/`echo $PROJECT_NAME`"
PORT=80

docker kill $PROJECT_NAME
docker rm $PROJECT_NAME

docker run -d \
  -p $PORT:80 \
  -v `pwd`/code:/var/www/logger_frontend \
  --name=$PROJECT_NAME \
  $CONTAINER_IMAGE
```