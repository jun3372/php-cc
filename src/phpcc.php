<?php

namespace Jun3\PhpCc;

/**
 * @uses    Phpcc
 * @package Jun3\PhpCc
 * @version 2019年04月29日
 * @author  Jun <zhoujun3372@gmail.com>
 * @license PHP Version 7.1.x {@link [图片]http://www.php.net/license/3_0.txt}
 */
class Phpcc
{
    /**
     * 执行安装
     */
    public static function install()
    {
        // commit前置文件
        static::installFile('pre-commit');
        // push 前置文件
        static::installFile('pre-push');
    }

    /**
     * 安装文件
     *
     * @param string $fileName
     * @return void
     */
    public static function installFile(string $fileName = 'pre-commit')
    {
        // 获取commit配置文件
        $commitFilePath       = self::getGitPath('hooks/' . $fileName);
        $sourceCommitFilePath = self::getDirPath('bin/phpcc');

        // 获取文件的md5至并判断是否一致
        $commitFileMd5       = @md5_file($commitFilePath);
        $sourceCommitFileMd5 = @md5_file($sourceCommitFilePath);
        if (is_file($commitFilePath) && $commitFileMd5 == $sourceCommitFileMd5) {
            echo "你已经安装过了", PHP_EOL;

            return;
        }

        if (!is_file($commitFilePath)) {
            copy($sourceCommitFilePath, $commitFilePath);

            // 添加执行权限
            chmod($commitFilePath, 0755);
        }

        // 获取文件的md5至并判断是否一致
        $commitFileMd5       = md5_file($commitFilePath);
        $sourceCommitFileMd5 = md5_file($sourceCommitFilePath);
        if ($commitFileMd5 != $sourceCommitFileMd5) {
            // 备份就的文件
            copy($commitFilePath, $commitFilePath . '.backup.' . time());

            // 拷贝新的文件
            copy($sourceCommitFilePath, $commitFilePath);

            // 添加执行权限
            chmod($commitFilePath, 0755);
        }
    }

    /**
     * 执行删除
     */
    public static function remove()
    {
        // 移除 commit 前置文件
        static::removeFile('pre-commit');
        // 移除 push 前置文件
        static::removeFile('pre-push');
    }

    public static function removeFile(string $fileName = 'pre-commit')
    {
        $commitFilePath = self::getGitPath("hooks/{$fileName}");
        if (is_file($commitFilePath)) {
            unlink($commitFilePath);
        }

        echo "移除{$fileName}文件成功", PHP_EOL;
    }


    /**
     * 是否已经安装
     *
     * @return bool
     */
    public static function isInstall(): bool
    {
        if (!self::isGit()) {
            return false;
        }

        if (!self::isPhpLint()) {
            return false;
        }

        // commit 前置文件
        $result = static::isInstallFile('pre-commit');
        if (!$result) {
            return $result;
        }

        // push 前置文件
        $result = static::isInstallFile('pre-push');

        return $result;
    }


    /**
     * 是否安装过文件
     *
     * @param string $fileName
     * @return boolean
     */
    public static function isInstallFile(string $fileName = 'pre-commit'): bool
    {
        // 获取commit配置文件
        $commitFilePath       = self::getGitPath("hooks/{$fileName}");
        $sourceCommitFilePath = self::getDirPath('bin/phpcc');
        if (!is_file($commitFilePath)) {
            self::install();

            return false;
        }

        // 获取文件的md5至并判断是否一致
        $commitFileMd5       = md5_file($commitFilePath);
        $sourceCommitFileMd5 = md5_file($sourceCommitFilePath);
        if ($commitFileMd5 != $sourceCommitFileMd5) {
            self::install();

            return false;
        }

        return true;
    }


    /**
     * 是否安装phplint
     *
     * @return bool
     */
    public static function isPhpLint(): bool
    {
        $file = self::getPhpLintPath();
        if (!is_file($file)) {
            echo "你还没有安装phplint扩展", PHP_EOL;

            return false;
        }

        // 执行获取版本号
        exec($file . ' --version', $output, $return_var);
        if ($return_var) {
            echo "你还没有安装phplint扩展", PHP_EOL;

            return false;
        }

        // 输出phplint版本号
        echo $output[0], PHP_EOL;


        return true;
    }

    /**
     * 获取phplint文件位置
     *
     * @return string
     */
    public static function getPhpLintPath(): string
    {
        $path = self::getRootPath('vendor/bin/phplint');

        return $path;
    }

    /**
     * 是否已经是git仓库
     *
     * @return bool
     */
    public static function isGit(): bool
    {
        // 获取git配置路径
        $gitPath = self::getGitPath();
        if (is_dir($gitPath)) {
            return true;
        }

        echo "你还没有初始化Git仓库", PHP_EOL;

        return false;
    }

    /**
     * 获取git配置路径
     *
     * @param string $path
     *
     * @return string
     */
    public static function getGitPath($path = ''): string
    {
        $rootPath = self::getRootPath();
        $result   = true;
        $gitPath  = '';
        while ($result) {
            $gitPath = $rootPath . '.git';
            if (is_dir($gitPath)) {
                $result = false;
            }

            // 获取上级目录
            $rootPath = dirname($rootPath);
        }

        // 是否为空
        if (empty($rootPath)) {
            die("你还没有初始化Git仓库");
        }

        // 拼接后缀
        if (!empty($path)) {
            $gitPath .= DIRECTORY_SEPARATOR . $path;
        }

        return $gitPath;
    }

    /**
     * 获取配置文件路径
     *
     * @return string
     */
    public static function getConfigPath(): string
    {
        $path = self::getRootPath('.php_cc');

        return $path;
    }

    /**
     * 获取根目录文件路径
     *
     * @param string $path
     *
     * @return string
     */
    public static function getRootPath($path = ''): string
    {
        if (strlen($path) > 0) {
            if (substr($path, 0, 1) == DIRECTORY_SEPARATOR) {
                $path = substr($path, 1);
            }
        }

        return getcwd() . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * 获取当前组件根目录
     *
     * @param string $path
     * @return string
     */
    public static function getDirPath($path = ''): string
    {
        if (strlen($path) > 0) {
            if (substr($path, 0, 1) == DIRECTORY_SEPARATOR) {
                $path = substr($path, 1);
            }
        }

        $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR;

        return $dir . $path;
    }
}
