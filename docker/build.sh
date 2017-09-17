#!/usr/bin/env bash

cd php
docker build -t asahitar0u/zugzug_php:dev .
docker push asahitar0u/zugzug_php:dev

cd ../nginx/
docker build -t asahitar0u/zugzug_nginx:dev .
docker push asahitar0u/zugzug_nginx:dev

cd ../postgres/
docker build -t asahitar0u/zugzug_postgres:dev .
docker push asahitar0u/zugzug_postgres:dev

cd ../redis/
docker build -t asahitar0u/zugzug_redis:dev .
docker push asahitar0u/zugzug_redis:dev

