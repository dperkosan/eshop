<p align="center">
    <img src="https://raw.githubusercontent.com/dperkosan/files/master/syliusPart.jpg" />
</p>

<h1 align="center">Headless E-shop</h1>

For Headless E-shop part is used [**Sylius Standard Edition**](https://sylius.com) + [**Shop API Plugin**](https://github.com/Sylius/ShopApiPlugin). Since I see the "Shop API Plugin" as a main part of the system that will be quite different from the original version (in order to accommodate both client and "Vue storefront API" requirements), I moved it from the vendor folder to a project and made it a separate plugin that can be directly modified. (`src/Webedia/ShopApiPlugin`)

System Requirements
-----

* [Requirements for running Symfony](http://symfony.com/doc/current/reference/requirements.html)
* The recommended operating systems for running Sylius are the Unix systems - Linux, MacOS
* In the production environment recommendation is Apache web server ≥ 2.2, while developing the recommended way to work is to use PHP’s built-in web server
* PHP version: ^7.2
* PHP extensions: `gd`, `exif`, `fileinfo`, `intl`
* PHP configuration settings: `memory_limit ≥ 1024M`, `date.timezone` Use your local timezone
* MySQL	5.7+, 8.0+

Installation
------------

```bash
$ git clone git@github.com:dperkosan/eshop.git
$ cd eshop
```

Please, contact me for:
* DB
* media folder
* jwt folder
* .env.local file

```bash
$ composer install
$ yarn install
$ yarn build
$ symfony serve
$ open http://localhost:8000/
```

Tasks
---------------

- [x] Move "Shop Api Plugin" into project
- [ ] Disable front shop
- [ ] "Shop Api Plugin" customization for "Vue storefront API"
- [ ] Creating platform for "Sylius" in "Vue storefront API"
- [ ] Creating of "Data pump" in "Sylius" that will copy data from MySql into Elasticsearch
- [ ] Synchronisation between MySql and Elasticsearch (on every create, insert, update and delete)
- [ ] Custom functionalities regarding ongoing project