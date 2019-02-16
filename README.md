## Social RSS
Read your social networks timeline in different formats (RSS, JSON).

Social RSS fully parses API data including avatars, quotes, photos, videos, hashtags, user mentions and urls.

You can read your own timeline as well as user's timelines.

[![Build Status](https://travis-ci.org/andr-andreev/social-rss.svg?branch=master)](https://travis-ci.org/andr-andreev/social-rss)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andr-andreev/social-rss/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/andr-andreev/social-rss/?branch=master)
[![Code Climate](https://codeclimate.com/github/andr-andreev/social-rss/badges/gpa.svg)](https://codeclimate.com/github/andr-andreev/social-rss)
[![Issue Count](https://codeclimate.com/github/andr-andreev/social-rss/badges/issue_count.svg)](https://codeclimate.com/github/andr-andreev/social-rss)
[![Test Coverage](https://codeclimate.com/github/andr-andreev/social-rss/badges/coverage.svg)](https://codeclimate.com/github/andr-andreev/social-rss/coverage)

### Supported networks / screenshots
#### Twitter ([source post](https://twitter.com/TwitterData/status/767372163431018496))
![Twitter](docs/screenshots/twitter.png?raw=true "Twitter")

#### VK ([source post](https://vk.com/wall-32295218_365558))
![VK](docs/screenshots/vk.png?raw=true "VK")

Screenshots made in [NewsBlur](https://newsblur.com/) RSS reader.

### Output formats
* RSS 2.0
* JSON

### Requirements
* PHP >= 7.2
* Composer

### Installation
```bash
$ git clone https://github.com/andr-andreev/social-rss.git
$ cd social-rss
$ make install
```

### Configuration
Copy `.env.example` to `.env` and configure it as seen below.
```bash
$ cp .env.example .env
```

#### Twitter
1. Create an application: https://apps.twitter.com/app/new
2. Go to `Keys and Access Tokens` tab. Press `Create my access token` button at the bottom of the page
3. Save `Consumer Key`, `Consumer Secret`, `Access Token` and `Access Token Secret` to `.env`

#### VK
1. Create an standalone application: https://vk.com/editapp?act=create
2. Go to `Settings` tab
3. Open https://oauth.vk.com/authorize?client_id={APP_ID}&scope=wall,friends,offline&redirect_uri=http://oauth.vk.com/blank.html&response_type=code and allow access to your account. You will be redirected to https://oauth.vk.com/blank.html#code={CODE}
4. Open https://oauth.vk.com/access_token?client_id={APP_ID}&client_secret={API_SECRET}&code={CODE}&redirect_uri=http://oauth.vk.com/blank.html. Save `access_token` to `.env`

### Usage ###
[Configure your webserver](https://www.slimframework.com/docs/start/web-servers.html), assuming `./web/` is public-accessible directory.

To view your own timeline in default (RSS) format:
```
http://example.com/feed/{source}
```
To view user timeline in default (RSS) format:
```
http://example.com/feed/{source}?username={username}
```
To specify another format:
```
http://example.com/feed/{source}?output={format}
```
where `{source}` is `twitter` or `vk` and `{format}` is `rss` or `json`

### Tests ###
You can run the test suite:
```bash
$ make test
```

### Disclaimer ###
Please note that using this script may be against social networks Terms of Service.
