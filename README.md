# 試験項目チェックリスト生成ツール

## 概要

Markdownから試験項目のチェックリストを生成するツールです。

以下のようにMarkdownで書かれた試験項目を、Excel形式のチェックリストに変換します。

```
# テスト項目１

## 試験項目

### 試験1

アプリが正常に起動することを確認する

#### 手順

- アプリを起動する
- 「始める」ボタンを押下する。

#### 確認

- [ ] ホーム画面が表示されること
- [ ] ホーム画面にウェルカムメッセージが表示されること
```

## インストール方法

以下の３通りの方法で使うことができます。

dockerを使う方法以外では、予めphpの実行環境が用意されている必要があります。

### このリポジトリをcloneして使う

```
$ git clone https://github.com/kojiro526/php-checklist.git
$ cd php-checklist
$ php -q ./bin/checklist-php.php --help
```

### composerでグローバルインストールして使う

#### パスを追加

Linux, OSX等の場合、`.bash_profile`等に以下のパスを設定して下さい。

```
export PATH=$HOME/.composer/vendor/bin:$PATH
```

Windowsでは以下のフォルダを環境変数のPATHに設定して下さい。

```
%USERPROFILE%\AppData\Roaming\Composer\vendor\bin
```

#### インストール

```
$ composer global require kojiro526/php-checklist
$ checklist-php --help
```

### dockerで使う

phpの実行環境が無くても、本リポジトリに含まれているDockerfileをビルドして使うことが出来ます。

```
$ git clone https://github.com/kojiro526/php-checklist.git
$ cd php-checklist
$ docker build -t kojiro526/php-checklist .
$ docker run --rm kojiro526/php-checklist checklist-php --help
```

## 使い方

基本的な使い方は以下の通りです。

```
$ php -q ./bin/checklist-php -i SOURCE_DIR_PATH -o OUTPUT_FILE
```

dockerを使う場合は、対象となるMarkdownドキュメントが存在するディレクトリをマウントして実行します。

```
$ cd (Markdonドキュメントのあるディレクトリ)
$ docker run --rm -v $(pwd):/work kojiro526/php-checklist checklist-php -i /work -o /work/output.xlsx
```

上記の例では、カレントディレクトリをdockerコンテナ内の`/work`ディレクトリにマウントしているため、コマンドの引数にもコンテナ内のパスを指定しています。

## ライセンス

このソフトウェアは、MITライセンスの元で公開されています。
