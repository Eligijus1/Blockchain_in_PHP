#!/usr/bin/env bash

echo "# IPv4 and IPv6 localhost aliases:" | sudo tee /etc/hosts
echo "127.0.0.1 vagrant.blockchain.in.php.test  vagrant  localhost" | sudo tee -a /etc/hosts
echo "::1       vagrant.blockchain.in.php.test  vagrant  localhost" | sudo tee -a /etc/hosts
echo "10.0.2.15 vagrant.blockchain.in.php.test  vagrant  localhost" | sudo tee -a /etc/hosts

sudo ex +"%s@DPkg@//DPkg" -cwq /etc/apt/apt.conf.d/70debconf
sudo dpkg-reconfigure debconf -f noninteractive -p critical

# Fixing languages:
sudo apt-get install -y language-pack-en-base
sudo LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php

# Update packages:
apt-get update

# Install nmap:
sudo apt-get install -y nmap

# Add DNS to /etc/resolv.conf
echo "nameserver 8.8.8.8" | sudo tee -a /etc/resolv.conf
echo "nameserver 8.8.4.4" | sudo tee -a /etc/resolv.conf

# Install composer:
cd /tmp/
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Install git:
sudo apt-get install -y git

# Install zip:
sudo apt-get install zip -y
sudo apt-get install unzip -y

# Install MySQL:
echo "mysql-server-5.5 mysql-server/root_password password root" | debconf-set-selections
echo "mysql-server-5.5 mysql-server/root_password_again password root" | debconf-set-selections
sudo apt-get -y install mysql-server-5.5

# Installing PHP 7.2 and some extra libraries:
sudo apt-get install -y php7.2
sudo apt-get install -y php7.2-xml 
sudo apt-get install -y php7.2-curl
sudo apt-get install -y php7.2-mbstring
sudo apt-get install -y php7.2-bcmath
sudo apt-get install -y php7.2-zip
sudo apt-get install -y php7.2-mysql
sudo apt-get install -y php7.2-gmp
