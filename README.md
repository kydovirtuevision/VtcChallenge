# Welcome to VTC Challenge

This challenge is created to test your knowledge of creating Symfony Applications with SPA Frontend build using ReactJS.
In order to start working your solution, please clone this repository and create a copy under your namespace. 
Consider also to change name of your project to make it harder to find your solution by other competitors.

**DO NOT FORK THIS REPOSITORY, THIS WILL ALLOW OTHERS TO COPY YOUR SOLUTION**

## Challenge

Based on this bare-bones application create a service that will allow:
* registration of a user
* confirming account by clicking a link from email. (no need to send actual email, email can be persisted as a file in var/emails directory)
* users to login
* users to create notes
* each note should have fields
  * title
  * content
  * category
  * status (new, todo, done)
* list of notes should have possibility to search notes by text from title/content, a select list for statuses and another select list for categories.

To finish this challenge you have 24h since the moment of receiving this email. Remember to push your last changes before the end of the deadline.
We will be evaluating your solution based on the time of the commit. And remember to send us back link to your solution on GitHub.

Good look.

## Help notes

### Requirements
To run this project you will need:
* Docker: >24
* Docker Compose: >1.29
* NodeJS: >18
* PHP: >7.4

### First steps:

    $ cp .env.dist .env
    $ composer install
    $ yarn install
    $ docker-compose up -d
    $ yarn watch

After running this set of commands, without errors; you should be able to open `http://localhost:81/` and see `Hello World!!!` in the middle of the page.

## Quick usage (API)

Register a user (creates a file in `var/emails` with confirmation token):

```bash
curl -X POST -H "Content-Type: application/json" -d '{"email":"me@example.com","password":"pass"}' http://localhost:81/api/auth/register
```

Confirm account using token from `var/emails/<token>.txt`:

```bash
curl http://localhost:81/api/auth/confirm/TOKEN
```

Login to receive an API token:

```bash
curl -X POST -H "Content-Type: application/json" -d '{"email":"me@example.com","password":"pass"}' http://localhost:81/api/auth/login
```

Use the returned `api_token` to call notes endpoints (Bearer token):

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:81/api/notes/
```
Note about running tests in this environment:

 - I attempted to run the project's `bin/phpunit` wrapper, but the runtime here is missing a compatible PHP/OpenSSL binary and PHPUnit could not be executed. You can run tests locally after `composer install` with:

```bash
composer install
./bin/phpunit
```

Database schema helper:

 - A quick SQL schema is provided at `scripts/create_schema.sql` which you can apply to your database to create the `user` and `note` tables if not using Doctrine migrations.

Frontend: open the app and use the Register / Login pages. After login the `Notes` page uses the API token stored in `localStorage`.