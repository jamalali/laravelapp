<?php
echo "<pre>\n";
echo shell_exec('git reset --hard origin/master')."\n";
echo shell_exec('git fetch')."\n";
echo shell_exec('git pull')."\n";
echo shell_exec('chmod -R 0775 ../*');
echo shell_exec('cd /var/www/ometria.vivolife.co.uk/&&php /var/www/ometria.vivolife.co.uk/artisan migrate --force')."\n";
echo shell_exec('cd /var/www/ometria.vivolife.co.uk/&&php /var/www/ometria.vivolife.co.uk/artisan config:cache')."\n";
echo shell_exec('cd /var/www/ometria.vivolife.co.uk/&&php /var/www/ometria.vivolife.co.uk/artisan route:cache')."\n";
echo shell_exec('cd /var/www/ometria.vivolife.co.uk/&&php /var/www/ometria.vivolife.co.uk/artisan view:cache')."\n";
echo shell_exec('cd /var/www/ometria.vivolife.co.uk/&&php /var/www/ometria.vivolife.co.uk/artisan horizon:terminate')."\n";
echo shell_exec('cd /var/www/ometria.vivolife.co.uk/&&php /var/www/ometria.vivolife.co.uk/artisan queue:restart')."\n";