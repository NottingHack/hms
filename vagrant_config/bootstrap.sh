#!/bin/bash

apt-get update
apt-get install -y apache2 php5-mysql libapache2-mod-php5 git

# install mysql with root password set to "root"
debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
apt-get install -y mysql-server

# Enable mod rewrite
ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/
cp /vagrant/vagrant_config/apache/default /etc/apache2/sites-available/

# install TinyMCE plugin
git clone https://github.com/CakeDC/TinyMCE.git /vagrant/app/Plugin/TinyMCE

apachectl restart

# Create HMS MySQL account
mysql -uroot -proot -e "GRANT ALL ON *.* TO 'hms'@'localhost' IDENTIFIED BY '' WITH GRANT OPTION"
mysql -uroot -proot -e "FLUSH PRIVILEGES"

# Move the tmp folder onto /tmp. Having it in the shared/synced vargant folder seems to cause permissions issues
rm -rf /vagrant/app/tmp
mkdir /tmp/hms
chmod a+rw -R /tmp/hms
ln -s /tmp/hms /vagrant/app/tmp

# do hms setup
cp /vagrant/vagrant_config/setup.php /vagrant/dev/Setup/Web/vagrant-setup.php
cd /vagrant/dev/Setup/Web/
php vagrant-setup.php
chmod a+rw -R /tmp/hms

echo " **** HMS should now be running at http://localhost:8080/ **** "
echo " Login using username Admin, any password"
