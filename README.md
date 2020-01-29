<p align="center">
    <img src="https://raw.githubusercontent.com/dperkosan/files/master/syliusPart.jpg" />
</p>

<h1 align="center">Headless E-shop</h1>

For Headless E-shop part is used [**Sylius Standard Edition**](https://sylius.com) + [**Shop API Plugin**](https://github.com/Sylius/ShopApiPlugin). Since I see the "Shop API Plugin" as a main part of the system that will be quite different from the original version (in order to accommodate both client and "Vue storefront API" requirements), I moved it from the vendor folder to a project and made it a separate plugin that can be directly modified. (`src/Webedia/ShopApiPlugin`)

System Requirements
-----

Sylius is the first decoupled eCommerce platform based on [**Symfony**](http://symfony.com) and [**Doctrine**](http://doctrine-project.org). 
The highest quality of code, strong testing culture, built-in Agile (BDD) workflow and exceptional flexibility make it the best solution for application tailored to your business requirements. 
Enjoy being an eCommerce Developer again!

Powerful REST API allows for easy integrations and creating unique customer experience on any device.

We're using full-stack Behavior-Driven-Development, with [phpspec](http://phpspec.net) and [Behat](http://behat.org)

Installation
------------

```bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar create-project sylius/sylius-standard project
$ cd project
$ yarn install
$ yarn build
$ php bin/console sylius:install
$ php bin/console server:start
$ open http://localhost:8000/
```

Tasks
---------------

- [x] Finish my changes
- [ ] Push my commits to GitHub
- [ ] Open a pull request