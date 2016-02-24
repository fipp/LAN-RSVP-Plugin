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
->
class { 'wordpress::install': }

class { 'phpmyadmin::install': }
#class { 'composer::install': }
#class { 'phpqa::install': }