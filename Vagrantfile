# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "trusty64"
  config.vm.box_url = "http://cloud-images.ubuntu.com/vagrant/trusty/current/trusty-server-cloudimg-amd64-vagrant-disk1.box"

  # allows running commands globally in shell for installed composer libraries
  config.vm.provision :shell, path: "files/scripts/setup.sh"

  # setup virtual hostname and provision local IP
  config.vm.hostname = "seatmapevents.dev"
  config.vm.network :private_network, :ip => "192.168.50.4"
  config.hostsupdater.aliases = ["wordpress.seatmapevents.dev", "craft.seatmapevents.dev"]
  config.hostsupdater.remove_on_suspend = true

  config.vm.provision :shell do |shell|
    shell.inline = "mkdir -p /etc/puppet/modules;
                    puppet module install --force puppetlabs/mysql;
                    puppet module install --force puppetlabs/stdlib;
                    puppet module install --force puppetlabs/concat;
                    puppet module install --force puppetlabs-apache"
  end

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "puppet/manifests"
    puppet.module_path = "puppet/modules"
    puppet.manifest_file  = "init.pp"
    puppet.options="--verbose --debug"
  end

  # Fix for slow external network connections
  config.vm.provider :virtualbox do |vb|
    vb.memory = 2048
    vb.customize ['modifyvm', :id, '--natdnshostresolver1', 'on']
    vb.customize ['modifyvm', :id, '--natdnsproxy1', 'on']
  end

  # This shares the folder and sets very liberal permissions
  config.vm.synced_folder ".", "/vagrant",
    :owner => 'www-data',
    :group => 'www-data',
    :mount_options => ['dmode=777,fmode=777']

  # This disables the runtime folder from being synced which makes vagrant 2-3 times faster
  config.vm.synced_folder "craft/storage/runtime", "/vagrant/craft/storage/runtime", disabled: true

end
