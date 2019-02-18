<?php

/**
 * Image file optimizer, uses https://github.com/bensquire/php-image-optim. 
 * Class try optimize image file (png, jpg, gif) require: optipng, jpegoptim, gifsicle.
 * 
 * - http://optipng.sourceforge.net/
 * - http://freecode.com/projects/jpegoptim
 * - http://www.lcdf.org/gifsicle/
 * 
 * @category    Extlib
 * @package     ImageOptimizer
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2014 Lukasz Ciolecki (mart)
 * @link        https://github.com/lciolecki/php-image-optimizer
 */
class ImageOptimizer {

    const TYPE_PNG = 'image/png';
    const TYPE_JPEG = 'image/jpeg';
    const TYPE_GIF = 'image/gif';

    /* Default image optimizators */
    const OPTIMIZER_OPTIPNG = 'optipng';
    const OPTIMIZER_JPEGOPTIM = 'jpegoptim';
    const OPTIMIZER_GIFSICLE = 'gifsicle';

    /**
     * Binary paths optimizators
     * 
     * @var array
     */
    protected $binaryPaths = array(
        self::OPTIMIZER_OPTIPNG => '/usr/bin/optipng',
        self::OPTIMIZER_JPEGOPTIM => '/usr/bin/jpegoptim',
        self::OPTIMIZER_GIFSICLE => '/usr/bin/gifsicle'
    );

    /**
     * Instance of construct
     * 
     * @param array $binaryPaths
     */
    function __construct(array $binaryPaths = array()) {
        $this->binaryPaths = array_merge($this->binaryPaths, $binaryPaths);
        foreach($this->binaryPaths as $optimizerName => $optimizerPath){
            switch ($optimizerName) {
                case self::OPTIMIZER_JPEGOPTIM:
                    $optim = new IJpegOptim();
                    $optim->setBinaryPath($this->binaryPaths[self::OPTIMIZER_JPEGOPTIM]);
                    break;
                case self::OPTIMIZER_OPTIPNG:
                    $optim = new IOptiPng();
                    $optim->setBinaryPath($this->binaryPaths[self::OPTIMIZER_OPTIPNG]);
                    break;
                case self::OPTIMIZER_GIFSICLE:
                    $optim = new IGifSicle();
                    $optim->setBinaryPath($this->binaryPaths[self::OPTIMIZER_GIFSICLE]);
                    break;
                default:
                    $optim = null;
                    break;
            }
            if(!$optim || !($version = $optim->checkVersion())){
                throw new Exception("$optimizerName tools load failed. Is $optimizerPath installed on the server?");
            } // else echo $version.'<hr/>';
        }
    }

    /**
     * Optimize file
     * 
     * @param string $path
     * @return boolean
     */
    public function optimize($path, $output = null, $simulate = null, $quiet = null) {
        if(IOptimCommon::checkImagePath($path) && ($info = getimagesize($path))!==false){
            switch ($info['mime']) {
                case self::TYPE_JPEG:
                    $optim = new IJpegOptim();
                    $optim->setBinaryPath($this->binaryPaths[self::OPTIMIZER_JPEGOPTIM]);
                    break;
                case self::TYPE_PNG:
                    $optim = new IOptiPng();
                    $optim->setBinaryPath($this->binaryPaths[self::OPTIMIZER_OPTIPNG]);
                    break;
                case self::TYPE_GIF:
                    $optim = new IGifSicle();
                    $optim->setBinaryPath($this->binaryPaths[self::OPTIMIZER_GIFSICLE]);
                    break;
                default:
                    return false;
            }
            $optim->setImagePath($path, false);
            if ($output !== null && $output) {
                $optim->setOutputPath($output);
            }
            if ($simulate !== null) {
                $optim->setSimulate($simulate);
            }
            if ($quiet !== null) {
                $optim->setQuiet($quiet);
            }
            return $optim->optimise()->getResult();
        }
        return false;
    }

}

interface IOptimInterface {

    const OPTIMISATION_LEVEL_BASIC = 1;
    const OPTIMISATION_LEVEL_STANDARD = 2;
    const OPTIMISATION_LEVEL_EXTREME = 3;

    function optimise();

    function setBinaryPath($binaryPath = '');

    function checkVersion();

    function setImagePath($imagePath);

    function setOptimisationLevel($level = 2);

    function determinePreOptimisedFileSize();

    function determinePostOptimisedFileSize();
}

class IOptimCommon {

    protected $binaryPath = '';
    protected $imagePath = '';
    protected $outputPath = '';
    protected $originalFileSize = '';
    protected $finalFileSize = '';
    protected $optimisationLevel = 1;
    protected $simulate = false;
    protected $quiet = true;
    protected $result = '';

    public function setSimulate($simulate) {
        $this->simulate = $simulate;
        return $this;
    }
    
    public function setQuiet($quiet) {
        $this->quiet = $quiet;
        return $this;
    }

    public function getResult() {
        return $this->result;
    }

    public function setResult($result) {
        $this->result = $result;
        return $this;
    }

    public static function buildResult($originalFileSize, $finalFileSize) {
        $result = '['.((!$originalFileSize || !$finalFileSize) ? 'ERROR' : 'OK').']';
        $result .= ' ' . ($originalFileSize*1).' --> '.($finalFileSize*1).' bytes';
        if(!$originalFileSize || !$finalFileSize || $originalFileSize == $finalFileSize){
            $result .= ', skipped';
        } else if($originalFileSize > $finalFileSize){
            $result .= ' ('.round(($originalFileSize-$finalFileSize)*100/$originalFileSize, 2).'%), optimized';
        } else {
            $result .= ', unoptimized';
        }
        $result .= '.';
        return $result;
    }

    /**
     * Check the path of the image
     *
     * @param $imagePath
     *
     * @return bool
     * @throws Exception
     */
    public static function checkImagePath($imagePath) {
        if (!file_exists($imagePath)) {
            throw new Exception('Invald image path');
        }
        if (!is_readable($imagePath)) {
            throw new Exception('The file cannot be read');
        }
        return true;
    }

    /**
     * Check the output path of the image
     *
     * @param $destPath
     *
     * @return bool
     * @throws Exception
     */
    public static function checkOutputPath($destPath) {
        if (!$destPath || !($destDir = dirname($destPath)) || !is_dir($destDir)) {
            throw new Exception('Invald destination image path');
        }
        if (!is_writable($destDir)) {
            throw new Exception('The file cannot be write');
        }
        return true;
    }

    /**
     * Sets the path of the executable
     *
     * @param string $binaryPath
     *
     * @return $this
     * @throws Exception
     */
    public function setBinaryPath($binaryPath = '') {
//        if (!file_exists($binaryPath)) {
//            throw new Exception('Unable to locate binary file');
//        }

        $this->binaryPath = $binaryPath;
        return $this;
    }

    /**
     * Sets the path of the image
     *
     * @param $imagePath
     *
     * @return $this
     * @throws Exception
     */
    public function setImagePath($imagePath, $check=true) {
        if (!$check || self::checkImagePath($imagePath)) {
            $this->imagePath = $imagePath;
        }
        return $this;
    }

    /**
     * Sets the path of the image output
     *
     * @param $outputPath
     *
     * @return $this
     * @throws Exception
     */
    public function setOutputPath($outputPath, $check=true) {
        if (!$check || self::checkOutputPath($outputPath)) {
            $this->outputPath = $outputPath;
        }
        return $this;
    }

    /**
     * Sets the desired level of optimisation.
     *
     * @param int $level
     *
     * @return $this
     * @throws \Exception
     */
    public function setOptimisationLevel($level = 2) {
        if (!is_int($level)) {
            throw new Exception('Invalid Optimisation Level');
        }

        if ($level !== IOptimInterface::OPTIMISATION_LEVEL_BASIC &&
                $level !== IOptimInterface::OPTIMISATION_LEVEL_STANDARD &&
                $level !== IOptimInterface::OPTIMISATION_LEVEL_EXTREME
        ) {
            throw new Exception('Invalid Optimisation level');
        }

        $this->optimisationLevel = $level;
        return $this;
    }

    /**
     * Applay prepared command
     *
     * @param string $cmd
     *
     * @return string
     */
    protected function apply($cmd) {
        $this->determinePreOptimisedFileSize();
        $result = shell_exec($cmd);
        $this->determinePostOptimisedFileSize();
        return $result;
    }

    /**
     * Calculates and stores the pre-optimised fileSize
     *
     * @return $this
     */
    public function determinePreOptimisedFileSize() {
        $this->originalFileSize = filesize($this->imagePath);
        return $this;
    }

    /**
     * Calculates and stores the post-optimised fileSize
     *
     * @return $this
     */
    public function determinePostOptimisedFileSize() {
        clearstatcache();
        $this->finalFileSize = filesize($this->outputPath ? $this->outputPath : $this->imagePath);
        return $this;
    }

}

class IGifSicle extends IOptimCommon implements IOptimInterface {

    public function optimise() {
        if($this->simulate && (!$this->outputPath || $this->imagePath == $this->outputPath)){
            $this->outputPath = dirname($this->imagePath) . '/_giftmp' . md5($this->imagePath);
        }
        
        $cmd = $this->binaryPath . ' -b -O2';
        if($this->quiet) $cmd .= ' -w';
        if($this->outputPath) $cmd .= ' -o ' . escapeshellarg($this->outputPath);
        $cmd .= ' ' . escapeshellarg($this->imagePath);
        
        $iResult = $this->apply($cmd);

        if($this->simulate){
            unlink($this->outputPath);
        }

        if ($iResult === null || $iResult === 0) {
            $iResult = self::buildResult($this->originalFileSize, $this->finalFileSize);
        } else if(!$this->finalFileSize || $this->originalFileSize == $this->finalFileSize){
            throw new Exception('Gifsicle was unable to optimise image, result:' . $iResult . ' File: ' . $this->imagePath);
        }
        
        $this->setResult($iResult);

        return $this;
    }

    public function checkVersion() {
        return shell_exec($this->binaryPath . ' --version');
    }

}

class IJpegOptim extends IOptimCommon implements IOptimInterface {

    public function optimise() {
        $cmd = $this->binaryPath . ' --max=85 --strip-all --all-progressive';
        if($this->simulate) $cmd .= ' -n';
        if($this->quiet) $cmd .= ' -q';
        if($this->outputPath) $cmd .= ' --dest=' . escapeshellarg($this->outputPath);
        $cmd .= ' ' . escapeshellarg($this->imagePath);

        $iResult = $this->apply($cmd);

        if ($iResult === null) {
            $iResult = self::buildResult($this->originalFileSize, $this->finalFileSize);
        } else if (!$iResult) {
            throw new Exception("Conversion to compressed JPG failed. Is jpegoptim installed on the server?");
        }

        $this->setResult($iResult);

        return $this;
    }

    public function checkVersion() {
        return shell_exec($this->binaryPath . ' --version');
    }

    public function setOutputPath($outputPath, $check=true) {
        if (!$outputPath || !($outputDir = dirname($outputPath)) || !is_dir($outputDir)) {
            throw new Exception('Invald destination image path');
        }

        if (!is_writable($outputDir)) {
            throw new Exception('The file cannot be write');
        }

        $this->outputPath = $outputDir;
        return $this;
    }

}

class IOptiPng extends IOptimCommon implements IOptimInterface {

    public function optimise() {
        $cmd = $this->binaryPath . ' -i0 -o7 -zc9';
        if($this->simulate) $cmd .= ' -simulate';
        if($this->quiet) $cmd .= ' -quiet';
        if($this->outputPath) $cmd .= ' -out ' . escapeshellarg($this->outputPath);
        $cmd .= ' ' . escapeshellarg($this->imagePath);

        $iResult = $this->apply($cmd);

        if ($iResult === null || $iResult === 0) {
            $iResult = self::buildResult($this->originalFileSize, $this->finalFileSize);
        } else if(!$this->finalFileSize || $this->originalFileSize == $this->finalFileSize) {
            throw new Exception('OPTIPNG was unable to optimise image, result:' . $iResult . ' File: ' . $this->imagePath);
        }

        $this->setResult($iResult);

        return $this;
    }

    public function checkVersion() {
        return shell_exec($this->binaryPath . ' -v');
    }

}