#!/bin/bash

export DEBIAN_FRONTEND=noninteractive

apt-get update

# set some default config for Kerberos & MySQL, so the installer doesn't ask during installation
debconf-set-selections <<< 'libssl1.0.0:amd64 libssl1.0.0/restart-services string ntp'
debconf-set-selections <<< 'krb5-config krb5-config/add_servers_realm string NOTTINGTEST.ORG.UK'
debconf-set-selections <<< 'krb5-config krb5-config/read_conf boolean true'
debconf-set-selections <<< 'krb5-admin-server krb5-admin-server/kadmind boolean true'
debconf-set-selections <<< 'krb5-config krb5-config/kerberos_servers string hmsdev.nottingtest.org.uk'
debconf-set-selections <<< 'krb5-config krb5-config/default_realm string NOTTINGTEST.ORG.UK'
debconf-set-selections <<< 'krb5-kdc krb5-kdc/debconf boolean true'
debconf-set-selections <<< 'krb5-kdc krb5-kdc/purge_data_too boolean false'
debconf-set-selections <<< 'krb5-admin-server krb5-admin-server/newrealm note'
debconf-set-selections <<< 'krb5-config krb5-config/add_servers boolean true'
debconf-set-selections <<< 'krb5-config krb5-config/admin_server string hmsdev.nottingtest.org.uk'
debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'

apt-get install -y apache2 php5-mysql libapache2-mod-php5 git haveged expect php-pear php5-dev libkrb5-dev mysql-server


# Install krb, create database, and set the master password to "krbMasterPassword"
apt-get install krb5-{admin-server,kdc} -y
kdb5_util create -s -P krbMasterPassword
mkdir /var/log/kerberos
touch /var/log/kerberos/{krb5kdc,kadmin,krb5lib}.log
chmod -R 750  /var/log/kerberos
echo "vagrant/admin@NOTTINGTEST.ORG.UK * " > /etc/krb5kdc/kadm5.acl
/etc/init.d/krb5-kdc start 
/etc/init.d/krb5-admin-server start 

# create some accounts (vagrant, vagrant/admin, admin/admin)
kadmin.local -q "addprinc -pw admin admin"
kadmin.local -q "addprinc -pw vagrant vagrant/admin"
kadmin.local -q "addprinc -pw vagrant vagrant"

# Enable mod rewrite
ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/
cp /vagrant/vagrant_config/apache/default /etc/apache2/sites-available/

# install TinyMCE plugin
git clone https://github.com/CakeDC/TinyMCE.git /vagrant/app/Plugin/TinyMCE

apachectl restart

# download / build / install php/krb5 extension
# pecl install krb5 - this didn't have kadm support.
mkdir /root/php-krb
cd /root/php-krb
wget http://pecl.php.net/get/krb5-1.0.0.tgz
tar zxf krb5-1.0.0.tgz
cd /root/php-krb/krb5-1.0.0
phpize
./configure --with-krb5kadm=S
patch < /vagrant/vagrant_config/php-krb.patch 
make && make install
ldconfig

echo "extension=krb5.so" >> /etc/php5/mods-available/krb5.ini
ln -s /etc/php5/mods-available/krb5.ini /etc/php5/conf.d/20-krb5.ini 

apachectl restart


# Create HMS MySQL account
mysql -uroot -proot -e "GRANT ALL ON *.* TO 'hms'@'localhost' IDENTIFIED BY '' WITH GRANT OPTION"
mysql -uroot -proot -e "FLUSH PRIVILEGES"

# Move the tmp folder into /home, having it in the shared/synced vagrant folder seems to cause permissions issues
rm -rf /vagrant/app/tmp
mkdir -p /home/vagrant/hms-tmp/hms
chmod a+rw -R /home/vagrant/hms-tmp/hms
ln -s /home/vagrant/hms-tmp/hms /vagrant/app/tmp

# do hms setup
cp /vagrant/vagrant_config/setup.php /vagrant/dev/Setup/Web/vagrant-setup.php
cd /vagrant/dev/Setup/Web/
php vagrant-setup.php
chmod a+rw -R /home/vagrant/hms-tmp/hms
cp /vagrant/vagrant_config/krb.php /vagrant/app/Config

# Create Kerberos account and keytab for HMS
/etc/init.d/krb5-kdc restart 
/etc/init.d/krb5-admin-server restart 
kadmin.local -q "addprinc -randkey hms/web"
rm /vagrant/app/Config/hms.keytab
kadmin.local -q "ktadd -k /vagrant/app/Config/hms.keytab hms/web"
chmod a+r /vagrant/app/Config/hms.keytab 
apachectl restart

echo "alias sql=\"mysql -proot -uroot hms\"" > /home/vagrant/.bash_aliases

echo ""
echo "------------------------------------------------------------------------"
echo " **** HMS should now be running at http://localhost:8080/ **** "
echo " **** Login using username=Admin, password=admin          ****"
echo ""
echo "MySQL:  username = root,        password = root"
echo "kadmin: username = admin/admin, password = admin"
echo "kadmin: username = vagrant      password = vagrant"
echo ""
echo "Once connected, run 'sql' to start an SQL session in the HMS database, "
echo "and 'kadmin' administer the password database (password=vagrant)"
echo "------------------------------------------------------------------------"
echo ""