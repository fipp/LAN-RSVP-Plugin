class myapache::install {
  class { 'apache':
    default_vhost => false,
    mpm_module => 'prefork',
  }

  class { 'apache::mod::rewrite': }
  class { 'apache::mod::php': }

  apache::vhost { 'wordpress.seatmapevents.dev':
    port    => '80',
    docroot => '/vagrant/wordpress/',
  }

  apache::vhost { 'craft.seatmapevents.dev':
    port    => '80',
    docroot => '/vagrant/craft/public/',
    rewrites => [
      {
        comment      => 'https://craftcms.com/support/remove-index.php',
        rewrite_cond => ['%{REQUEST_FILENAME} !-f', '%{REQUEST_FILENAME} !-d', '%{REQUEST_URI} !^/(favicon\.ico|apple-touch-icon.*\.png)$ [NC]'],
        rewrite_rule => ['^(.+) index.php?p=$1 [QSA,L]'],
      },
    ],
  }
}
