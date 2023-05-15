#!/bin/sh

SCRIPTPATH=$(dirname "$0")"/";

cd ${SCRIPTPATH};

. ${SCRIPTPATH}"_emporioApp-conf.sh"

$EXE_PY emporiosd.py

rm -fr ./excel/*
