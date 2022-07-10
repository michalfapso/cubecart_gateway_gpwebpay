# Setup instructions

1. Copy this extension's folder to your CubeCart's `modules/gateway/GPWebpay` folder.
2. Go to your GP webpay account
   - testing: https://test.portal.gpwebpay.com/portal/anon/login.xhtml
   - production: https://portal.gpwebpay.com/portal/anon/login.xhtml
3. Open the "Key Management", your shop is listed at the bottom of the page.
   Click on it so see more info and check if both keys are green.
   If they aren't, click on the "CREATE" button to create a new private key,
   Add it to "THE USER'S BROWSER", choose to permanently store the key (not temporarily).
4. Generate a public key for your private key. When the private key is stored in
   `gpwebpay-private.key`, this command creates its public key in `gpwebpay-public.key`.
   ```
   openssl rsa -in gpwebpay-private.key -pubout > gpwebpay-public.key
   ```
5. Put both private and public key files into this module's `keys` subfolder.
   There is also `gpe.signing_test.pem` file downloaded from the
   *GP webpay portal -> Downloads -> GPE test public key ("GPE_test_public_key.zip" file)*
6. Open your `CubeCart's admin -> Extensions -> Manage Extensions`, enable the "GPWebpay" extension
   and fill out the settings form.

# Troubleshooting

The current list of all `prcode` and `srcode` errors can be found in the "Download" section of the GP
webpay Portal:
- testing: https://test.portal.gpwebpay.com/portal/anon/login.xhtml
- production: https://portal.gpwebpay.com/portal/anon/login.xhtml
in the document "GP webpay - List of return codes".

In case you're getting an error, try to reproduce it in the "demoshop_code" project which is available
in "the GP webpay portal -> Downloads -> Application - Demo e-shop PHP".
To run it, you need to set `short_open_tag = On` in your `php.ini`:

