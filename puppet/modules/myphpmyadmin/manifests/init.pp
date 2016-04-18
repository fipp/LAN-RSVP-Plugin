class myphpmyadmin::install {
  package { 'phpmyadmin':
    ensure => present,
  }
}
