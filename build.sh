#!/bin/bash

echo "Installing build tools..."

apt-get update
apt-get install -y build-essential

echo "Compiling Swiss Ephemeris..."

cd swisseph
make swetest