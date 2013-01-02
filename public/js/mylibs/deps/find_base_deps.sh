#!/bin/bash

egrep -c -e "clone|cutDown|openDot|roundRect|roundTab" ../*.js | grep -v 0 > this

#edit this and run ":source process.vim"

