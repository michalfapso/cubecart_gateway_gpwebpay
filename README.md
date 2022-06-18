```
# Generate a public key from the private key:
openssl rsa -in gpwebpay-private.key -pubout > gpwebpay-public.key
```

php.ini:
short_open_tag = On # Required for GP demo shop
