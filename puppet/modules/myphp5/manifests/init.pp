class myphp5::install {
	package { [
			'php5',
			'php5-mysql',
			'php5-curl',
			'php5-gd',
			'php5-fpm',
			'libapache2-mod-php5',
			'php5-dev',
			'php5-xdebug',
			'mcrypt',
			'php5-mcrypt',
		]:
		ensure => present,
	}

  # Ensure Mcrypt is enabled
	exec { "enablemcrypt":
		path => [ "/bin/", "/sbin/" , "/usr/bin/", "/usr/sbin/" ],
		command => "php5enmod mcrypt",
		notify => Service["apache2"],
		require => Package["php5-mcrypt"],
	}
}
