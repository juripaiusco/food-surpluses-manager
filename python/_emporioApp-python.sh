#!/bin/sh

SCRIPTPATH=$(dirname "$0")"/";

cd ${SCRIPTPATH};

. "_emporioApp-conf.sh"

$EXE_PY emporiosd.py

rm -fr ./excel/*
