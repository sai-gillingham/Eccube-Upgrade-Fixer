# EC-CUBE Upgrade Fixer [![Build Status](https://travis-ci.org/EC-CUBE/Eccube-Upgrade-Fixer.svg)](https://travis-ci.org/EC-CUBE/Eccube-Upgrade-Fixer)

EC-CUBE3.0系のプラグインプロジェクトを解析して、EC-CUBE3.1系に互換性のあるコードに変換します。このツールは [Symfony Upgrade Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer) をフォークしています。

## インストール

### 手動

#### ローカルインストール

``eccube-upgrade-fixer.phar``をダウンロードしてローカルに保存します。

#### グローバルインストール

以下のコマンドで簡単に``eccube-upgrade-fixer``コマンドをインストールできます。

```bash
$ sudo wget https://github.com/EC-CUBE/Eccube-Upgrade-Fixer/releases/download/v%s/eccube-upgrade-fixer.phar -O /usr/local/bin/eccube-upgrade-fixer
$ sudo chmod a+x /usr/local/bin/eccube-upgrade-fixer
```
インストールしたら、``eccube-upgrade-fixer``コマンドが使用できます。

### Composer

Composerでのインストール方法は今のところありません。

## 使い方

``fix``コマンドを使用して指定したパスのコードを変換します。

```bash
$ eccube-upgrade-fixer fix /path/to/dir
$ eccube-upgrade-fixer fix /path/to/file
```

``--dry-run``オプションを付けると、ファイルは変更せずに変換が必要なファイルを表示します。

```bash
$ symfony-upgrade-fixer fix /path/to/code --dry-run
```

``--no-use-reorder``オプションを指定した場合は、use文の並び替えを行いません。

```bash
$ symfony-upgrade-fixer fix /path/to/code --no-use-reorder
```

##　利用可能なFixer一覧

| Name  | Description |
| ----  | ----------- |%s


## Contribute

このツールは[Symfony Upgrade Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer)をフォークしています。
EC-CUBE3.1系に特化したものでないなら、[Symfony Upgrade Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer) にコントリビュートしてください。

このREADMEにコントリビュートする場合は、直接`README.md`を変更するのではなく、`README.tpl`を変更してから以下のコマンドを実行してください:
```bash
$ eccube-upgrade-fixer readme > README.md
```
