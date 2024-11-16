<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/php-fpm.php';

// Config

set('repository', 'git@github.com:fatahdigital/concord-laravel.git');

// the http user, generally the same as the SSH/remote_user
set('http_user', 'concord');

set('ssh_multiplexing', false);


// add('shared_files', []);
// add('shared_dirs', []);
// add('writable_dirs', []);


// Hosts
host('staging')
    ->set('labels', ['stage' => 'staging'])
    ->set('hostname', 'staging-concord.configured.cc') // the server hostname
    ->set('branch', 'staging') // the git branch to deploy
    ->set('remote_user', 'concord') // the SSH user
    // ->set('ssh_arguments', ['-o UserKnownHostsFile=/dev/null'])
    ->set('ssh_arguments', ['-o StrictHostKeyChecking=accept-new'])
    ->set('deploy_path', '/home/concord/web/staging-concord.configured.cc/public_html'); // the path to deploy to

host('production')
    ->set('labels', ['stage' => 'main'])
    ->set('hostname', 'concord.configured.cc') // the server hostname
    ->set('branch', 'main') // the git branch to deploy
    ->set('remote_user', 'concord') // the SSH user
    // ->set('ssh_arguments', ['-o UserKnownHostsFile=/dev/null'])
    ->set('ssh_arguments', ['-o StrictHostKeyChecking=accept-new'])
    ->set('deploy_path', '/home/concord/web/concord.configured.cc/public_html');

// its likely that you can get away without modifying anything more
// and you'd have a successful deployment at this point.
// define the paths to PHP & Composer binaries on the server
set('bin/php', '/usr/bin/php');
set('bin/npm', '/usr/bin/npm');
set('bin/composer', '{{bin/php}} /home/concord/.composer/composer');
// a couple of additional options
set('allow_anonymous_stats', false);
set('git_tty', false);



// now onto the build steps, in most cases, you can leave these as below,
// but you can add or remove build steps as required!
// compile our production assets
task('npm:build', function () {
    run('cd {{release_path}} && {{bin/npm}} install');
    run('cd {{release_path}} && {{bin/npm}} run build');
    run('cd {{release_path}} && {{bin/npm}} install --omit=dev');
})->desc('Compile npm files locally');
after('deploy:vendors', 'npm:build');


// Hooks

after('deploy:failed', 'deploy:unlock');

// after a deploy, clear our cache and run optimisations
after('deploy:cleanup', 'artisan:cache:clear');
after('deploy:cleanup', 'artisan:optimize');
// handle queue restarts
after('deploy:success', 'artisan:queue:restart');
after('rollback', 'artisan:queue:restart');

set('php_fpm_version', '8.2');
// after('deploy', 'php-fpm:reload');
after('deploy:success', 'mytask');
task('mytask', function ()  {
    run('whoami');
    run('sudo systemctl status php8.2-fpm');
    run('sudo systemctl restart php8.2-fpm');
    run('sudo systemctl status php8.2-fpm');
})->desc('Restart PHP FPM');