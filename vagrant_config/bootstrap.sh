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
debconf-set-selections <<< 'phpmyadmin phpmyadmin/dbconfig-install boolean true'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/admin-pass password root'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/app-password-confirm password phpmyadmin'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/app-pass password mysql'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2'

# nb. SetupCmd.php needs php5-mysqlnd not php5-mysql
apt-get install -y apache2  php5-mysqlnd libapache2-mod-php5 git haveged expect php-pear php5-dev libkrb5-dev mysql-server phpmyadmin php5-xdebug

# bump php.ini memory limit's for xdebug code coverage
sed -i -e 's/memory_limit = 128M/memory_limit = 256M/' /etc/php5/apache2/php.ini

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
if [ -d '/vagrant/app/Plugin/TinyMCE' ]; then
    rm -rf /vagrant/app/Plugin/TinyMCE
fi
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

# if tmp symlink from the previous vagranet exsists remove it
if [ -L '/vagrant/app/tmp' ]; then
    rm -rf /vagrant/app/tmp
fi

# do hms setup
cd /vagrant/dev/Setup/Cmd
php SetupCmd.php -d -h=admin -n=admin -s=user -e=admin@example.org -k -v -f

# Move the tmp folder into /home, having it in the shared/synced vagrant folder seems to cause permissions issues
mkdir -p /home/vagrant/hms-tmp/hms
mv /vagrant/app/tmp /home/vagrant/hms-tmp/hms
ln -s /home/vagrant/hms-tmp/hms/tmp  /vagrant/app/tmp
chmod a+rw -R /home/vagrant/hms-tmp/hms

# Configure HMS to use Kerberos
cp /vagrant/vagrant_config/krb.php /vagrant/app/Config

# Allow HMS principal to manage the Kerberos database
echo "hms/web@NOTTINGTEST.ORG.UK * " >> /etc/krb5kdc/kadm5.acl
/etc/init.d/krb5-kdc restart
/etc/init.d/krb5-admin-server restart 

# Create Kerberos account and keytab for HMS
kadmin.local -q "addprinc -randkey hms/web"
rm /vagrant/app/Config/hms.keytab
kadmin.local -q "ktadd -k /vagrant/app/Config/hms.keytab hms/web"
chmod a+r /vagrant/app/Config/hms.keytab

# Download PHPUunit (CakePHP will read it directly from the PHAR from CakePHP 2.5.7)
wget -O /vagrant/app/Vendor/phpunit.phar https://phar.phpunit.de/phpunit-3.7.38.phar

apachectl restart

cat <<\EOF > /home/vagrant/labelprinter.sh
#!/bin/bash
while [ 1 ]; do
nc -l -p 9100 >> /vagrant/labelprinter.txt;
done
EOF

chmod +x /home/vagrant/labelprinter.sh
sed -i -e 's/^exit 0/\/home\/vagrant\/labelprinter.sh \&\n\nexit 0/' /etc/rc.local

echo "Setting the password of all dummy accounts to be \"password\""...
mysql -uroot -proot hms <<<"select lower(username) from members where username is not null and username != 'Admin'" |
while IFS='\n' read USERNAME
do
  kadmin.local -q "addprinc -pw password $USERNAME" > /dev/null 2>&1
done
echo "...Done"

echo "alias sql=\"mysql -proot -uroot hms\"" > /home/vagrant/.bash_aliases

echo ""
echo "------------------------------------------------------------------------"
echo " **** HMS should now be running at http://localhost:8080/ **** "
echo " **** Login using username=Admin, password=admin          ****"
echo "      (all other accounts have the password \"password\")"
echo ""
echo "MySQL:  username = root,        password = root"
echo "kadmin: username = admin/admin, password = admin"
echo "kadmin: username = vagrant      password = vagrant"
echo ""
echo "Once connected, run 'sql' to start an SQL session in the HMS database, "
echo "and 'kadmin' administer the password database (password=vagrant)"
echo ""
echo "You can access the database at http://localhost:8080/phpmyadmin/"
echo "------------------------------------------------------------------------"
echo ""
