#!/bin/bash

vendor/bin/propel-gen
sqlite3 demo.db < build/sql/schema.sql
