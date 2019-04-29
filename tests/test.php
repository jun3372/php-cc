<?php

require __DIR__ . '/../vendor/autoload.php';

use Jun3\PhpCc\Phpcc;

$root = Phpcc::install();
if (1 ==2)

    var_dump(1==2);

die;
exec('git status -s', $files, $return_var);

echo "----------", PHP_EOL;

foreach ($files as $item) {
    $item = explode(' ', $item);
    if (count($item) < 2) {
        continue;
    }

    if (in_array($item[0], ['A', 'D', 'M'])) {
        continue;
    }

    // 获取文件名称
    $fileName = end($item);
    if (substr($fileName, -4) != '.php') {
        continue;
    }

    // phplint
    // exec("./vendor/bin/phplint " . $fileName, $output, $return_var);
    // if ($return_var) {
    //     foreach ($output as $out) {
    //         if (empty($out)) {
    //             continue;
    //         }
    //         echo $out, PHP_EOL;
    //     }
    //     die;
    // }


    // 执行phpcs文件检测
    exec("./vendor/bin/phpcs -n --standard=psr2 " . $fileName, $output, $return_var);
    if ($return_var) {
        foreach ($output as $out) {
            if (empty($out)) {
                continue;
            }
            echo $out, PHP_EOL;
        }

        // 等待用户确认是否需要自动修复
        fwrite(STDOUT, "是否需要使用phpcbf修复代码(Y|N)：");
        $option = trim(fgets(STDIN));

        // 是否自动修复
        if (strtoupper($option) == 'Y') {
            exec("./vendor/bin/phpcbf " . $fileName, $output, $return_var);
            if ($return_var) {
                echo  $fileName . ", 修复成功: ", PHP_EOL;
            } else {
                echo $fileName . ", 修复失败, 请手动修复", PHP_EOL;
            }
        }
        die;
    }

    echo "phpcs file {$fileName} success !", PHP_EOL;
}
//var_dump($files);
