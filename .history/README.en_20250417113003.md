# Website Navigation System (HuihuiResourceNav)

[![License](https://img.shields.io/badge/License-MulanPSL2-blue.svg)](LICENSE)

## Introduction
This is a PHP and MySQL based website navigation system that helps users manage and organize their bookmarked website links. The system supports category management, searching, and bookmarks import/export features.

## Features
- User registration and login system
- Category-based bookmark management
- Search functionality
- Bookmark import and export
- Responsive interface design

## Requirements
- PHP 7.0+
- MySQL 5.6+
- Web server (Apache/Nginx)

## Installation
1. Clone or download this repository to your web server
2. Create a MySQL database and import the `update_database.sql` file
3. Modify the database connection information in the `config.php` file
4. Access your website domain, register and start using

## Configuration
Configure database connection in the `config.php` file:
```php
define('DB_HOST', 'your_database_host');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_NAME', 'your_database_name');
```

## Usage
1. After logging in, you can add and manage your bookmarks
2. Use categories to organize your bookmarks
3. Find your bookmarks quickly using the search function
4. Import bookmarks from your browser or export to your browser

## Contribution
1. Fork this repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Create a Pull Request

## License
This project is licensed under the [Mulan Permissive Software License, Version 2](LICENSE).

## Contact
If you have any questions or suggestions, please submit an Issue or Pull Request.

#### Gitee Feature

1.  You can use Readme\_XXX.md to support different languages, such as Readme\_en.md, Readme\_zh.md
2.  Gitee blog [blog.gitee.com](https://blog.gitee.com)
3.  Explore open source project [https://gitee.com/explore](https://gitee.com/explore)
4.  The most valuable open source project [GVP](https://gitee.com/gvp)
5.  The manual of Gitee [https://gitee.com/help](https://gitee.com/help)
6.  The most popular members  [https://gitee.com/gitee-stars/](https://gitee.com/gitee-stars/)
