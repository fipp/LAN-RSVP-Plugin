# Install latest Wordpress

class wordpress::install {

  # Get a new copy of the latest wordpress release
  # FILE TO DOWNLOAD: http://wordpress.org/latest.tar.gz

  exec { 'download-wordpress': #tee hee
    command => '/usr/bin/wget http://wordpress.org/latest.tar.gz',
    cwd     => '/vagrant/',
  }

  exec { 'untar-wordpress':
    cwd     => '/vagrant/',
    command => '/bin/tar xzvf /vagrant/latest.tar.gz',
    require => Exec['download-wordpress']
  }

  # Copy a working wp-config.php file for the vagrant setup.
  file { '/vagrant/wordpress/wp-config.php':
    source => 'puppet:///modules/wordpress/wp-config.php'
  }

  # Copy a working wp-tests-config.php file for the vagrant setup.
  file { '/vagrant/wordpress/wp-tests-config.php':
    source  => 'puppet:///modules/wordpress/wp-tests-config.php',
	  require => Exec['untar-wordpress'],
  }
}
