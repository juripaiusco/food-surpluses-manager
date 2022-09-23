#!/bin/bash

SCRIPTPATH=$(dirname "$0")"/";

cd ${SCRIPTPATH};

#. ${SCRIPTPATH}"_emporioApp-conf.sh"

$EXE_PHP artisan cron:customer:renew
