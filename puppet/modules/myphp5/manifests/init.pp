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
}
