#!/usr/bin/env bash

BACKEND_NAME=$1
WP_CORE_DIR=${WP_CORE_DIR-/tmp/wordpress/}

download() {
    if [ `which curl` ]; then
        curl -s "$1" > "$2";
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1"
    fi
}

if [ ! -d $WP_CORE_DIR/wp-content ]; then
    mkdir -p $WP_CORE_DIR/wp-content
fi

rm -f $WP_CORE_DIR/wp-content/object-cache.php

if [ "$BACKEND_NAME" = 'redis' ]; then
    download https://raw.githubusercontent.com/pantheon-systems/wp-redis/master/object-cache.php $WP_CORE_DIR/wp-content/object-cache.php
elif [ "$BACKEND_NAME" = 'memcached' ]; then
    download https://raw.githubusercontent.com/Ipstenu/memcached-redux/master/object-cache.php $WP_CORE_DIR/wp-content/object-cache.php
fi
