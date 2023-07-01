# api.papers

api.papers is a project created as a document for cookie-based session authentication in Laravel. It is intended to be used as a backend Web API.

## Requirements for development environment

- PHP >= 8.1
- Composer >= 2.2.0
- SQLite 3
- [Mailpit](https://github.com/axllent/mailpit)

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
php artisan test
```

## Related repositories

- [jamband/papers-next](https://github.com/jamband/papers-next) with Next.js
- [jamband/papers](https://github.com/jamband/papers)

## License

api.papers is licensed under the MIT license.
