# MyProduct 

Open-source product management tool. Collect customer feedback, prioritize new features and share plans with your users!

Try the app on https://myproduct.janzabloudil.cz/!

## Features

- Register, Login, Forgot Password, Account management
- Feedback collection
- Features planning and priorization 
- Sharing features with your users

See [app screenshots](#screenshots-from-the-app).

## Tech stack & Notes on app architecture

See  [documentation](https://github.com/jz-myproduct/app/wiki).

## Installation

Clone the repository and run the following commands to install all the dependencies.

```
git clone https://github.com/jz-myproduct/app.git myproduct
cd myproduct
composer self-update
composer install
```

## Database setup

First setup database connection in .ENV file. You can use MySQL database system. 

Then run following commands.

```
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate -n -q
```

## Other installation instructions

Set up APP_SECRET in .ENV file

## Testing data

To access all app features you need to load data to entities:
- FeatureState
- PortalFeatureState
- InsightWeight

You can easily managed that by running

```
php bin/console doctrine:fixtures:load --group=FeatureStateFixtures --group=PortalFeatureStateFixtures --group=InsightWeightFixtures
```

If you want to load all testing data, run just

```
bin/console doctrine:fixtures:load
```

## Tests

Basic application controller tests are implemented.

If you want to run tests, load fixtures first.

```
bin/console doctrine:fixtures:load
```

Then run tests by following command

```
 php ./vendor/bin/phpunit tests
```

## Contribution

Any contribution is appreciated. See issues section and read our [CONTRIBUTING](https://github.com/jz-myproduct/app/blob/master/CONTRIBUTING.md) file.

## MIT License

MyProduct is completely free and released under the [MIT License](https://github.com/jz-myproduct/app/blob/master/LICENSE).

## Contact 

If you have any problems using the application, please open a Github issue. The same applies to any questions or feature requests.

You can also use myproduct@janzabloudil.cz.

## Screenshots from the app


Feedback detail

<img src="https://www.janzabloudil.cz/myproduct-screens-eng/feedback-detail-improved.png" width="350">

Feedback list

<img src="https://www.janzabloudil.cz/myproduct-screens-eng/feedback-list.png" width="350">

Roadmap

<img src="https://www.janzabloudil.cz/myproduct-screens-eng/roadmap.png" width="350"> 
 
Feature detail

<img src="https://www.janzabloudil.cz/myproduct-screens-eng/feature-detail.png" width="350">

Portal
 
<img src="https://www.janzabloudil.cz/myproduct-screens-eng/portal.png" width="350">

Adding feedback through the Portal

<img src="https://www.janzabloudil.cz/myproduct-screens-eng/portal-feedback.png" width="400"> 





