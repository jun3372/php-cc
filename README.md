<h1 align="center"> php-cc </h1>

<p align="center"> .</p>


## Installing

```shell
$ composer require jun3/php-cc --dev
```

## Usage

### 运行检测文件并检测是否安装
```shell
$ ./vendor/bin/phpcc
```
### 自动安装
```php
Jun3\PhpCc\Phpcc::install
```

### 判断是否安装, 没安装则自动安装
```php
Jun3\PhpCc\Phpcc::isInstall
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/jun3/php-cc/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/jun3/php-cc/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT