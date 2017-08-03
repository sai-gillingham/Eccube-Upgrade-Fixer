# EC-CUBE Upgrade Fixer [![Build Status](https://travis-ci.org/EC-CUBE/Eccube-Upgrade-Fixer.svg)](https://travis-ci.org/EC-CUBE/Eccube-Upgrade-Fixer)

EC-CUBE3.0系のプラグインプロジェクトを解析して、EC-CUBE3.1系に互換性のあるコードに変換します。このツールは [Symfony Upgrade Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer) をフォークしています。

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
| app_request | Fix $app["request"] -> $app["request"]->getCurrentRequest(). |
| eccube_form_type_names | EC-CUBE FormType support. |
| form_choice_type_array | Flip choices in ChoiceType. |
| form_configure_options | The method AbstractType::setDefaultOptions(OptionsResolverInterface $resolver) have been renamed to AbstractType::configureOptions(OptionsResolver $resolver). |
| form_events | The events PRE_BIND, BIND and POST_BIND were renamed to PRE_SUBMIT, SUBMIT and POST_SUBMIT. |
| form_getname_to_getblockprefix | The method FormTypeInterface::getName() was deprecated, you should now implement FormTypeInterface::getBlockPrefix() instead. |
| form_option_names | Options precision and virtual was renamed to scale and inherit_data. |
| form_parent_type | Returning type instances from FormTypeInterface::getParent() is deprecated, return the fully-qualified class name of the parent type class instead. |
| form_type_names | Instead of referencing types by name, you should reference them by their fully-qualified class name (FQCN) instead. |
| get_request | The getRequest method of the base controller class was removed, request object is injected in the action method instead. |
| inherit_data_aware_iterator | The class VirtualFormAwareIterator was renamed to InheritDataAwareIterator. |
| progress_bar | ProgressHelper has been removed in favor of ProgressBar. |
| property_access | Renamed PropertyAccess::getPropertyAccessor to PropertyAccess::createPropertyAccessor. |
| service_provider | Fix ServiceProvider. |


## Contribute

このツールは[Symfony Upgrade Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer)をフォークしています。
EC-CUBE3.1系に特化したものでないなら、[Symfony Upgrade Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer) にコントリビュートしてください。

このREADMEにコントリビュートする場合は、直接`README.md`を変更するのではなく、`README.tpl`を変更してから以下のコマンドを実行してください:
```bash
$ eccube-upgrade-fixer readme > README.md
```
