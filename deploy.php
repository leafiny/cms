<?php

declare(strict_types=1);

/**
 * Deploy all the resources in the public directory
 */
class Deployment {
    /**
     * The crypt key file
     *
     * @var string CRYPT_KEY
     */
    protected const CRYPT_KEY = 'deploy.key';

    /**
     * The directory separator
     *
     * @var string DIRECTORY_SEPARATOR
     */
    protected const DS = DIRECTORY_SEPARATOR;

    /**
     * The directory to deploy the resources
     *
     * @var string $pubDirectory
     */
    protected $pubDirectory = 'pub';

    /**
     * The link type (symlink or copy)
     *
     * @var string $linkType
     */
    protected $linkType = 'symlink';

    /**
     * Process the deployment
     */
    public function process(): void
    {
        $this->addMediaLink();
        $this->addModuleLink();
    }

    /**
     * Add the link to the media directory
     *
     * @return void
     */
    public function addMediaLink(): void
    {
        $pubMediaDir = $this->getPubDir() . 'media';

        if ($this->linkType === 'symlink' && !is_link($pubMediaDir)) {
            symlink($this->getMediaDir(), $pubMediaDir);
        }
        if ($this->linkType === 'copy') {
            $this->copyDir($this->getMediaDir(), $pubMediaDir);
        }
    }

    /**
     * Add the links to the modules directories
     *
     * @return void
     */
    public function addModuleLink(): void
    {
        $modules = scandir($this->getModulesDir(), 1);

        foreach ($modules as $module) {
            if ($module === '..') {
                continue;
            }
            foreach ($this->getModuleDirToLink() as $directory) {
                $target = $this->getModulesDir() . $module . self::DS . $directory;
                if (!is_dir($target)) {
                    continue;
                }

                $link = $this->getPubDir() . 'modules' . self::DS . $this->getEncryptedDirName($module) . $directory;

                if (!is_dir(dirname($link))) {
                    mkdir(dirname($link), 0777, true);
                }

                if ($this->linkType === 'symlink' && !is_link($link)) {
                    symlink($target, $link);
                }
                if ($this->linkType === 'copy') {
                    $this->copyDir($target, $link);
                }
            }
        }
    }

    /**
     * Update the default pub directory
     *
     * @param string $directory
     *
     * @return deployment
     */
    public function setPubDirectory(string $directory): deployment
    {
        $this->pubDirectory = $directory;

        return $this;
    }

    /**
     * Update the default pub directory
     *
     * @param string $linkType
     *
     * @return deployment
     */
    public function setLinkType(string $linkType): deployment
    {
        $this->linkType = $linkType;

        return $this;
    }

    /**
     * Retrieve the root directory
     *
     * @return string
     */
    public function getRootDir(): string
    {
        return __DIR__ . self::DS;
    }

    /**
     * Retrieve the media directory
     *
     * @return string
     */
    public function getMediaDir(): string
    {
        return $this->getRootDir() . 'media'  . self::DS;
    }

    /**
     * Retrieve the pub directory
     *
     * @return string
     */
    public function getPubDir(): string
    {
        return $this->getRootDir() . $this->pubDirectory  . self::DS;
    }

    /**
     * Retrieve the modules directory
     *
     * @return string
     */
    public function getModulesDir(): string
    {
        return $this->getRootDir() . 'modules'  . self::DS;
    }

    /**
     * Retrieve the var directory
     *
     * @return string
     */
    public function getVarDir(): string
    {
        return $this->getRootDir() . 'var'  . self::DS;
    }

    /**
     * Retrieve the encrypted directory name
     *
     * @param string $moduleName
     *
     * @return string
     */
    protected function getEncryptedDirName(string $moduleName): string
    {
        return md5($this->getSalt() . $moduleName) . self::DS;
    }

    /**
     * Retrieve the crypt directory
     *
     * @return string
     */
    protected function getCryptDir(): string
    {
        $directory = $this->getVarDir() . 'crypt' . self::DS;

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return $directory;
    }

    /**
     * Retrieve the scrypt file
     *
     * @return string
     */
    protected function getCryptFile(): string
    {
        $file = $this->getCryptDir() . self::CRYPT_KEY;

        if (!is_file($file)) {
            file_put_contents($file, $this->generateSalt());
        }

        return $file;
    }

    /**
     * Retrieve the module directories to link
     *
     * @return string[]
     */
    protected function getModuleDirToLink(): array
    {
        return ['skin', 'media'];
    }

    /**
     * Retrieve the salt for the directory name
     *
     * @return string
     */
    protected function getSalt(): string
    {
        return file_get_contents($this->getCryptFile());
    }

    /**
     * Generate a random salt
     *
     * @return string
     */
    protected function generateSalt(): string
    {
        return uniqid('', true) . '.' . rand(10000000, 99999999);
    }

    /**
     * Copy directory recursively
     *
     * @param string $from
     * @param string $to
     * @param int    $count
     *
     * @return int
     */
    public function copyDir(string $from, string $to, int &$count = 0): int
    {
        $from = rtrim($from, self::DS) . self::DS;
        $to = rtrim($to, self::DS) . self::DS;

        $files = glob($from . '*');

        foreach ($files as $file) {
            $target = str_replace($from, $to, $file);

            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0777, true);
            }

            if (is_file($file)) {
                if (copy($file, $target)) {
                    $count++;
                }
            }
            if (is_dir($file)) {
                $this->copyDir($file, $target, $count);
            }
        }

        return $count;
    }
}

$deployment = new Deployment();
if (isset($argv[1])) {
    $deployment->setLinkType($argv[1]);
}
if (isset($argv[2])) {
    $deployment->setPubDirectory($argv[2]);
}
$deployment->process();
