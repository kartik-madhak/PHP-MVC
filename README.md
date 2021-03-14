# php.mvc.basic
Very basic MVC framework for PHP (version 7.4), MYSQL. 

---

NOTE
----
- Not Tested AT ALL. Definitely contains many bugs.
- Not recommended for production level code (because of above).
- Add a `.env` file according to the `.env.example` format.
- Changing the contents of `index.php` (in the root folder) is NOT RECOMMENDED.
- Run `composer require` to add the required dependencies.
- Documentation is missing, will add later. I am assuming you know little bit of MVC. This is basically Laravel with a lot less features, and lot more bugs!
- Please contribute to this code!

How to use
----------

- Clone this repo into your workspace and add your models into `models` directory, views in `views` and controllers in `controllers` directory.
- There are some examples already inside the `models`, `views`, and `controllers` directory on how to use them.
- Point a virtual host in the directory where you have cloned the project and open it to run.

Features
----------

- Basic Router that supports variables inside url.
- Models extend abstract class `Lib\database\Model` which contains an ORM for MYSQL (called FluentDB) for very easy database operations.
- Dynamic views using php.

