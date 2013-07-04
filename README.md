## Requirements

* PHP 5.4+
* the Phalcon extension
* Composer
* sqlite3 support or some RDBMS (MySQL, PgSQL, Oracle, ...)
* 1 minute of free time

## Installation

1. SSH into your server
2. Use `git clone https://github.com/philippgerard/kolibri.git` to clone the repository to a desitination of your choice
3. If you intend to use Sqlite: set `chmod -r 777` to the `data` folder.
4. Rename `application/config/config.php.dist` to `application/config/config.php` and provide the required database information.
5. Use `sql/[your RDBMS].sql` to set up the base table structure.
6. Point your webserver to the `public` directory and make sure the rewrite-definition inside the `.htaccess` is used. Also make sure that only the `public` directory is world-accessible.
7. Run `composer.phar install` in the main directory of Kolibri to download the required third-party-libraries to the vendor directory. If you plan to develop Kolibri, you might want to download the dev libraries as well, use `composer.phar update` to do so.
8. Enjoy!

## License

Copyright (c) 2013 Philipp Gérard

MIT License

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.