# set global path variable for project
# http://www.puppetcookbook.com/posts/set-global-exec-path.html
Exec { path => [ "/bin/", "/sbin/" , "/usr/bin/", "/usr/sbin/", "/usr/local/bin", "/usr/local/sbin", "~/.composer/vendor/bin/" ] }
#class { 'git::install': }
#class { 'subversion::install': }

package { 'sendmail':
  ensure => 'installed',
}

exec { 'apt_update':
  command => 'apt-get update',
  path    => '/usr/bin'
}

class { 'myphpmyadmin::install': }
class { 'myapache::install': }
class { 'myphp5::install': }

class { 'mywordpress::install': }
->
class { 'mycraft::install': }
->
class { 'mymysql::install': }

#class { 'composer::install': }
#class { 'phpqa::install': }