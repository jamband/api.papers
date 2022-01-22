# api.papers

api.papers is a project created as a document for cookie-based session authentication in Laravel. It is intended to be used as a backend Web API.

## Requirements for development environment

- PHP >= 8.0
- Composer 2.x
- Laravel 8.x
- SQLite 3
- [MailHog](https://github.com/mailhog/MailHog)

## Install on local

```
cd path/to/somewhere
git clone https://github.com/jamband/api.papers.git
cd api.papers
composer run dev
```

## Actions

General user:

- User register
- Email verification notification
- User login
- User logout
- Forgot password
- Reset password
- Confirm password
- Delete account

Admin user:

- Admin user login
- Admin user logout
- Delete user

## Testing

Unit tests and feature tests:

```
php artisan test -p
```

## Related repositories

- [jamband/papers-next](https://github.com/jamband/papers-next) with Next.js
- [jamband/papers](https://github.com/jamband/papers)

## License

api.papers is licensed under the MIT license.
