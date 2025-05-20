# OAW

# RSS Reader
## Authors
1. Patricio Peña
2. Wilbert Manzur
3. Martin Cuevas
4. Diego Burgos

## 📌 Project Setup

### 1️⃣ Clone the Repository
Make sure you have **XAMPP** installed. Then, open your terminal or Git Bash and navigate to your XAMPP `htdocs` directory:

```sh
cd /Applications/XAMPP/xamppfiles/htdocs  # macOS/Linux
cd C:\xampp\htdocs  # Windows
```

Now, **clone this repository** inside `htdocs`:

```sh
git clone https://github.com/your-username/rss-reader.git
```

### 2️⃣ Start XAMPP
- Open **XAMPP Control Panel**.
- Start **Apache** and **MySQL**.

### 3️⃣ Create the Database
1. Open your browser and go to:  
   ```
   http://localhost/phpmyadmin/
   ```
2. Click on **Databases** (top menu).
3. In the **"Create database"** field, enter:  
   ```
   rss_reader
   ```
4. Click **Create**.

### 4️⃣ Import the Database Schema
1. Click on the **"rss_reader"** database.
2. Go to the **SQL** tab.
3. Copy and paste the following SQL commands:
   ```sql
   CREATE TABLE news (
     id INT AUTO_INCREMENT PRIMARY KEY,
     title VARCHAR(255),
     url TEXT,
     description TEXT,
     category VARCHAR(100),
     pub_date DATETIME,
     source_url VARCHAR(255),
     image_url TEXT 
   );
   ```
4. Click **Go**.

### ✅ Done!  
Now your database is ready. You can proceed with running the project. 🚀 

### Note
Only XML File Items with images attached are going to be loaded, if the XML do not have images, an Error alert will display

# XAMPP build system

The XAMPP build system is a group of Tcl classes and procedures that allow us to automate the generation of XAMPP installers, by taking care of building, configuring and preparing the distribution of the bundled programs.

The build system lays in the `src` folder of the repository. Most of the Tcl files that are there only contain program classes. Under the `apps` and `base` directories, you will find the XML files describing the [InstallBuilder](https://installbuilder.com) logic steps.

## Requirements

- [Docker](https://www.docker.com/products/docker-desktop/). You can check the resources into the `Dockerfile` if you want to run the same steps in a different host.
- Thirdparty source code tarballs. You can get those tarballs from the official websites. For simplicity you can also download from [Sourceforge](https://sourceforge.net/projects/xampp/files/thirdparties/).

## TL;DR

```
$ docker build . -t xampp-build
$ docker run -v `pwd`/../xampp-code:/home/xampp-code -v `pwd`/tarballs:/tmp/tarballs -it xampp-build bash
```

## How to compile the code from source for Linux and OS X

For Unix platforms (Linux and OS X), before creating the installers, the process relays on a tarball including all the required components already compiled. The XAMPP build system is also able to compile those components for you.

You can build the XAMPP base tarballs from the `src` directory. You can get the source code for any of the components from the official website. For simplicity you can get them together from [Sourceforge](https://sourceforge.net/projects/xampp/files/thirdparties/).

Once the needed files are located into a `tarballs` directory and mounted into the container at `/tmp/tarballs`, your can run the commands below to create the desired installer depending on the platform. You can use the container to compile the binaries for Linux x64 but you will need access to an OS X higher than 10.6 to compile the binaries from OS X there.

```
tclkit createstack.tcl buildTarball xamppunixinstaller80stack linux-x64
tclkit createstack.tcl buildTarball xamppunixinstaller80stack osx-x64
```

> NOTE: you can build other PHP versions (7.4.x, 8.0.x, or 8.1.x) replacing `80` with `74` or `81`.

Once the tarball is compressed, you can move it to the `/tmp/tarball` mounted directory to use it in the next step.

## How to build the XAMPP installers

You can build the XAMPP installers from the `src` directory. The Linux and OS X platforms will require a tarball with all the binaries compiled from the previous step.

Once the needed files are located into a `tarballs` directory and mounted into the container at `/tmp/tarballs`, your can run the commands below to create the desired installer depending on the platform.

```
tclkit createstack.tcl pack xamppunixinstaller80stack linux-x64
tclkit createstack.tcl pack xamppunixinstaller80stack osx-x64
tclkit createstack.tcl pack xamppinstaller80stack windows-x64
```

> NOTE: you can pack other PHP versions (7.4.x, 8.0.x, or 8.1.x) replacing `80` with `74` or `81`.

The installers will be accessible at `/opt/installbuilder/output/`.

## License


Copyright &copy; 2022 Apache Friends

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.



SimplePie
=========

SimplePie is a very fast and easy-to-use class, written in PHP, that puts the
'simple' back into 'really simple syndication'.  Flexible enough to suit
beginners and veterans alike, SimplePie is focused on [speed, ease of use,
compatibility and standards compliance][what_is].

[what_is]: http://simplepie.org/wiki/faq/what_is_simplepie


Requirements
------------
* PHP 7.2+ (Required since SimplePie 1.8.0)
* libxml2 (certain 2.7.x releases are too buggy for words, and will crash)
* One of iconv, mbstring or intl extensions
* Optionally, intl extension, [symfony/polyfill-intl-idn](https://github.com/symfony/polyfill-intl-idn) or cURL extension built with IDN support to support IDNs
* cURL or fsockopen()
* PCRE support

PSR-18: HTTP Client support
--------------

Since SimplePie 1.9.0 you can use a [PSR-18](https://www.php-fig.org/psr/psr-18/) HTTP client like [Guzzle](https://guzzlephp.org)
or [every other implementation](https://packagist.org/providers/psr/http-client-implementation).
Please note that you would also need [PSR-17](https://www.php-fig.org/psr/psr-17/) implementations of `RequestFactoryInterface` and an `UriFactoryInterface` implementation.

```php
$simplepie = new \SimplePie\SimplePie();
$simplepie->set_http_client(
    new \GuzzleHttp\Client(),
    new \GuzzleHttp\Psr7\HttpFactory(),
    new \GuzzleHttp\Psr7\HttpFactory(),
);
```

PSR-16: Caching support
--------------

Since SimplePie 1.8.0 you can use the [PSR-16](https://www.php-fig.org/psr/psr-16/) cache from
[Symfony](https://symfony.com/doc/current/components/cache.html)
or [every other implementation](https://packagist.org/providers/psr/simple-cache-implementation).

```php
$simplepie = new \SimplePie\SimplePie();
$simplepie->set_cache(
    new \Symfony\Component\Cache\Psr16Cache(
        new \Symfony\Component\Cache\Adapter\FilesystemAdapter()
    ),
);
```

What comes in the package?
--------------------------
1. `src/` - SimplePie classes for use with the autoloader
2. `autoloader.php` - The SimplePie Autoloader if you want to use the separate
   file version.
3. `README.markdown` - This document.
4. `LICENSES/BSD-3-Clause.txt` - A copy of the BSD license.
5. `compatibility_test/` - The SimplePie compatibility test that checks your
   server for required settings.
6. `demo/` - A basic feed reader demo that shows off some of SimplePie's more
   noticeable features.
7. `build/` - Scripts related to generating pieces of SimplePie
8. `test/` - SimplePie's unit test suite.

### Where's `simplepie.inc`?
Since SimplePie 1.3, we've split the classes into separate files to make it easier
to maintain and use.

If you'd like a single monolithic file, see the assets in the
[releases](https://github.com/simplepie/simplepie/releases), or you can
run `php build/compile.php` to generate `SimplePie.compiled.php` yourself.

To start the demo
-----------------
1. Upload this package to your webserver.
2. Make sure that the cache folder inside of the demo folder is server-writable.
3. Navigate your browser to the demo folder.


Need support?
-------------
For further setup and install documentation, function references, etc., visit
[the wiki][wiki]. If you're using the latest version off GitHub, you can also
check out the [API documentation][].

If you can't find an answer to your question in the documentation, head on over
to one of our [support channels][]. For bug reports and feature requests, visit
the [issue tracker][].

[API documentation]: http://dev.simplepie.org/api/
[wiki]: http://simplepie.org/wiki/
[support channels]: http://simplepie.org/support/
[issue tracker]: http://github.com/simplepie/simplepie/issues


Project status
--------------
SimplePie is currently maintained by Malcolm Blaney.

As an open source project, SimplePie is maintained on a somewhat sporadic basis.
This means that feature requests may not be fulfilled straight away, as time has
to be prioritized.

If you'd like to contribute to SimplePie, the best way to get started is to fork
the project on GitHub and send pull requests for patches. When doing so, please
be aware of our [coding standards](http://simplepie.org/wiki/misc/coding_standards).

The main development for the next minor release happens in `master` branch.
Please create your pull requests primarily against this branch.

We do not actively provide bug fixes or security fixes for older versions. Nevertheless,
you are welcome to create backport PRs if you still need support for older PHP versions.
Please open your PR against the appropriate branch.

| branch                                                                     | requires    |
|----------------------------------------------------------------------------|-------------|
| [master](https://github.com/simplepie/simplepie/tree/master)               | PHP 7.2.0+  |
| [one-dot-seven](https://github.com/simplepie/simplepie/tree/one-dot-seven) | PHP 5.6.0+  |
| [one-dot-three](https://github.com/simplepie/simplepie/tree/one-dot-three) | PHP 5.2.0+  |


Authors and contributors (SimplePie)
------------------------
### Current
* [Malcolm Blaney][] (Maintainer, support)

### Alumni
* [Ryan McCue][] (developer, support)
* [Ryan Parman][] (Creator, developer, evangelism, support)
* [Sam Sneddon][] (Lead developer)
* [Michael Shipley][] (Submitter of patches, support)
* [Steve Minutillo][] (Submitter of patches)

[Malcolm Blaney]: https://mblaney.xyz
[Ryan McCue]: http://ryanmccue.info
[Ryan Parman]: http://ryanparman.com
[Sam Sneddon]: https://gsnedders.com
[Michael Shipley]: http://michaelpshipley.com
[Steve Minutillo]: http://minutillo.com/steve/


### Contributors
For a complete list of contributors:

1. Pull down the latest SimplePie code
2. In the `simplepie` directory, run `git shortlog -ns`

License
-------
[New BSD license](http://www.opensource.org/licenses/BSD-3-Clause)
