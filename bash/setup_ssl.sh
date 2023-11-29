sudo yum -y install mod_ssl
sudo service httpd restart
sudo yum -y install epel-release
sudo git clone https://github.com/letsencrypt/letsencrypt /usr/local/
cd /usr/local/letsencrypt
echo "Please enter the name of the domain to secure : "
read input_variable
echo "Please run the following line"
echo ""
echo "cd /usr/local/letsencrypt"
echo "./letsencrypt-auto --apache -d $input_variable"
