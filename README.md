# Next Actions for Trello

[![Build Status](https://scrutinizer-ci.com/g/stevecshanks/next-actions-for-trello/badges/build.png?b=main)](https://scrutinizer-ci.com/g/stevecshanks/next-actions-for-trello/build-status/main)
[![Code Coverage](https://scrutinizer-ci.com/g/stevecshanks/next-actions-for-trello/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/stevecshanks/next-actions-for-trello/?branch=main)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/535d22174a604690813e804ced26645e)](https://www.codacy.com/app/stevecshanks/next-actions-for-trello?utm_source=github.com&utm_medium=referral&utm_content=stevecshanks/next-actions-for-trello&utm_campaign=Badge_Grade)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/stevecshanks/next-actions-for-trello/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/stevecshanks/next-actions-for-trello/?branch=main)

## What is it?

It brings together:

- all of your smaller todo items that don't merit their own board
- every card you are a member of
- the first card in each project's "Todo" list

## How do I set it up?

_Note: While I use this app pretty much every day, it's still a work in progress, so the steps below are a bit on the complex side_

Firstly you'll need to set up a couple of Trello lists - one for small tasks that don't require a project board (I call this "Todo") and one to list your projects (I call this one "Projects").

![Example board](https://user-images.githubusercontent.com/24637079/42135236-fb1467aa-7d3f-11e8-9774-fb8784747f29.png)

Each card in the Projects list should have the URL of the project board as its description.

Your individual project boards should each have a list called "Todo" - the card at the top of each list is considered its Next Action.

![Example project](https://user-images.githubusercontent.com/24637079/42135239-054e0ff0-7d40-11e8-9f89-d15edd5577c0.png)

Then you'll need to run through the following steps to set up the app:

1. `git clone` this repository somewhere
1. Make sure you have PHP 7.1 (at least) and Composer installed
1. `cd` to the repository you cloned and run `composer install`
1. You'll obviously need a Trello board with at least two lists: one containing links to your project boards, and one containing a list of smaller tasks that don't merit their own board
1. Next you'll need to get some information from Trello:
   1. Get your Application Key at https://trello.com/app-key
   1. Change {YourAPIKey} to the key above and browse to https://trello.com/1/authorize?expiration=never&name=Next%20Actions%20For%20Trello&scope=read&response_type=token&key={YourAPIKey}
   1. You'll need the IDs of the two lists on your board - the easiest way to get it is to add `.json` after the board identifier in the URL e.g. `https://trello.com/b/abcd1234.json`. Just search for the name of each list and you'll see the ID nearby.
1. You'll need to put these details somewhere the app can read then - either a `.env` file (see `.env.dist` for an example) or by exporting them as environment variables (again, see `.env.dist` for details of what you'd need to export)

## How do I use it?

```
make run
```

The application should then be available on localhost, with the port defaulting to 8000 e.g. [http://localhost:8000/](http://localhost:8000/)

![Example](https://user-images.githubusercontent.com/24637079/42135240-08bda61e-7d40-11e8-94a1-9c414d5c0cca.png)

## How do I run the tests?

If you want to run the tests, just type:

```
make test
```
