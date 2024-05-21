# Install instructions

## PDF generation and manipulation requirements

### Install Browsershot

### Install necessary php extensions

these extensions are necessary for the proper functioning of the application :

- Imagick
- Gd
- Zip
- Bcmath
- Mbstring

### Generate a private key

Generate the private key without a passphrase:

```sh
openssl genpkey -algorithm RSA -out storage/keys/private_key.pem
Generate the corresponding public key:
openssl rsa -pubout -in storage/keys/private_key.pem -out storage/keys/public_key.pem
```

Secure the file permissions:

```sh
chmod 600 storage/keys/private_key.pem
chmod 644 storage/keys/public_key.pem
```

Update the .env file Add the paths of the generated keys to your .env file:

```sh
PRIVATE_KEY_PATH=storage/keys/private_key.pem
PUBLIC_KEY_PATH=storage/keys/public_key.pem
SALT=your_random_salt
```
