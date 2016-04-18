class mywordpress::install {

  file { '/tmp/wordpress-db.sql':
    source  => 'puppet:///modules/mywordpress/wordpress-db.sql',
  }

  file { '/vagrant/wordpress/wp-config.php':
    source => 'puppet:///modules/mywordpress/wp-config.php',
  }

  file { '/vagrant/wordpress/wp-tests-config.php':
    source  => 'puppet:///modules/mywordpress/wp-tests-config.php',
  }

}
