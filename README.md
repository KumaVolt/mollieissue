Example where pricing is incorrect due to ommited numbers

## requirements
- ngrok
- mollie account


## testing 

```
php artisan migrate --seed

php artisan serve

nrgok http localhost:8000
```

navigate to /checkout through ngrok
