# Deploy 教學 #

> 本教學假設睇緊既你係有返咁上下電腦知識，甚至係資訊科技從業員（即俗稱既 IT 狗）。

## 事前準備 ##
1. [登記 Heroku account](https://signup.heroku.com/)
2. [安裝 Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli)
3. 安裝 [PHP](https://www.php.net/)
4. 安裝 [Composer](https://getcomposer.org/doc/00-intro.md)

## 設定 Heroku App ##
1. `Procfile` - 設定 [startup script](https://devcenter.heroku.com/articles/getting-started-with-php?singlepage=true#define-a-procfile) + [Runtime Environment](https://devcenter.heroku.com/articles/custom-php-settings#how-applications-are-launched-during-dyno-boot)
2. composer.json - 定義 PHP version + Heroku build pack

## Deploy 步驟 ##
1. `hreoku create <project_name>`
2. `git add -A`
3. `git commit -m "Custom commit message"`
4. `git push`

## 官方教學 ##
1. [Getting Started on Heroku with PHP](https://devcenter.heroku.com/articles/getting-started-with-php?singlepage=true)
2. [Customizing Web Server and Runtime Settings for PHP](https://devcenter.heroku.com/articles/custom-php-settings)