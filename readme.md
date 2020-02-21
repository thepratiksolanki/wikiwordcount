# Wiki Word Count Frequency

This is an API built with the help of laravel in PHP language. 
It fetches wiki page data when provided with a page-id and lists top 5 words comma separated with their frequencies.
The output is provided in both json(default) and yaml formats.
**Stop words** from this link **https://gist.github.com/sebleier/554280** have been used.
These stop words are escaped so that more relevant top 5 words with their frequencies are displayed.

All the steps to install and run the project are given below.

The only pre-requisite is **Docker**.

Steps to install docker according to your OS can be found in **https://docs.docker.com/install/**

Once docker is up and running in your system, follow the below steps :

1. Clone the github repo locally
```sh
git clone https://github.com/thepratiksolanki/wikiwordcount.git wiki
````
2. cd into your project
```sh
cd wiki
```
3. Run the docker containers in detached mode with the below command
```sh
docker-compose up -d
```
4. Execute php bash to later install dependencies. This command will enter into the container with root user
```sh
docker-compose exec php-fpm bash
```
5. Install all the dependencies with the help of composer
```sh
composer install
```
6. Create a copy of .env file
```sh
cp .env.example .env
```
7. Generate an app encryption key
```sh
php artisan key:generate
```
8. To run the tests, run the below command
```sh
./vendor/bin/phpunit
```
9. Visit **http://localhost:8081/wiki/21721040** This follows http://localhost:8081/wiki/<page-id> . The page-id can be changed according to the page data required. This will give output in JSON format.

10. If output is expected in YAML format then visit **http://localhost:8081/wiki/21721040/yaml** This follows http://localhost:8081/wiki/<page-id>/yaml . Again, the page-id can be changed according to the requirements.
11. For command-line wrappers please use the link in step 9
12. The above 2 links can also be used with Postman, CURL or other API client tools to access the data
13. The main code logic is written in wiki/app/Http/Controllers/WikiController.php
14. The unit tests are written in wiki/tests/Feature/WikiTest.php