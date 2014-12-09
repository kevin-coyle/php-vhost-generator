<?php namespace VHostGenerator;
use \Exception as Exception;

/**
 * @file
 * @author Kevin Coyle
 * Contains the VHost Class.
 */

class VHost {
    private $name;
    private $vHostDir;
    private $domain;
    private $hostDir;
    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Creates a new VHost object.
     * @param $name The name of the VHost.
     * @param $vHostDir The Directory of the VHost.
     * @param $hostDir The Location where the websites are stored.
     * @throws Exception
     */
    public function __construct($name, $vHostDir, $hostDir) {
        $name = preg_replace('/\s+/', '', $name);
        $this->name = htmlspecialchars(strtolower($name));
        $this->vHostDir = self::setVhostDir($vHostDir);
        $this->hostDir = $hostDir;
    }

    /**
     * Sets the Virtual Host Directory.
     * @param $vHostDir
     * @return mixed
     */
    private function setVhostDir($vHostDir) {
        if(file_exists($vHostDir)) {
            return $vHostDir;
        }
        throw new Exception('VHost Directory Does Not Exist');
    }

    /**
     * Lists all Vhost conf files that have been created.
     * @param string $VhostDir
     * @return array
     */
    public function listVhosts() {
        $vHostDir = $this->vHostDir;
        if (!file_exists($vHostDir)) {
            throw new Exception('VHost Directory Does Not Exist');
        }
        $files = array_diff(scandir($vHostDir), array('.', '..'));
        return $files;
    }

    /**
     * Creates the VHost File.
     * @param $name
     * @param $vHostDir
     */
    public function createVhostFile($name, $vHostDir) {
        $uniqueDomain = $this->generateUniqueDomain($name);
        $hostDir = $this->hostDir;
        $domain = $this->getDomain();
        $output = <<<EOD
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
EOD;
        file_put_contents("{$vHostDir}/{$name}.conf", $output . PHP_EOL);
    }

    /**
     * Generates a unique name that can be used for the domain name.
     * @return string
     */
    private function generateUniqueDomain($name) {
        $this->domain = uniqid($name . '-') . '.192.168.99.56.xip.io';
        return $this->domain;
    }
    /**
     * Returns the contents of a VHost file.
     * @return string
     */
    public function getVhost() {
        $name = $this->name;
        $vHostDir = $this->vHostDir;
        $vHostFileName = $vHostDir . '/' . $name . '.conf';
        if (!file_exists($vHostFileName)) {
            throw new Exception('VHost File Does Not Exist');
        }
        return file_get_contents($vHostDir . '/' . $name . '.conf');
    }

}