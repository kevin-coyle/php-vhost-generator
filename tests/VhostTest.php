<?php
/**
 * @file
 * Test file for the VHost Class.
 */
use VhostGenerator\Vhost as VHost;
class VhostTest extends PHPUnit_Framework_TestCase{
    private $vHostDir;
    private $name;
    private $hostDir;

    protected function setUp() {
        $this->vHostDir = __DIR__ . '/vhosts';
        $this->hostDir = '/var/www/html/websites/temp';
        $this->name = 'test-vhost';
        mkdir($this->vHostDir);
    }

    protected function tearDown() {
        exec("rm -rf {$this->vHostDir}");
    }

    public function testCanBeInstantiated() {
        try {
            $reflector = new ReflectionClass('\Vhost\Vhost');
            if ($reflector->isInstantiable()) {

            }
        } catch (Exception $e){
            $this->fail('Class not instantiable');
        }
    }

    public function testCanListVirtualHosts() {
        $vhostDir = $this->vHostDir;
        $hostDir = $this->hostDir;
        $reflector = new ReflectionClass('\Vhost\Vhost');
        $reflector->getMethod('listVhosts');

        $Vhost = new Vhost('', $vhostDir, $hostDir);
        $Vhosts = $Vhost->listVhosts();
        $this->assertTrue(is_array($Vhosts));
    }

    public function testCanCreateVhost() {
        $name = $this->name;
        $vHostDir = $this->vHostDir;
        $hostDir = $this->hostDir;
        $Vhost = new Vhost($name, $vHostDir, $hostDir);
        $Vhost->createVhostFile($name, $vHostDir);
    }

    public function testCanGetVhost() {
        $vHostDir = $this->vHostDir;
        $name = $this->name;
        $hostDir = $this->hostDir;

        $vHost = new Vhost($name, $vHostDir, $hostDir);
        $vHost->createVhostFile($name, $vHostDir);
        $vHostFile = $vHost->getVhost();
        $domain = $vHost->getDomain();
        $expectedVhostFile = <<<EOF
            <VirtualHost *:80>
              ServerName {$domain}
              ServerAdmin webmaster@example.org
              ErrorLog /var/log/httpd/{$name}.err
              CustomLog /var/log/httpd/{$name}.log combined
              DocumentRoot {$hostDir}/{$name}
              <Directory "{$hostDir}/{$name}">
                Order allow,deny
                Allow from all
              </Directory>
            </VirtualHost>
EOF;
        $expectedVhostFile = $expectedVhostFile . PHP_EOL;
        $this->assertEquals($expectedVhostFile, $vHostFile);
    }
}