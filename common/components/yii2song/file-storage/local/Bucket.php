<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\filestorage\local;

use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\log\Logger;
use yii2tech\filestorage\BucketSubDirTemplate;

/** 
 * Bucket introduces the file storage bucket based simply on the OS local file system.
 * 
 * Configuration example:
 *
 * ```php
 * 'fileStorage' => [
 *     'class' => 'yii2tech\filestorage\local\Storage',
 *     'basePath' => '@webroot/files',
 *     'baseUrl' => '@web/files',
 *     'filePermission' => 0777,
 *     'buckets' => [
 *         'tempFiles' => [
 *             'baseSubPath' => 'temp',
 *             'fileSubDirTemplate' => '{^name}/{^^name}',
 *         ],
 *         'imageFiles' => [
 *             'baseSubPath' => 'image',
 *             'fileSubDirTemplate' => '{ext}/{^name}/{^^name}',
 *         ],
 *     ]
 * ]
 * ```
 *
 * @see Storage
 *
 * @property string $baseSubPath public alias of {@link _baseSubPath}.
 * @method Storage getStorage()
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Bucket extends BucketSubDirTemplate
{
    /**
     * @var string sub path in the directory specified by [[Storage::basePath]].
     */
    private $_baseSubPath = '';


    /**
     * @param string $baseSubPath sub path
     */
    public function setBaseSubPath($baseSubPath)
    {
        $this->_baseSubPath = $baseSubPath;
    }

    /**
     * @return string sub path
     */
    public function getBaseSubPath()
    {
        if (empty($this->_baseSubPath)) {
            $this->_baseSubPath = $this->defaultBaseSubPath();
        }
        return $this->_baseSubPath;
    }

    /**
     * Composes base sub path with default value.
     * @return string sub path.
     */
    protected function defaultBaseSubPath()
    {
        return $this->getName();
    }

    /**
     * Returns the bucket full base path.
     * This path is based on [[Storage::basePath]] and [[baseSubPath]].
     * @return string bucket full base path.
     */
    public function getFullBasePath()
    {
        $fullBasePath = $this->getStorage()->getBasePath() . '/' . $this->getBaseSubPath();
        $fullBasePath = rtrim($fullBasePath, '/');
        return $fullBasePath;
    }

    /**
     * Gets the full file system name of the file.
     * @param string $fileName - self name of the file.
     * @return string full file name.
     */
    public function getFullFileName($fileName)
    {
        return $this->composeFullFileName($fileName);
    }

    /**
     * Make sure the bucket base path exists and writeable.
     * @return string bucket full base path.
     */
    protected function resolveFullBasePath()
    {
        if (!empty($this->_internalCache['resolvedFullBasePath'])) {
            return $this->_internalCache['resolvedFullBasePath'];
        }
        $fullBasePath = $this->getFullBasePath();
        $this->resolvePath($fullBasePath);
        $this->_internalCache['resolvedFullBasePath'] = $fullBasePath;
        return $fullBasePath;
    }

    /**
     * Composes the full file system name of the file.
     * @param string $fileName - self name of the file.
     * @return string full file name.
     */
    protected function composeFullFileName($fileName)
    {
        return $this->resolveFullBasePath() . '/' . $this->getFileNameWithSubDir($fileName);
    }

    /**
     * Composes the full file system name of the file, making sure its container directory exists.
     * @param string $fileName - self name of the file.
     * @return string full file name.
     */
    protected function resolveFullFileName($fileName)
    {
        $fullFileName = $this->composeFullFileName($fileName);
        $fullFilePath = dirname($fullFileName);
        $this->resolvePath($fullFilePath);
        return $fullFileName;
    }

    /**
     * Resolves file path, making sure it exists and writeable.
     * @param string $path file path to be resolved.
     * @return boolean success.
     * @throws Exception on failure.
     */
    protected function resolvePath($path)
    {
        if (!file_exists($path)) {
            $dirPermission = $this->getStorage()->filePermission;
            $this->log("creating file path '{$path}'");
            FileHelper::createDirectory($path, $dirPermission);
        }
        if (!is_dir($path)) {
            throw new Exception("Path '{$path}' is not a directory!");
        } elseif (!is_writable($path)) {
            throw new Exception("Path: '{$path}' should be writeable!");
        }
        return true;
    }

    /**
     * Gets full file name of the file inside the bucket or inside the other bucket in
     * the same storage.
     * File can be passed as string, which means the internal bucket file,
     * or as an array of 2 elements: first one - the name of the bucket,
     * the second one - name of the file in this bucket
     * @param mixed $fileReference - this bucket existing file name or array reference to another bucket file name.
     * @return string full file name.
     */
    protected function getFullFileNameByReference($fileReference)
    {
        if (is_array($fileReference)) {
            list($bucketName, $fileName) = $fileReference;
            $bucket = $this->getStorage()->getBucket($bucketName);
            $fullFileName = $bucket->getFullFileName($fileName);
        } else {
            $fullFileName = $this->getFullFileName($fileReference);
        }
        return $fullFileName;
    }

    /**
     * Creates this bucket.
     * @return boolean success.
     */
    public function create()
    {
        $this->resolveFullBasePath();
        return true;
    }

    /**
     * Destroys this bucket.
     * @return boolean success.
     */
    public function destroy()
    {
        $fullBasePath = $this->resolveFullBasePath();
        FileHelper::removeDirectory($fullBasePath);
        $this->log("bucket has been destroyed at base path '{$fullBasePath}'");
        $this->clearInternalCache();
        return true;
    }

    /**
     * Checks is bucket exists.
     * @return boolean success.
     */
    public function exists()
    {
        $fullBasePath = $this->getFullBasePath();
        return file_exists($fullBasePath);
    }

    /**
     * Saves content as new file.
     * @param string $fileName - new file name.
     * @param string $content - new file content.
     * @return boolean success.
     */
    public function saveFileContent($fileName, $content)
    {
        $fullFileName = $this->resolveFullFileName($fileName);
        $writtenBytesCount = file_put_contents($fullFileName, $content);
        $result = ($writtenBytesCount>0);
        if ($result) {
            $this->log("file '{$fullFileName}' has been saved");
            chmod($fullFileName, $this->getStorage()->filePermission);
        } else {
            $this->log("Unable to save file '{$fullFileName}'!", Logger::LEVEL_ERROR);
        }
        return $result;
    }

    /**
     * Returns content of an existing file.
     * @param string $fileName - new file name.
     * @return string $content - file content.
     */
    public function getFileContent($fileName)
    {
        $fullFileName = $this->resolveFullFileName($fileName);
        $this->log("content of file '{$fullFileName}' has been returned");
        return file_get_contents($fullFileName);
    }

    /**
     * Deletes an existing file.
     * @param string $fileName - new file name.
     * @return boolean success.
     */
    public function deleteFile($fileName)
    {
        $fullFileName = $this->resolveFullFileName($fileName);
        if (file_exists($fullFileName)) {
            $result = unlink($fullFileName);
            if ($result) {
                $this->log("file '{$fullFileName}' has been deleted");
            } else {
                $this->log("unable to delete file '{$fullFileName}'!", Logger::LEVEL_ERROR);
            }
            return $result;
        }
        $this->log("unable to delete file '{$fullFileName}': file does not exist");
        return true;
    }

    /**
     * Checks if the file exists in the bucket.
     * @param string $fileName - searching file name.
     * @return boolean file exists.
     */
    public function fileExists($fileName)
    {
        $fullFileName = $this->composeFullFileName($fileName);
        return file_exists($fullFileName);
    }

    /**
     * Copies file from the OS file system into the bucket.
     * @param string $srcFileName - OS full file name.
     * @param string $fileName - new bucket file name.
     * @return boolean success.
     */
    public function copyFileIn($srcFileName, $fileName)
    {
        $fullFileName = $this->resolveFullFileName($fileName);
        $result = copy($srcFileName, $fullFileName);
        if ($result) {
            $this->log("file '{$srcFileName}' has been copied to '{$fullFileName}'");
            chmod($fullFileName, $this->getStorage()->filePermission);
        } else {
            $this->log("unable to copy file from '{$srcFileName}' to '{$fullFileName}'!", Logger::LEVEL_ERROR);
        }
        return $result;
    }

    /**
     * Copies file from the bucket into the OS file system.
     * @param string $fileName - bucket existing file name.
     * @param string $destFileName - new OS full file name.
     * @return boolean success.
     */
    public function copyFileOut($fileName, $destFileName)
    {
        $fullFileName = $this->resolveFullFileName($fileName);
        $result = copy($fullFileName, $destFileName);
        if ($result) {
            $this->log("file '{$fullFileName}' has been copied to '{$destFileName}'");
        } else {
            $this->log("unable to copy file from '{$fullFileName}' to '{$destFileName}'!", Logger::LEVEL_ERROR);
        }
        return $result;
    }

    /**
     * Copies file inside this bucket or between this bucket and another
     * bucket of this file storage.
     * File can be passed as string, which means the internal bucket file,
     * or as an array of 2 elements: first one - the name of the bucket,
     * the second one - name of the file in this bucket
     * @param mixed $srcFile - this bucket existing file name or array reference to another bucket file name.
     * @param mixed $destFile - this bucket existing file name or array reference to another bucket file name.
     * @return boolean success.
     */
    public function copyFileInternal($srcFile, $destFile)
    {
        $srcFullFileName = $this->getFullFileNameByReference($srcFile);
        $destFullFileName = $this->getFullFileNameByReference($destFile);
        $this->resolvePath(dirname($destFullFileName));
        $result = copy($srcFullFileName, $destFullFileName);
        if ($result) {
            $this->log("file '{$srcFullFileName}' has been copied to '{$destFullFileName}'");
            chmod($destFullFileName, $this->getStorage()->filePermission);
        } else {
            $this->log("unable to copy file from '{$srcFullFileName}' to '{$destFullFileName}'!", Logger::LEVEL_ERROR);
        }
        return $result;
    }

    /**
     * Copies file from the OS file system into the bucket and
     * deletes the source file.
     * @param string $srcFileName - OS full file name.
     * @param string $fileName - new bucket file name.
     * @return boolean success.
     */
    public function moveFileIn($srcFileName, $fileName)
    {
        return ($this->copyFileIn($srcFileName, $fileName) && unlink($srcFileName));
    }

    /**
     * Copies file from the bucket into the OS file system and
     * deletes the source bucket file.
     * @param string $fileName - bucket existing file name.
     * @param string $destFileName - new OS full file name.
     * @return boolean success.
     */
    public function moveFileOut($fileName, $destFileName)
    {
        return ($this->copyFileOut($fileName, $destFileName) && unlink($this->resolveFullFileName($fileName)));
    }

    /**
     * Moves file inside this bucket or between this bucket and another
     * bucket of this file storage.
     * File can be passed as string, which means the internal bucket file,
     * or as an array of 2 elements: first one - the name of the bucket,
     * the second one - name of the file in this bucket
     * @param mixed $srcFile - this bucket existing file name or array reference to another bucket file name.
     * @param mixed $destFile - this bucket existing file name or array reference to another bucket file name.
     * @return boolean success.
     */
    public function moveFileInternal($srcFile, $destFile)
    {
        $result = $this->copyFileInternal($srcFile, $destFile);
        if ($result) {
            $fullSrcFileName = $this->getFullFileNameByReference($srcFile);
            $result = ($result && unlink($fullSrcFileName));
        }
        return $result;
    }

    /**
     * Gets web URL of the file.
     * @param string $fileName - self file name.
     * @return string file web URL.
     */
    public function getFileUrl($fileName)
    {
        $baseUrl = $this->getStorage()->getBaseUrl();
        $baseUrl .= '/' . $this->getBaseSubPath();
        $fileSubDir = $this->getFileSubDir($fileName);
        if (!empty($fileSubDir)) {
            $baseUrl .= '/' . $fileSubDir;
        }
        $fileUrl = $baseUrl . '/' . $fileName;
        return $fileUrl;
    }
}