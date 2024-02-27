# EC-CUBE Upgrade Fixer

EC-CUBE4.2系のプラグインプロジェクトを解析して、EC-CUBE4.3系に互換性のあるコードに変換します。このツールは [Symfony Upgrade Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer) をフォークしています。

## インストール

### Composer

``composer install``コマンドでインストールできます。

## システム要件

このFixerはPHP8.1以上で動作します。
PHP7.4以下には対応していません。

## 使い方

``fix``コマンドを使用して指定したパスのコードを変換します。

```bash
$ bin/eccube-upgrade-fixer fix /path/to/dir
$ bin/eccube-upgrade-fixer fix /path/to/file
```

``--dry-run``オプションを付けると、ファイルは変更せずに変換が必要なファイルを表示します。

```bash
$ bin/eccube-upgrade-fixer fix /path/to/code --dry-run
```


## 利用可能なFixer一覧

| Name  | Description |
| ----  | ----------- |
| ContainerClassFixer | コンテナクラスの取得方法を変更（services.yamlの変更は未対応） |
| EncoderFactoryInterfaceFixer | EncoderFactoryInterfaceクラスの取得先を変更 |
| GetDirFixer | コンテナクラスのgetParameterメソッドによるパラメータ取得をEccubeConfigクラスから実行するように変更 |
| GetRepositoryFixer | コンテナクラスによるレポジトリクラスの取得をEntityManagerから実行するように変更 |
| MasterToMainFixer | RequestStackのgetMasterRequestをgetMainRequestへ変更 |
| NewTokenFixer | Symfony6.0更新によるトークン作成時の第2引数部分の削除 |
| SessionFixer | セッション情報の取得をRequestStackから実行するように変更 |


## Contribute

このツールは[php-cs-fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)に依存しています。
