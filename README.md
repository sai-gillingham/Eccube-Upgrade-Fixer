# EC-CUBE Upgrade Fixer [![Build Status](https://travis-ci.org/EC-CUBE/Eccube-Upgrade-Fixer.svg)](https://travis-ci.org/EC-CUBE/Eccube-Upgrade-Fixer)

EC-CUBE4.1系のプラグインプロジェクトを解析して、EC-CUBE4.2系に互換性のあるコードに変換します。このツールは [Symfony Upgrade Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer) をフォークしています。

## インストール

### 手動

#### ローカルインストール

``eccube-upgrade-fixer.phar``をダウンロードしてローカルに保存します。

#### グローバルインストール

以下のコマンドで簡単に``eccube-upgrade-fixer``コマンドをインストールできます。

```bash
$ sudo wget https://github.com/EC-CUBE/Eccube-Upgrade-Fixer/releases/download/v0.1.5-eccube-3.1.0-alpha/eccube-upgrade-fixer.phar -O /usr/local/bin/eccube-upgrade-fixer
$ sudo chmod a+x /usr/local/bin/eccube-upgrade-fixer
```
インストールしたら、``eccube-upgrade-fixer``コマンドが使用できます。

### Composer

Composerでのインストール方法は今のところありません。

## システム要件

このFixerはPHP7.4で動作します。
現在PHP8には対応していません。

## 使い方

``fix``コマンドを使用して指定したパスのコードを変換します。

```bash
$ eccube-upgrade-fixer fix /path/to/dir
$ eccube-upgrade-fixer fix /path/to/file
```

``--dry-run``オプションを付けると、ファイルは変更せずに変換が必要なファイルを表示します。

```bash
$ eccube-upgrade-fixer fix /path/to/code --dry-run
```

``--no-use-reorder``オプションを指定した場合は、use文の並び替えを行いません。

```bash
$ eccube-upgrade-fixer fix /path/to/code --no-use-reorder
```

### Dockerで利用する

プラグインプロジェクトのディレクトリをマウントすれば以下のようにDockerで実行することができます。

```bash
docker run --rm -v /path/to/plugin:/app eccube/upgrade-fixer fix /app
```


## 利用可能なFixer一覧

| Name  | Description |
| ----  | ----------- |
| doctrine_namespace | Update Doctrine namespacing |
| email_validator | Fix up strict validation changes to email. From an instance reference to CONSTANT reference |
| email_validator_parameter_update | Email validation parameter updated |
| event_dispatcher | Switch EC-CUBE Event dispatcher parameters |
| event_namespace_update | Response event namespace update. |
| extended_types_type_return | Add iterable type return to getExtendedTypes() class |
| pDOFunction_update | fetchOne to fetchRow Update |
| pHP8_parameter | Fixes for php 8 |
| pHPDOC | Fixes to incorrect php doc class references |
| remove_format_from_date_form | Remove date parameter from DateType::class |
| swift_mailer_change | Update from \Swift_Mailer to Symfony 5 MailerInterface |
| translation_class | Translation class fixes |
| unit_test | UnitTest setUp function requires void return type |


## Contribute

このツールは[Symfony Upgrade Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer)をフォークしています。
EC-CUBEに特化したものでないなら、[Symfony Upgrade Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer) にコントリビュートしてください。

このREADMEにコントリビュートする場合は、直接`README.md`を変更するのではなく、`README.tpl`を変更してから以下のコマンドを実行してください:
```bash
$ eccube-upgrade-fixer readme > README.md
```
