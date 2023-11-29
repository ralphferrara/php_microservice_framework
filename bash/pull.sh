
echo "Reading git.txt.."
git=$(cat /var/www/git.txt)
if [ "$git" = "" ]; then
    echo "Please enter some git name for microservice (ie scmmesh.com) : "
    read input_variable
    echo "$input_variable" > /var/www/git.txt
    exit 1
fi
git=$(cat /var/www/git.txt)
echo "Have you stopped forever?"
read -n1 -r -p "Press space to continue..." key

echo "Deleting Previous Framework Directory.."
rm -rf /tmp/microservice

echo "Removing all HTML Files Folders.."
rm -rf /var/www/html/
rm -rf /var/www/documents/
echo "Removing all Node Files.."
rm -rf /var/www/node/classes/
rm -rf /var/www/node/views/
rm -rf /var/www/node/sql/
rm -rf /var/www/node/app.js
rm -rf /var/www/node/package.json
echo "Removing all Bash Files.."
rm -rf /var/www/bash/
echo "Removing Core Symbolic Link.."
rm -rf /core
echo "Cloning Framework for Microservice...Please wait.."
sudo git clone https://##USERNAME##:##PASSWORD##@github.com/socialcomedia/__microservice.framework.git /tmp/microservice
sudo mv /tmp/microservice/* /var/www/
echo ""
echo ""


if [ "$git" = "" ]; then
    echo "Git.txt : $git IS EMPTY!"
else
    echo "Found Git.txt : $git"
    echo "Seting HostName to $git"
    sudo hostname "$git"
fi
echo "Deleting Previous Project Directory.."
rm -rf /tmp/MyApp
sudo git clone https://##USERNAME##:##PASSWORD##@github.com/socialcomedia/$git.git /tmp/MyApp
echo "Copy Project Files.."
cp -rf /tmp/MyApp/* /var/www/

echo "Creating Symbolic Link.."
sudo ln -s /var/www/html/core/ /core

echo "Installing Composer"
php -r "copy('https://getcomposer.org/installer', '/var/www/html/core/classes/composer-setup.php');"
php /var/www/html/core/classes/composer-setup.php --install-dir=/var/www/html/core/classes/

echo "Updating Permissions.."
sudo chown -R ec2-user /var/www/
sudo chgrp -R www /var/www/html
sudo chmod -R 655 /var/www/html/program/cache
sudo chmod -R 655 /var/www/html/sites/default/cache
sudo chmod -R 655 /var/www/html/sites/default/uploads
sudo chmod -R 655 /var/www/instance/
sudo chmod +x /var/www/bash/pull.sh

echo "Please start foreveecho "Installing Composer"
php -r "copy('https://getcomposer.org/installer', '/var/www/html/core/classes/composer-setup.php');"
read -n1 -r -p "Press space to continue..." key

echo "Please run npm install"
read -n1 -r -p "Press space to continue..." key

echo "Please run npm install forever -g"
read -n1 -r -p "Press space to continue..." key

pause



