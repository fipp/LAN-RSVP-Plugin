class mymysql::install {
  class { '::mysql::server':
    root_password    => 'vagrant',
    override_options => {
      'mysqld' => {
        'bind_address' => '0.0.0.0'
      }
    }
  }

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

  mysql::db { 'craft':
    user     => 'craft',
    password => 'craft',
    host     => '%',
    grant    => ['ALL PRIVILEGES'],
    sql      => '/tmp/craft-db.sql',
  }

}
