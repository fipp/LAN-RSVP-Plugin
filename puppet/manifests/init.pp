# set global path variable for project
# http://www.puppetcookbook.com/posts/set-global-exec-path.html
Exec { path => [ "/bin/", "/sbin/" , "/usr/bin/", "/usr/sbin/", "/usr/local/bin", "/usr/local/sbin", "~/.composer/vendor/bin/" ] }
#class { 'git::install': }
#class { 'subversion::install': }

class { 'apache2::install': }
class { 'php5::install': }
exec { 'apt_update':
  command => 'apt-get update',
  path    => '/usr/bin'
}
class { '::mysql::server':
  root_password    => 'vagrant',
  override_options => {
    'mysqld' => {
      'bind_address' => '0.0.0.0'
    }
  }
}

# Copy a working wp-tests-config.php file for the vagrant setup.
file { '/tmp/wordpress-db.sql':
  source  => 'puppet:///modules/wordpress/wordpress-db.sql',
}
->
mysql::db { 'wordpress':
  user     => 'wordpress',
  password => 'wordpress',
  host     => '%',
  grant    => ['ALL PRIVILEGES'],
  sql      => '/tmp/wordpress-db.sql',
}

mysql::db { 'wp_tests':
  user     => 'wordpress',
  password => 'wordpress',
  host     => '%',
  grant    => ['ALL PRIVILEGES'],
}


class { 'wordpress::install': }
class { 'phpmyadmin::install': }
#class { 'composer::install': }
#class { 'phpqa::install': }