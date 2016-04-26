## php-social-rss ##
Read your social networks timeline as an RSS-feed. Currently php-social-rss supports Twitter and VK.

### Requirements ###
* PHP >= 5.6
* Composer

### Installation ###
```bash
$ git clone https://github.com/andr-andreev/php-social-rss.git
$ cd php-social-rss
$ composer install
```

### Configuration ###
Copy `.env.example` to `.env` and configure it as seen below.
```bash
$ cp .env.example .env
```

#### Twitter ####
1. Create an application: https://apps.twitter.com/app/new
2. Go to `Keys and Access Tokens` tab. Press `Create my access token` button at the bottom of the page
3. Save `Consumer Key`, `Consumer Secret`, `Access Token` and `Access Token Secret` to `.env`

#### VK ####
1. Create an standalone application: https://vk.com/editapp?act=create
2. Go to `Settings` tab
3. Save `Application ID` and `Secure key` to `.env`
4. Open https://oauth.vk.com/authorize?client_id={APP_ID}&scope=wall,friends,offline&redirect_uri=http://oauth.vk.com/blank.html&response_type=code and allow access to your account. You will be redirected to https://oauth.vk.com/blank.html#code={CODE}
5. Open https://oauth.vk.com/access_token?client_id={APP_ID}&client_secret={API_SECRET}&code={CODE}&redirect_uri=http://oauth.vk.com/blank.html. Save `access_token` to `.env`

### Usage ###
Twitter feed:
```
http://example.com/index.php?source=twitter
```
VK feed:
```
http://example.com/index.php?source=vk
```

### Tests ###
You can run the test suite:
```bash
$ ./vendor/bin/phpunit
```
