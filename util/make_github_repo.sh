#!/bin/bash
#make a new github repo from CLI

curl -i -u bonestorm -d '{"name" : "dbag", "auto_init": true}' https://api.github.com/user/repos
