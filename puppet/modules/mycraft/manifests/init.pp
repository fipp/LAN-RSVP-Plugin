class mycraft::install {
  file { '/tmp/craft-db.sql':
    source  => 'puppet:///modules/mycraft/craft-db.sql',
  }

  file { '/vagrant/craft/craft/config/db.php':
    source => 'puppet:///modules/mycraft/db.php',
  }
}